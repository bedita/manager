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

namespace App\Test\TestCase\Controller;

use App\Controller\TreeController;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\TreeController} Test Case
 *
 * @coversDefaultClass \App\Controller\TreeController
 */
class TreeControllerTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Controller\TreeController
     */
    public $Tree;

    /**
     * Setup controller to test with request config
     *
     * @param array $requestConfig
     * @return void
     */
    protected function setupController($config = null): void
    {
        $request = null;
        if ($config != null) {
            $request = new ServerRequest($config);
        }
        $this->Tree = new TreeController($request);
    }

    /**
     * Test `treeJson` method
     *
     * @covers ::treeJson()
     * @return void
     */
    public function testTreeJson(): void
    {
        // Setup controller for test
        $this->setupController([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'params' => [
                'object_type' => 'folders',
            ],
        ]);

        // do controller call
        $this->Tree->treeJson();

        // verify expected vars in view
        static::assertArrayHasKey('_serialize', $this->Tree->viewVars);
    }
}
