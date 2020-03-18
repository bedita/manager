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
 */
class ExportController extends AppController
{
    /**
     * Export data in csv format
     *
     * @return \Cake\Http\Response
     */
    public function export(): Response
    {
        // check request (allowed methods and required parameters)
        $data = $this->checkRequest([
            'allowedMethods' => ['post'],
            'requiredParameters' => ['objectType'],
        ]);
        $ids = $this->request->getData('ids');

        // load csv data for objects by object type and ids
        $rows = $this->csvRows($data['objectType'], $ids);

        // save data to csv and output it to browser
        return $this->csv($rows, $data['objectType']);
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
    protected function csvRows(string $objectType, string $ids = ''): array
    {
        if (empty($ids)) {
            return $this->csvAll($objectType);
        }

        $data = [];
        $response = $this->apiClient->get($objectType, ['filter' => ['id' => $ids]]);
        $fields = $this->fillDataFromResponse($data, $response);
        array_unshift($data, $fields);

        return $data;
    }

    /**
     * Load all CSV data for a given type usgin limit and query filters.
     *
     * @param string $objectType Object type
     * @return array
     */
    protected function csvAll(string $objectType): array
    {
        $data = [];
        $limit = Configure::read('Export.limit', 10000);
        $pageCount = $page = 1;
        $total = 0;
        $query = ['page_size' => 100] + $this->prepareQuery();
        while ($total < $limit && $page <= $pageCount) {
            $response = (array)$this->apiClient->get($objectType, $query + compact('page'));
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
     * Create csv data (string) per rows/objects and output it to browser
     *
     * @param array $rows The rows data
     * @param string $objectType The object type
     * @return \Cake\Http\Response
     */
    private function csv(array $rows, string $objectType): Response
    {
        $result = $this->processCsv($rows, $objectType);
        extract($result); // => $filename, $csv

        return $this->outputCsv($filename, $csv);
    }

    /**
     * Output csv file.
     *
     * @param string $filename The file name.
     * @param string $csv The csv string data.
     * @return \Cake\Http\Response
     */
    protected function outputCsv($filename, $csv): Response
    {
        $response = $this->response->withStringBody($csv);
        $response = $response->withType('text/csv');

        return $response->withDownload(sprintf('%s.csv', $filename));
    }

    /**
     * Process data (string) per rows/objects.
     * Create a temporary csv file.
     * Return filename and csv string in an array.
     *
     * @param array $rows The rows data.
     * @param string $objectType The object type.
     * @return array
     */
    private function processCsv(array $rows, string $objectType): array
    {
        $csv = '';
        $fields = array_shift($rows);
        $filename = sprintf('%s_%s', $objectType, date('Ymd-His'));
        $tmpfilename = tempnam('/tmp', $filename);
        if ($tmpfilename) {
            $fp = fopen($tmpfilename, 'w+');
            if (!empty($fp)) {
                fputcsv($fp, $fields);
                foreach ($rows as $row) {
                    fputcsv($fp, $row);
                }
                $csv = file_get_contents($tmpfilename);
                fclose($fp);
            }
        }
        unlink($tmpfilename);

        return compact('csv', 'filename');
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
    private function fillRowFields(&$row, $data, $fields)
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
