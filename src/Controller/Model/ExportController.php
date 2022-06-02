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
namespace App\Controller\Model;

use App\Controller\AppController;
use Cake\Http\Response;

/**
 * Export cotroller: download project model in JSON format
 */
class ExportController extends AppController
{
    /**
     * Downloaded file name
     *
     * @var string
     */
    public const FILENAME = 'project_model.json';

    /**
     * Export project model JSON file
     *
     * @return \Cake\Http\Response
     */
    public function model(): Response
    {
        $content = $this->apiClient->get('/model/project', [], ['Accept' => 'application/json']);

        $response = $this->getResponse()->withStringBody(json_encode($content));
        $response = $response->withType('application/json');

        return $response->withDownload(self::FILENAME);
    }
}
