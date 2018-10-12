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
use Cake\Utility\Hash;
use Cake\Event\Event;
use Cake\Http\Response;
use Cake\Network\Exception\BadRequestException;

/**
 * Export controller: upload and load using filters
 */
class ExportController extends AppController
{
    /**
     * {@inheritDoc}
     */
    public function beforeFilter(Event $event) : void
    {
        parent::beforeFilter($event);

        $actions = [
            'export',
        ];

        if (in_array($this->request->params['action'], $actions)) {
            // for csrf
            $this->getEventManager()->off($this->Csrf);

            // for security component
            $this->Security->setConfig('unlockedActions', $actions);
        }
    }

    /**
     * Export data in csv format
     *
     * @return void
     */
    public function export() : void
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
        $this->csv($rows, $data['objectType']);
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
    private function csvRows(string $objectType, string $ids = '') : array
    {
        $fields = $data = [];
        if (empty($ids)) { // empty ids? then get all (multi api calls)
            // read export limit from config, default 10000
            $limit = Configure::read('Export.limit', 10000);
            $pageCount = 1;
            $total = 0;
            $query = ['page_size' => 100];
            for ($i = 0; $i < $pageCount; $i++) {
                if ($total < $limit) {
                    $query['page'] = $i + 1;
                    if ($total + 100 > $limit) {
                        $query['page_size'] = $limit - $total;
                    }
                    $response = $this->apiClient->getObjects($objectType, $query);
                    $pageCount = $response['meta']['pagination']['page_count'];
                    $total += $response['meta']['pagination']['page_items'];
                    $fields = $this->fillDataFromResponse($data, $response);
                }
            }
        } else { // get data per ids
            $response = $this->apiClient->getObjects($objectType, ['filter' => ['id' => $ids]]);
            $fields = $this->fillDataFromResponse($data, $response);
        }
        array_unshift($data, $fields);

        return $data;
    }

    /**
     * Fill data array, using response.
     * Return the fields representing each data item.
     *
     * @param array $data The array of data
     * @param array $response The response to use as source for data
     * @return array The fields representing each data item
     */
    private function fillDataFromResponse(array &$data, array $response) : array
    {
        if (empty($response['data'])) {
            return [];
        }
        $extrafields = [];
        $extraFieldsFilled = false;

        $fields = $this->getFields($response['data']);
        if (($k = array_search('extra', $fields)) !== false) {
            unset($fields[$k]);
        }
        foreach ($response['data'] as $key => $val) {
            $row = [];
            foreach ($fields as $field) {
                $row[$field] = '';
                if (isset($val[$field])) {
                    if (is_array($val[$field])) {
                        $row[$field] = json_encode($val[$field]);
                    } else {
                        $row[$field] = $val[$field];
                    }
                } elseif (isset($val['attributes'][$field])) {
                    if (is_array($val['attributes'][$field])) {
                        $row[$field] = json_encode($val['attributes'][$field]);
                    } else {
                        $row[$field] = $val['attributes'][$field];
                    }
                }
            }
            if (!empty($val['attributes']['extra'])) {
                foreach ($val['attributes']['extra'] as $extrafield => $extraval) {
                    $extraAttribute = sprintf('extra__%s', $extrafield);
                    if (is_array($extraval)) {
                        $row[$extraAttribute] = json_encode($extraval);
                    } else {
                        $row[$extraAttribute] = $extraval;
                    }
                    if (!$extraFieldsFilled) {
                        $extrafields[] = $extraAttribute;
                    }
                }
                $extraFieldsFilled = true;
            }

            $data[] = $row;
        }
        foreach ($extrafields as $ef) {
            array_push($fields, $ef);
        }

        return $fields;
    }

    /**
     * Create csv data (string) per rows/objects and output it to browser
     *
     * @param array $rows The rows data
     * @param string $objectType The object type
     * @return void
     */
    private function csv(array $rows, string $objectType) : void
    {
        // create csv string data
        $fields = array_shift($rows);
        $csv = '';
        try {
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
        } catch (\Exception $e) {
            $this->log('Error during export', 'error');
        }
        unlink($tmpfilename);

        // Output csv file
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=" . $filename . ".csv");
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header("Pragma: no-cache");
        print $csv;
        exit;
    }

    /**
     * Get fields array using data first element attributes
     *
     * @param array $data The data from which extract fields
     * @return array
     */
    private function getFields(array $data = []) : array
    {
        if (empty($data['0']['attributes'])) {
            return [];
        }

        return array_keys($data['0']['attributes']);
    }
}
