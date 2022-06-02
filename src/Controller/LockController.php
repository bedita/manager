<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
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
use Cake\Http\Response;
use Psr\Log\LogLevel;

/**
 * Lock Controller
 */
class LockController extends AppController
{
    /**
     * Add lock
     *
     * @return \Cake\Http\Response|null
     */
    public function add(): ?Response
    {
        $this->lock(true);

        return $this->redirect($this->referer());
    }

    /**
     * Remove lock
     *
     * @return \Cake\Http\Response|null
     */
    public function remove(): ?Response
    {
        $this->lock(false);

        return $this->redirect($this->referer());
    }

    /**
     * Perform lock/unlock on an object.
     *
     * @param bool $locked The value, true or false
     * @return bool
     */
    protected function lock(bool $locked): bool
    {
        $type = $this->getRequest()->getParam('object_type');
        $id = $this->getRequest()->getParam('id');
        $meta = compact('locked');
        $data = compact('id', 'type', 'meta');
        $payload = json_encode(compact('data'));
        try {
            $this->apiClient->patch(
                sprintf('/%s/%s', $type, $id),
                $payload,
                ['Content-Type' => 'application/json']
            );
        } catch (BEditaClientException $ex) {
            $this->log($ex->getMessage(), LogLevel::ERROR);
            $this->Flash->error(__('Error: %s', $ex->getMessage()));

            return false;
        }

        return true;
    }
}
