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

use App\Controller\ModulesController;
use BEdita\SDK\BEditaClientException;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\Utility\Hash;
use Psr\Log\LogLevel;

/**
 * Translations controller: create, edit, remove translations
 */
class TranslationsController extends ModulesController
{
    /**
     * Display data to add a translation.
     *
     * @param string|int $id Object ID.
     * @return \Cake\Http\Response|null
     */
    public function add($id): ?Response
    {
        $this->request->allowMethod(['get']);

        try {
            $response = $this->apiClient->getObject($id, $this->objectType);
        } catch (BEditaClientException $e) {
            // Error! Back to index.
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e->getMessage(), ['params' => $e]);

            return $this->redirect(['_name' => 'modules:view', 'object_type' => $this->objectType, 'id' => $id]);
        }
        $this->ProjectConfiguration->read();

        $this->set('schema', $this->Schema->getSchema($this->objectType));

        $object = Hash::extract($response, 'data');
        $this->set('translation', []);
        $this->set('object', $object);

        return null;
    }

    /**
     * View single translation.
     *
     * @param string|int $id Object ID.
     * @param string $lang The lang code.
     * @return \Cake\Http\Response|null
     */
    public function edit($id, $lang): ?Response
    {
        $this->request->allowMethod(['get']);

        $translation = [];
        try {
            $response = $this->apiClient->getObject($id, $this->objectType, compact('lang'));

            // verify that exists a translation in lang $lang inside include
            if (!empty($response['included'])) {
                foreach ($response['included'] as $included) {
                    if ($included['type'] === 'translations' && $included['attributes']['lang'] == $lang) {
                        $translation = $included;
                    }
                }
            }
            if (empty($translation)) {
                throw new NotFoundException(sprintf('Translation not found per %s %s and lang %s', $this->objectType, $id, $lang));
            }
        } catch (\Exception $e) {
            // Error! Back to index.
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e->getMessage(), ['params' => $e]);

            return $this->redirect(['_name' => 'modules:view', 'object_type' => $this->objectType, 'id' => $id]);
        }
        $this->ProjectConfiguration->read();

        $this->set('schema', $this->Schema->getSchema($this->objectType));

        $object = Hash::extract($response, 'data');
        $this->set('translation', $translation);
        $this->set('object', $object);

        return null;
    }

    /**
     * Create or edit single translation.
     *
     * @return \Cake\Http\Response|null
     */
    public function save(): ?Response
    {
        $this->request->allowMethod(['post']);
        $requestData = $this->prepareRequest($this->objectType);
        $objectId = $requestData['object_id'];
        if (!empty($requestData['id'])) {
            unset($requestData['object_id']);
        }
        $lang = $requestData['lang'];
        try {
            $this->apiClient->save('translations', $requestData);
        } catch (BEditaClientException $e) {
            // Error! Back to object view or index.
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e->getMessage(), ['params' => $e]);

            if ($this->request->getData('id')) {
                return $this->redirect([
                    '_name' => 'translations:edit',
                    'object_type' => $this->objectType,
                    'id' => $objectId,
                    'lang' => $lang,
                ]);
            }

            return $this->redirect([
                '_name' => 'translations:add',
                'object_type' => $this->objectType,
                'id' => $objectId,
            ]);
        }

        return $this->redirect([
            '_name' => 'translations:edit',
            'object_type' => $this->objectType,
            'id' => $objectId,
            'lang' => $lang,
        ]);
    }

    /**
     * Delete single translation.
     * Expected request:
     *     data: [
     *         {
     *             id: <translation id>,
     *             object_id: <translated object id>,
     *         }
     *     ]
     *
     * @return \Cake\Http\Response
     */
    public function delete(): Response
    {
        $this->request->allowMethod(['post']);
        $requestData = $this->request->getData();
        try {
            if (empty($requestData[0])) {
                throw new BadRequestException(__('Empty request data'));
            }
            $translation = $requestData[0];
            if (empty($translation['id'])) {
                throw new BadRequestException(__('Empty translation "id"'));
            }
            if (empty($translation['object_id'])) {
                throw new BadRequestException(__('Empty translation "object_id"'));
            }
            // remove completely the translation
            $this->apiClient->delete(sprintf('/translations/%s', $translation['id']));
        } catch (BEditaClientException $e) {
            $this->log($e, LogLevel::ERROR);
            $this->Flash->error($e->getMessage(), ['params' => $e]);

            // redir to main object view
            return $this->redirect(['_name' => 'modules:view', 'object_type' => $this->objectType, 'id' => $translation['object_id']]);
        }
        $this->Flash->success(__('Translation(s) deleted'));

        // redir to main object view
        return $this->redirect(['_name' => 'modules:view', 'object_type' => $this->objectType, 'id' => $translation['object_id']]);
    }
}
