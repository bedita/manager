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
        if (empty($this->request)) {
            return;
        }
        $this->request->allowMethod(['post']);
        $ids = $this->request->getData('ids');
        if (empty($ids)) {
            return;
        }
        $objectType = $this->request->getData('objectType');
        if (!is_string($objectType)) {
            return;
        }
        $filename = $objectType . "_" . date('Ymd-His');
        $response = $this->apiClient->getObjects($objectType, ['filter' => ['id' => $ids]]);
        if (empty($response)) {
            return;
        }
        $fields = ['id'] + array_keys($response['data']['0']['attributes']);
        $data = [];
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
            $data[] = $row;
        }
        $csv = '';
        try {
            $tmpfilename = tempnam('/tmp', $filename);
            if ($tmpfilename) {
                $fp = fopen($tmpfilename, 'w+');
                if (!empty($fp)) {
                    fputcsv($fp, $fields);
                    foreach ($data as $row) {
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
}
