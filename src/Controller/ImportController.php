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
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Response;
use Cake\Utility\Hash;
use Exception;

/**
 * Import controller: upload and load using filters
 */
class ImportController extends AppController
{
    /**
     * List of asyn service names to lookup
     *
     * @var array
     */
    protected $services = [];

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function beforeRender(Event $event): ?Response
    {
        $this->set('moduleLink', ['_name' => 'import:index']);

        return parent::beforeRender($event);
    }

    /**
     * Display import page.
     *
     * @return void
     * @codeCoverageIgnore
     */
    public function index(): void
    {
        $result = $this->request->getSession()->consume('Import.result');
        $this->set(compact('result'));
        $this->loadFilters();
    }

    /**
     * Get jobs rendering as json.
     *
     * @return void
     */
    public function jobs(): void
    {
        $this->viewBuilder()->setClassName('Json');
        $this->request->allowMethod('get');
        $this->loadFilters();
        $this->loadAsyncJobs();
        $this->set('_serialize', ['jobs']);
    }

    /**
     * Import data by filter/file.
     *
     * @return \Cake\Http\Response|null the Response.
     */
    public function file(): ?Response
    {
        try {
            $filter = $this->request->getData('filter');
            if (empty($filter)) {
                throw new BadRequestException(__('Import filter not selected'));
            }
            $importFilter = new $filter($this->apiClient);

            // see http://php.net/manual/en/features.file-upload.errors.php
            $fileError = (int)$this->request->getData('file.error', UPLOAD_ERR_NO_FILE);
            if ($fileError > UPLOAD_ERR_OK) {
                throw new BadRequestException($this->uploadErrorMessage($fileError));
            }

            $result = $importFilter->import(
                $this->request->getData('file.name'),
                $this->request->getData('file.tmp_name'),
                $this->request->getData('filter_options')
            );
            $this->request->getSession()->write(['Import.result' => $result]);
        } catch (Exception $e) {
            $this->Flash->error($e->getMessage(), ['params' => $e]);
        }

        return $this->redirect(['_name' => 'import:index']);
    }

    /**
     * Return a meaningful upload error message
     * see http://php.net/manual/en/features.file-upload.errors.php
     *
     * @param int $code Upload error code
     * @return string
     */
    protected function uploadErrorMessage(int $code): string
    {
        $errors = [
            UPLOAD_ERR_INI_SIZE => __('File is too big, max allowed size is {0}', ini_get('upload_max_filesize')),
            UPLOAD_ERR_FORM_SIZE => __('File is too big, form MAX_FILE_SIZE exceeded'),
            UPLOAD_ERR_PARTIAL => __('File only partially uploaded'),
            UPLOAD_ERR_NO_FILE => __('Missing import file'),
            UPLOAD_ERR_NO_TMP_DIR => __('Temporary folder missing'),
            UPLOAD_ERR_CANT_WRITE => __('Failed to write file to disk'),
            UPLOAD_ERR_EXTENSION => __('An extension stopped the file upload'),
        ];

        return (string)Hash::get($errors, (string)$code, __('Unknown upload error'));
    }

    /**
     * Load filters for view
     *
     * @return void
     */
    private function loadFilters(): void
    {
        $filters = [];
        $importFilters = Configure::read('Filters.import', []);
        foreach ($importFilters as $filter) {
            $value = $filter['class'];
            $text = $filter['label'];
            $options = $filter['options'];
            $filters[] = compact('value', 'text', 'options');
            $this->updateServiceList($value);
        }
        $this->set('filters', $filters);
        $this->set('services', $this->services);
        $this->loadAsyncJobs();
    }

    /**
     * Update services list to lookup
     *
     * @param string $filterClass Filter class
     * @return void
     */
    protected function updateServiceList($filterClass): void
    {
        $service = call_user_func([$filterClass, 'getServiceName']);
        if (!empty($service) && !in_array($service, $this->services)) {
            $this->services[] = $service;
        }
    }

    /**
     * Load async jobs services to lookup
     *
     * @return void
     */
    protected function loadAsyncJobs(): void
    {
        if (empty($this->services)) {
            $this->set('jobs', []);

            return;
        }
        $query = [
            'sort' => '-created',
            'filter' => ['service' => implode(',', $this->services)],
        ];
        try {
            $response = $this->apiClient->get('/admin/async_jobs', $query);
        } catch (BEditaClientException $e) {
            $this->log($e, 'error');
            $this->Flash->error($e->getMessage(), ['params' => $e]);
            $response = [];
        }

        $this->set('jobs', (array)Hash::get($response, 'data'));
    }
}
