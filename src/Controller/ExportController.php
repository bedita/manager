<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Utility\Hash;

/**
 * Export controller: upload and load using filters
 *
 * @property \App\Controller\Component\ExportComponent $Export
 */
class ExportController extends AppController
{
    /**
     * Default max number of exported items
     *
     * @var int
     */
    public const DEFAULT_EXPORT_LIMIT = 10000;

    /**
     * Default page size
     *
     * @var int
     */
    public const DEFAULT_PAGE_SIZE = 500;

    /**
     * {@inheritDoc}
     * {@codeCoverageIgnore}
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Export');
        $this->Security->setConfig('unlockedActions', ['related']);
    }

    /**
     * Export data to format specified by user
     *
     * @return \Cake\Http\Response|null
     */
    public function export(): ?Response
    {
        // check request (allowed methods and required parameters)
        $data = $this->checkRequest([
            'allowedMethods' => ['post'],
            'requiredParameters' => ['objectType'],
        ]);

        $format = (string)$this->getRequest()->getData('format');
        if (!$this->Export->checkFormat($format)) {
            $this->Flash->error(__('Format choosen is not available'));

            return $this->redirect($this->referer());
        }

        $ids = (string)$this->getRequest()->getData('ids');

        // load data for objects by object type and ids
        $rows = $this->rows($data['objectType'], $ids);

        // create spreadsheet and return as download
        $filename = $this->getFileName($data['objectType'], $format);
        $data = $this->Export->format($format, $rows, $filename);

        // output
        $response = $this->getResponse()->withStringBody(Hash::get($data, 'content'));
        $response = $response->withType(Hash::get($data, 'contentType'));

        return $response->withDownload($filename);
    }

    /**
     * Export related data to format specified by user
     *
     * @param string $id The object ID
     * @param string $relation The relation name
     * @param string $format The file format
     * @return \Cake\Http\Response|null
     */
    public function related(string $id, string $relation, string $format): ?Response
    {
        // check request (allowed methods and required parameters)
        $this->checkRequest([
            'allowedMethods' => ['get'],
        ]);

        if (!$this->Export->checkFormat($format)) {
            $this->Flash->error(__('Format choosen is not available'));

            return $this->redirect($this->referer());
        }

        // load related
        $objectType = $this->getRequest()->getParam('object_type');
        $rows = $this->rowsAllRelated($objectType, $id, $relation);

        // create spreadsheet and return as download
        $filename = sprintf('%s_%s_%s.%s', $objectType, $relation, date('Ymd-His'), $format);
        $data = $this->Export->format($format, $rows, $filename);

        // output
        $response = $this->getResponse()->withStringBody(Hash::get($data, 'content'));
        $response = $response->withType(Hash::get($data, 'contentType'));

        return $response->withDownload($filename);
    }

    /**
     * Obtain csv rows using api get per object type.
     * When using parameter ids, get only specified ids,
     * otherwise get all by object type.
     * First element of data is the attributes/fields array.
     *
     * @param string $objectType The object type
     * @param string $ids Object IDs comma separated string
     * @return array
     */
    protected function rows(string $objectType, string $ids = ''): array
    {
        if (empty($ids)) {
            return $this->rowsAll($objectType);
        }

        $response = $this->apiClient->get($this->apiPath(), ['filter' => ['id' => $ids]]);
        $fields = $this->getFieldNames($response);
        $data = [$fields];
        $this->fillDataFromResponse($data, $response, $fields);

        return $data;
    }

    /**
     * Get API path.
     *
     * @return string
     */
    protected function apiPath(): string
    {
        return sprintf('/%s', (string)$this->getRequest()->getData('objectType'));
    }

    /**
     * Get exported file name.
     *
     * @param string $type Object or resource type.
     * @param string $format The format.
     * @return string
     */
    protected function getFileName(string $type, string $format): string
    {
        return sprintf('%s_%s.%s', $type, date('Ymd-His'), $format);
    }

    /**
     * Get export limit.
     *
     * @return int
     */
    protected function limit(): int
    {
        return (int)Configure::read('Export.limit', self::DEFAULT_EXPORT_LIMIT);
    }

