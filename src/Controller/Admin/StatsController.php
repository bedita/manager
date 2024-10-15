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
use Cake\I18n\FrozenDate;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * Stats Controller
 */
class StatsController extends ModelBaseController
{
    /**
     * Resource type currently used
     *
     * @var string
     */
    protected $resourceType = 'object_types';

    /**
     * @inheritDoc
     */
    public function index(): ?Response
    {
        if ($this->getRequest()->getQuery('interval') !== null) {
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
        $interval = $this->getRequest()->getQuery('interval');
        $sub = $this->getRequest()->getQuery('sub');
        $year = $this->getRequest()->getQuery('year');
        $data = [];
        $intervals = $this->intervals($interval, $sub, $year);
        foreach ($intervals as $item) {
            $data[] = $this->fetchCount($objectType, $item['start'], $item['end'], $sub);
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
     * @param string|null $sub Subinterval
     * @return int
     */
    protected function fetchCount(string $objectType, string $from, string $to, string $sub): int
    {
        // if from is in the future, return 0
        if (new FrozenDate($from) > new FrozenDate('today')) {
            return 0;
        }
        $subval = $sub === '-' ? 'nosub' : $sub;
        $key = CacheTools::cacheKey(sprintf('stats-%s-%s-%s-%s', $objectType, $from, $to, $subval));
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
     * @param string $interval Interval name
     * @param string $subinterval Subinterval name
     * @param string|null $year Year
     * @return array
     */
    protected function intervals(string $interval, string $subinterval, ?string $year): array
    {
        if ($interval === 'week') {
            $exp = $subinterval === '-' ? 'last sunday' : sprintf('last %s', $subinterval);
            $start = new FrozenDate($exp);
            $exp = $subinterval === '-' ? 'today' : sprintf('last %s', $subinterval);
            $end = new FrozenDate($exp);
            $intervals = [];
            while ($start->lessThanOrEquals($end)) {
                $next = $start->addDays(1);
                $intervals[] = ['start' => $start->format('Y-m-d'), 'end' => $next->format('Y-m-d')];
                $start = $next;
            }

            return $intervals;
        }
        if ($interval === 'month') {
            $start = new FrozenDate('first day of this month');
            $exp = $subinterval === '-' ? 'last day of this month' : sprintf('last day of %s', $subinterval);
            $end = new FrozenDate($exp);
            $intervals = [];
            while ($start->lessThanOrEquals($end)) {
                $next = $start->addWeeks(1);
                $intervals[] = ['start' => $start->format('Y-m-d'), 'end' => $next->format('Y-m-d')];
                $start = $next;
            }

            return $intervals;
        }
        if ($interval === 'year') {
            if ($year === null) {
                $year = (new FrozenDate())->format('Y');
            }
            $start = new FrozenDate('first day of january ' . $year);
            $exp = $subinterval === '-' ? 'last day of december ' . $year : sprintf('last day of %s %s', $subinterval, $year);
            $end = new FrozenDate($exp);
            $intervals = [];
            while ($start->lessThanOrEquals($end)) {
                $next = $start->addMonths(1);
                $intervals[] = ['start' => $start->format('Y-m-d'), 'end' => $next->format('Y-m-d')];
                $start = $next;
            }

            return $intervals;
        }
    }
}
