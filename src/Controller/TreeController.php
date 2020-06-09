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

use BEdita\SDK\BEditaClientException;
use Cake\Utility\Hash;

/**
 * Tree controller.
 */
class TreeController extends AppController
{
    /**
     * Load tree data.
     *
     * @return void
     */
    public function treeJson(): void
    {
        $this->request->allowMethod(['get']);
        $query = $this->Modules->prepareQuery($this->request->getQueryParams());
        $root = Hash::get($query, 'root');
        $full = Hash::get($query, 'full');
        unset($query['full']);
        unset($query['root']);
        try {
            if (empty($full)) {
                $key = (empty($root)) ? 'roots' : 'parent';
                $val = (empty($root)) ? '' : $root;
                $query['filter'][$key] = $val;
            }
            $response = $this->apiClient->getObjects('folders', $query);
        } catch (BEditaClientException $error) {
            $this->log($error, 'error');

            $this->set(compact('error'));
            $this->set('_serialize', ['error']);

            return;
        }

        $this->set((array)$response);
        $this->set('_serialize', array_keys($response));
    }
}
