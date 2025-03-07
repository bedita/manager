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

namespace App\Test\TestCase\Controller\Model;

use App\Controller\Model\ExportController;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * {@see \App\Controller\Model\ExportController} Test Case
 */
#[CoversClass(ExportController::class)]
#[CoversMethod(ExportController::class, 'model')]
class ExportControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Model\ExportController
     */
    public ExportController $Export;

    /**
     * Client API
     *
     * @var \BEdita\SDK\BEditaClient
     */
    public BEditaClient $apiClient;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        $this->apiClient = ApiClientProvider::getApiClient();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        ApiClientProvider::setApiClient($this->apiClient);
    }

    /**
     * Test `model` method
     *
     * @return void
     */
    public function testModel(): void
    {
        $data = [
            'applications' => [
                [
                    'name' => 'website',
                ],
            ],
        ];

        // mock api get.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->willReturn($data);
        ApiClientProvider::setApiClient($apiClient);

        $this->Export = new ExportController(
            new ServerRequest([
                'environment' => [
                    'REQUEST_METHOD' => 'GET',
                ],
            ])
        );

        $result = $this->Export->model();
        static::assertInstanceOf(Response::class, $result);
        $content = $result->getBody()->__toString();
        static::assertEquals(json_encode($data), $content);
    }
}
