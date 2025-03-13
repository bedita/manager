<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Controller\Admin;

use App\Controller\Model\ModelBaseController;
use App\Utility\CacheTools;
use BEdita\SDK\BEditaClientException;
use Cake\Cache\Cache;
use Cake\Http\Response;
use Cake\I18n\DateTime;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * Statistics Controller
 */
class StatisticsController extends ModelBaseController
{
    /**
     * Resource type currently used
     *
     * @var string|null
     */
    protected ?string $resourceType = 'object_types';

    /**
     * @inheritDoc
     */
    public function index(): ?Response
    {
        if ($this->getRequest()->getQuery('year') !== null) {
            return $this->fetch();
        }
        parent::index();
        $resources = $this->viewBuilder()->getVar('resources');
        $resources = (array)Hash::extract($resources, '{n}.attributes.name');
        $modules = $this->viewBuilder()->getVar('modules');
        $data = [];
        foreach ($resources as $objectType) {
            $color = $modules[$objectType]['color'] ?? '#123123';
            $label = $modules[$objectType]['label'] ?? Inflector::humanize($objectType);
            $shortLabel = $modules[$objectType]['shortLabel'] ?? substr($label, 0, 3);
            $data[$objectType] = [
                'color' => $color,
                'label' => $label,
                'shortLabel' => $shortLabel,
            ];
        }

        $this->set('resources', $data);

        return null;
    }

    /**
     * Fetch counts for a specific object type and interval
     *
     * @return \Cake\Http\Response|null
     */
    protected function fetch(): ?Response
    {
        $objectType = $this->getRequest()->getQuery('objectType');
        $year = $this->getRequest()->getQuery('year');
        $month = $this->getRequest()->getQuery('month');
        $week = $this->getRequest()->getQuery('week');
        $day = $this->getRequest()->getQuery('day');
        $params = compact('year', 'month', 'week', 'day');
        $data = [];
        $intervals = $this->intervals($params);
        foreach ($intervals as $item) {
            $data[] = $this->fetchCount($objectType, $item['start'], $item['end']);
        }
        $this->viewBuilder()->setClassName('Json');
        $this->set('data', $data);
        $this->setSerialize(['data']);

        return null;
    }

    /**
     * Fetch count for a specific object type and interval
     *
     * @param string $objectType Object type name
     * @param string $from Start date
     * @param string $to End date
     * @return int
     */
    protected function fetchCount(string $objectType, string $from, string $to): int
    {
        // if from is in the future, return 0
        if (new DateTime($from) > new DateTime('today')) {
            return 0;
        }
        $key = CacheTools::cacheKey(sprintf('statistics-%s-%s-%s', $objectType, $from, $to));
        try {
            $count = Cache::remember(
                $key,
                function () use ($objectType, $from, $to) {
                    $response = $this->apiClient->get(
                        '/objects',
                        [
                            'filter' => [
                                'type' => $objectType,
                                'created' => [
                                    'gte' => $from,
                                    'lte' => $to,
                                ],
                            ],
                            'page' => 1,
                            'page_size' => 1,
                        ]
                    );

                    return (int)Hash::get($response, 'meta.pagination.count', 0);
                }
            );
        } catch (BEditaClientException $e) {
            $count = 0;
        }

        return $count;
    }

    /**
     * Get intervals for a specific interval
     *
     * @param array $params Interval parameters
     * @return array
     */
    protected function intervals(array $params): array
    {
        $year = Hash::get($params, 'year');
        $month = Hash::get($params, 'month');
        $week = Hash::get($params, 'week');
        $day = Hash::get($params, 'day');
        // case day: return interval with just one day
        if ($day !== null) {
            $start = new DateTime($day);
            $end = $start->addDays(1);

            return [['start' => $start->format('Y-m-d'), 'end' => $end->format('Y-m-d')]];
        }
        // case week: return interval with all days of the week, ~ 7 days
        if ($week !== null) {
            $firstWeek = intval($week);
            $lastWeek = intval($week);
            $start = new DateTime(sprintf('first day of %s', $month));
            $start = $start->addWeeks($firstWeek - 1);
            $end = $start->addWeeks(1)->subDays(1);
            $intervals = [];
            while ($start->lessThanOrEquals($end)) {
                $next = $start->addDays(1);
                $intervals[] = ['start' => $start->format('Y-m-d'), 'end' => $next->format('Y-m-d')];
                $start = $next;
            }

            return $intervals;
        }
        // case month: return interval with 4/5 weeks
        if ($month !== null) {
            $firstWeek = 1;
            $defaultLastWeek = $month === 'february' ? 4 : 5;
            $lastWeek = $defaultLastWeek;
            $start = new DateTime(sprintf('first day of %s %s', $month, $year));
            $start = $start->addWeeks($firstWeek - 1);
            $end = $start->addWeeks($lastWeek)->subDays(1);
            $intervals = [];
            while ($start->lessThanOrEquals($end)) {
                $next = $start->addWeeks(1);
                if ($next->format('m') !== $start->format('m')) {
                    $end = new DateTime(sprintf('last day of %s %s', $month, $year));
                    $next = $end->addDays(1);
                }
                $intervals[] = ['start' => $start->format('Y-m-d'), 'end' => $next->format('Y-m-d')];
                $start = $next;
            }

            return $intervals;
        }
        // case year: return interval with 12 months
        $start = new DateTime(sprintf('first day of january %s', $year));
        $end = new DateTime(sprintf('last day of december %s', $year));
        $intervals = [];
        while ($start->lessThanOrEquals($end)) {
            $next = $start->addMonths(1);
            $intervals[] = ['start' => $start->format('Y-m-d'), 'end' => $next->format('Y-m-d')];
            $start = $next;
        }

        return $intervals;
    }
}
