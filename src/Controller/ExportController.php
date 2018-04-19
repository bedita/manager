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
            $this->eventManager()->off($this->Csrf);

            // for security component
            $this->Security->config('unlockedActions', $actions);
        }
    }

    /**
     * Export data in csv format
     *
     * @return void
     */
    public function export() : void
    {
        $this->request->allowMethod(['post']);
        $delimiter = ',';
        $ids = $this->request->getData('ids');
        $objectType = $this->request->getData('objectType');
        $csv = null;
        if (!empty($ids)) { // export selected (filter by id)
            $response = $this->apiClient->getObjects($objectType, ['filter' => ['id' => $ids]]);
            $csv = $this->fillCsv($response, $delimiter);
        }

        // Output csv file
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=" . $objectType . "_" . date('Ymd-His') . ".csv");
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header("Pragma: no-cache");
        print $csv;
        exit;
    }

    /**
     * Csv rows per response data
     *
     * @param array $response the Response
     * @param string $delimiter the Delimiter char
     * @return string the csv string
     */
    private function fillCsv($response = [], $delimiter = ',') : string
    {
        if (empty($response) || empty($response['data'])) {
            return '';
        }
        $fields = ['id'] + array_keys($response['data'][0]['attributes']);
        $csv = implode($delimiter, $fields) . "\n";
        foreach ($response['data'] as $index => $data) {
            $cells = [];
            foreach ($fields as $field) {
                if (!empty($data[$field])) {
                    $cells[] = '"' . preg_replace('/"/', '""', $data[$field]) . '"';
                } elseif (!empty($data['attributes'][$field])) {
                    if (is_array($data['attributes'][$field])) {
                        $cells[] = '"' . preg_replace('/"/', '""', json_encode($data['attributes'][$field])) . '"';
                    } else {
                        $cells[] = '"' . preg_replace('/"/', '""', $data['attributes'][$field]) . '"';
                    }
                } else {
                    $cells[] = '';
                }
            }
            $csv .= implode($delimiter, $cells) . "\n";
        }

        return $csv;
    }
}
