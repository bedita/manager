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
use Cake\Core\Exception\Exception as CakeException;
use Cake\Event\Event;
use Cake\Http\Response;
use Cake\Network\Exception\BadRequestException;
use Cake\Utility\Hash;
use Exception;

/**
 * Import controller: upload and load using filters
 */
class ImportController extends AppController
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function beforeRender(Event $event) : void
    {
        parent::beforeRender($event);
        $this->set('moduleLink', ['_name' => 'import:index']);
    }

    /**
     * Display import page.
     *
     * @return void
     * @codeCoverageIgnore
     */
    public function index() : void
    {
        $this->loadFilters();
    }

    /**
     * Import data by filter/file.
     *
     * @return \Cake\Http\Response|null the Response.
     */
    public function file() : ?Response
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
                $this->request->getData('file.tmp_name')
            );
            $this->set(compact('result'));
        } catch (CakeException $e) {
            $this->Flash->error($e, ['params' => $e->getAttributes()]);

            return $this->redirect(['_name' => 'import:index']);
        } catch (Exception $e) {
            $this->Flash->error($e, ['params' => ['code' => 500]]);

            return $this->redirect(['_name' => 'import:index']);
        }
        $this->loadFilters();
        $this->render('index');

        return null;
    }

    /**
     * Return a meaningful upload error message
     * see http://php.net/manual/en/features.file-upload.errors.php
     *
     * @param int $code Upload error code
     * @return string
     */
    protected function uploadErrorMessage(int $code)
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

        return (string)Hash::get($errors, (string)$code, __('Unkown upload error'));
    }

    /**
     * Load filters for view
     *
     * @return void
     */
    private function loadFilters()
    {
        $filters = [];
        $importFilters = Configure::read('Filters.import', []);
        foreach ($importFilters as $filter) {
            $value = $filter['class'];
            $text = $filter['label'];
            $filters[] = compact('value', 'text');
        }
        $this->set('filters', $filters);
    }
}
