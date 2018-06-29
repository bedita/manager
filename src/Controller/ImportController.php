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
 * Import controller: upload and load using filters
 */
class ImportController extends AppController
{
    /**
     * {@inheritDoc}
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
        // prepare import filter
        $filter = $this->request->getData('filter');
        if (empty($filter)) {
            throw new BadRequestException('Import filter not selected', 500);
        }
        $importFilter = new $filter($this->apiClient);
        // read file
        $filename = $this->request->getData('file.name');
        $filepath = $this->request->getData('file.tmp_name');
        try {
            $result = $importFilter->import($filename, $filepath);
            $this->set(compact('result'));
        } catch (Exception $e) {
            $this->Flash->error($e, ['params' => $e->getAttributes()]);

            return $this->redirect(['_name' => 'import:index']);
        }
        $this->loadFilters();
        $this->render('index');

        return null;
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
