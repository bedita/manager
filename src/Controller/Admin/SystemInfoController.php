<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2023 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Controller\Admin;

use BEdita\SDK\BEditaClientException;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Utility\Hash;
use Twig\Environment;

/**
 * System info Controller
 */
class SystemInfoController extends AdministrationBaseController
{
    /**
     * @inheritDoc
     */
    public function index(): ?Response
    {
        $this->set('system_info', $this->getSystemInfo());
        $this->set('api_info', $this->getApiInfo());

        return null;
    }

    /**
     * Get system info.
     *
     * @return array
     */
    public function getSystemInfo(): array
    {
        return [
            'Version' => Configure::read('Manager.version'),
            'CakePHP' => Configure::version(),
            'PHP' => phpversion(),
            'Twig' => Environment::VERSION,
            'Vuejs' => '',
            'Operating System' => php_uname(),
            'PHP Server API' => php_sapi_name(),
            'Extensions' => get_loaded_extensions(),
            'Extensions info' => get_loaded_extensions(true),
            'Memory limit' => ini_get('memory_limit'),
            'Post max size' => sprintf('%dM', intVal(substr(ini_get('post_max_size'), 0, -1))),
            'Upload max size' => sprintf('%dM', intVal(substr(ini_get('upload_max_filesize'), 0, -1))),
        ];
    }

    /**
     * Get api info from API server.
     *
     * @return array
     */
    public function getApiInfo(): array
    {
        $info = [
            'Url' => Configure::read('API.apiBaseUrl'),
            'Version' => Hash::get((array)$this->viewBuilder()->getVar('project'), 'version'),
        ];
        try {
            $info = (array)Hash::get(
                $this->apiClient->get('sysinfo'),
                'meta.info'
            );
        } catch (BEditaClientException $e) {
            $this->log($e->getMessage(), 'error');
        }

        return $info;
    }
}