    /**
     * Load all data for a given type using limit and query filters.
     *
     * @param string $objectType Object type
     * @return array
     */
    protected function rowsAll(string $objectType): array
    {
        $data = $fields = [];
        $limit = $this->limit();
        $pageCount = $page = 1;
        $total = 0;
        $pageSize = $limit > self::DEFAULT_PAGE_SIZE ? self::DEFAULT_PAGE_SIZE : $limit;
        $query = ['page_size' => $pageSize] + $this->prepareQuery();
        while ($total < $limit && $page <= $pageCount) {
            $response = (array)$this->apiClient->get($this->apiPath(), $query + compact('page'));
            $pageCount = (int)Hash::get($response, 'meta.pagination.page_count');
            $total += (int)Hash::get($response, 'meta.pagination.page_items');

            if ($page === 1) {
                $fields = $this->getFieldNames($response);
                $data = [$fields];
            }

            $this->fillDataFromResponse($data, $response, $fields);
            $page++;
        }

        return $data;
    }

    /**
     * Load all related data for a given type and relation using limit and query filters.
     *
     * @param string $objectType Object type
     * @param string $id The object ID
     * @param string $relationName The relation name
     * @return array
     */
    protected function rowsAllRelated(string $objectType, string $id, string $relationName): array
    {
        $data = $fields = [];
        $url = sprintf('/%s/%s/%s', $objectType, $id, $relationName);
        $limit = $this->limit();
        $pageCount = $page = 1;
        $total = 0;
        $query = ['page_size' => self::DEFAULT_PAGE_SIZE] + $this->prepareQuery();
        while ($total < $limit && $page <= $pageCount) {
            $response = (array)$this->apiClient->get($url, $query + compact('page'));
            $pageCount = (int)Hash::get($response, 'meta.pagination.page_count');
            $total += (int)Hash::get($response, 'meta.pagination.page_items');

            if ($page === 1) {
                $fields = $this->getFieldNames($response);
                $data = [$fields];
            }

            $this->fillDataFromResponse($data, $response, $fields);
            $page++;
        }

        return $data;
    }

    /**
     * Prepare additional API query from POST data
     *
     * @return array
     */
    protected function prepareQuery(): array
    {
        $res = [];
        $f = (array)$this->getRequest()->getData('filter');
        if (!empty($f)) {
            $filter = [];
            foreach ($f as $v) {
                $filter += (array)json_decode($v, true);
            }
            $res = compact('filter');
        }
        $q = (string)$this->getRequest()->getData('q');
        if (!empty($q)) {
            $res += compact('q');
        }

        return $res;
    }

    /**
     * Fill data array, using response.
     * Return the fields representing each data item.
     *
     * @param array $data The array of data
     * @param array $response The response to use as source for data
     * @param array $fields Field names array
     * @return void
     */
    protected function fillDataFromResponse(array &$data, array $response, array $fields): void
    {
        if (empty($response['data'])) {
            return;
        }

        // fill row data from response data
        foreach ($response['data'] as $val) {
            $data[] = $this->rowFields($val, $fields);
        }
    }

    /**
     * Get field names array using data first element attributes
     *
     * @param array $response The response from which extract fields
     * @return array
     */
    protected function getFieldNames($response): array
    {
        $fields = (array)Hash::get($response, 'data.0.attributes');
        $meta = (array)Hash::get($response, 'data.0.meta');
        unset($meta['extra']);
        $fields = array_merge(['id' => ''], $fields, $meta);
        $fields = array_merge($fields, (array)Hash::get($response, 'data.0.meta.extra'));

        return array_keys($fields);
    }

    /**
     * Get row data per fields
     *
     * @param array $data The data
     * @param array $fields The fields
     * @return array
     */
    protected function rowFields(array $data, array $fields): array
    {
        $row = [];
        foreach ($fields as $field) {
            $row[$field] = '';
            if (isset($data[$field])) {
                $row[$field] = $this->getValue($data[$field]);
            } elseif (isset($data['attributes'][$field])) {
                $row[$field] = $this->getValue($data['attributes'][$field]);
            } elseif (isset($data['meta'][$field])) {
                $row[$field] = $this->getValue($data['meta'][$field]);
            } elseif (isset($data['meta']['extra'][$field])) {
                $row[$field] = $this->getValue($data['meta']['extra'][$field]);
            }
        }

        return $row;
    }

    /**
     * Get value from $value.
     * If is an array, return json representation.
     * Return value otherwise
     *
     * @param mixed $value The value
     * @return mixed
     */
    protected function getValue($value)
    {
        if (is_array($value)) {
            return json_encode($value);
        }

        return $value;
    }
}
