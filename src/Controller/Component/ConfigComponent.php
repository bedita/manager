<?php
declare(strict_types=1);
/**
 * BEdita, API-first content management framework
 * Copyright 2022 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Controller\Component;

use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Utility\Hash;

/**
 * Config component
 */
class ConfigComponent extends Component
{
    /**
     * BEdita Api client
     *
     * @var \BEdita\SDK\BEditaClient
     */
    protected $apiClient = null;

    /**
     * The config ID
     *
     * @var int
     */
    protected $configId;

    /**
     * {@inheritDoc}
     * {@codeCoverageIgnore}
     */
    public function startup(): void
    {
        $this->apiClient = ApiClientProvider::getApiClient();
    }

    /**
     * Get config from db whose name is 'Modules', if any.
     * Get 'Modules' config [from config/app.php etc.] otherwise.
     *
     * @return array
     */
    public function modules(): array
    {
        try {
            $response = (array)$this->apiClient->get('/config');
            $modules = (array)Hash::get($response, 'data');
            foreach ($modules as $module) {
                if ($module['attributes']['name'] === 'Modules') {
                    $this->configId = intVal($module['id']);

                    return (array)json_decode((string)Hash::get($module, 'attributes.content'), true);
                }
            }
        } catch (BEditaClientException $e) {
            $this->log($e->getMessage(), 'error');
            $this->Flash->error($e->getMessage(), ['params' => $e]);
        }

        return (array)Configure::read('Modules');
    }

    public function modulesConfigId(): ?int
    {
        if (!empty($this->configId)) {
            return $this->configId;
        }
        try {
            $response = (array)$this->apiClient->get('/config');
            $modules = (array)Hash::get($response, 'data');
            foreach ($modules as $module) {
                if ($module['attributes']['name'] === 'Modules') {
                    $this->configId = intVal($module['id']);

                    return $this->configId;
                }
            }
        } catch (BEditaClientException $e) {
            $this->log($e->getMessage(), 'error');
            $this->Flash->error($e->getMessage(), ['params' => $e]);
        }

        return null;
    }

    public function saveModules(array $modules): void
    {
        $this->configId = $this->modulesConfigId();
        $endpoint = '/admin/config';
        try {
            $body = [
                'data' => [
                    'type' => 'config',
                    'attributes' => [
                        'name' => 'Modules',
                        'context' => 'core',
                        'content' => json_encode($modules),
                    ],
                ],
            ];
            if (empty($this->configId)) {
                $this->apiClient->post($endpoint, json_encode($body));
            } else {
                $body['data']['id'] = (string)$this->configId;
                $this->apiClient->patch(sprintf('%s/%s', $endpoint, $this->configId), json_encode($body));
            }
        } catch (BEditaClientException $e) {
            $this->log($e->getMessage(), 'error');
            $this->Flash->error($e->getMessage(), ['params' => $e]);
        }
    }

    /**
     * Get config from db by ID, if any.
     * Get 'Modules' config [from config/app.php etc.] otherwise.
     *
     * @return array
     */
    protected function configById(int $id): array
    {
        try {
            $response = $this->apiClient->get(sprintf('/config/%d', $id));
            if ($response['data'][0]['attributes']['content']) {
                return (array)json_decode((string)Hash::get($response, 'data.0.attributes.content'));
            }
        } catch (BEditaClientException $e) {
            $this->log($e->getMessage(), 'error');
            $this->Flash->error($e->getMessage(), ['params' => $e]);
        }

        return (array)Configure::read('Modules');
    }
}
