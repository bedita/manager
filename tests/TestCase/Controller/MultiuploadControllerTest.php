<?php
declare(strict_types=1);
/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Test\TestCase\Controller;

use App\Controller\MultiuploadController;
use Cake\Http\ServerRequest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * {@see \App\Controller\MultiuploadController} Test Case
 */
#[CoversClass(MultiuploadController::class)]
#[CoversMethod(MultiuploadController::class, 'initialize')]
class MultiuploadControllerTest extends BaseControllerTest
{
    /**
     * Test `initialize` method
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $config = [
            'params' => [
                'object_type' => 'documents',
            ],
        ];
        $request = new ServerRequest($config);
        $controller = new MultiuploadController($request);
        $viewVars = $controller->viewBuilder()->getVars();
        $expected = ['currentModule', 'objectType'];
        foreach ($expected as $varName) {
            static::assertArrayHasKey($varName, $viewVars);
        }
        static::assertSame('documents', $viewVars['currentModule']['name']);
        static::assertSame('documents', $viewVars['objectType']);
    }
}
