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
    const DEFAULT_EXPORT_LIMIT = 10000;

    /**
     * {@inheritDoc}
     * {@codeCoverageIgnore}
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Export');
    }

    /**
     * Export data to format specified by user
     *
     * @return \Cake\Http\Response
     */
    public function export(): Response
    {
        // allow only specific export formats: csv, ods, xlsx
        $format = $this->request->getData('format');
        if (!in_array($format, ['csv', 'ods', 'xlsx'])) {
            return null;
        }
        // check request (allowed methods and required parameters)
        $data = $this->checkRequest([
            'allowedMethods' => ['post'],
            'requiredParameters' => ['objectType'],
        ]);
        $ids = $this->request->getData('ids');

        // load data for objects by object type and ids
        $rows = $this->rows($data['objectType'], $ids);

        // create spreadsheet and return as download
        $spreadsheet = $this->Export->spreadsheet($rows);
        $filename = sprintf('%s_%s.%s', $data['objectType'], date('Ymd-His'), $format);
        $data = $this->Export->{$format}($spreadsheet, $filename);

        // output
        $response = $this->response->withStringBody(Hash::get($data, 'content'));
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

        $data = [];
        $response = $this->apiClient->getObjects($objectType, ['filter' => ['id' => $ids]]);
        $fields = $this->fillDataFromResponse($data, $response);
        array_unshift($data, $fields);

        return $data;
    }

    /**
     * Load all data for a given type using limit and query filters.
     *
     * @param string $objectType Object type
     * @return array
     */
    protected function rowsAll(string $objectType): array
    {
        $data = [];
        $limit = Configure::read('Export.limit', self::DEFAULT_EXPORT_LIMIT);
        $pageCount = $page = 1;
        $total = 0;
        $query = ['page_size' => 100] + $this->prepareQuery();
        while ($total < $limit && $page <= $pageCount) {
            $response = (array)$this->apiClient->getObjects($objectType, $query + compact('page'));
            $pageCount = (int)Hash::get($response, 'meta.pagination.page_count');
            $total += (int)Hash::get($response, 'meta.pagination.page_items');
            $page++;

            $fields = $this->fillDataFromResponse($data, $response);
        }
        array_unshift($data, $fields);

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
        $f = (array)$this->request->getData('filter');
        if (!empty($f)) {
            $filter = [];
            foreach ($f as $v) {
                $filter += (array)json_decode($v, true);
            }
            $res = compact('filter');
        }
        $q = (string)$this->request->getData('q');
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
     * @return array The fields representing each data item
     */
    private function fillDataFromResponse(array &$data, array $response): array
    {
        if (empty($response['data'])) {
            return [];
        }

        // get fields for response 'attributes' and 'meta'
        $fields = $this->getFields($response);
        array_unshift($fields, 'id');
        $metaFields = $this->getFields($response, 'meta');

        // fill row data from response data
        foreach ($response['data'] as $key => $val) {
            $row = [];

            // fill row fields
            $this->fillRowFields($row, $val, $fields);

            // fill row data for meta
            $this->fillRowFields($row, $val, $metaFields);

            $data[] = $row;
        }
        foreach ($metaFields as $f) {
            $fields[$f] = $f;
        }

        return $fields;
    }

    /**
     * Get fields array using data first element attributes
     *
     * @param array $response The response from which extract fields
     * @param string $key The key
     * @return array
     */
    private function getFields($response, $key = 'attributes'): array
    {
        $data = (array)Hash::get($response, sprintf('data.0.%s', $key), []);

        return array_keys($data);
    }

    /**
     * Fill row data per fields
     *
     * @param array $row The row to be filled with data
     * @param mixed $data The data
     * @param array $fields The fields
     * @return void
     */
    private function fillRowFields(&$row, $data, $fields): void
    {
        foreach ($fields as $field) {
            $row[$field] = '';
            if (isset($data[$field])) {
                $row[$field] = $this->getValue($data[$field]);
            } elseif (isset($data['attributes'][$field])) {
                $row[$field] = $this->getValue($data['attributes'][$field]);
            } elseif (isset($data['meta'][$field])) {
                $row[$field] = $this->getValue($data['meta'][$field]);
            }
        }
    }

    /**
     * Get value from $value.
     * If is an array, return json representation.
     * Return value otherwise
     *
     * @param mixed $value The value
     * @return mixed
     */
    private function getValue($value)
    {
        if (is_array($value)) {
            return json_encode($value);
        }

        return $value;
    }
}
