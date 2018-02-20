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

namespace App\Test\TestCase\Error;

use App\Error\AppExceptionRenderer;
use App\View\AppView;
use Cake\Controller\Controller;
use Cake\Network\Exception\InternalErrorException;
use Cake\Network\Exception\NotFoundException;
use Cake\TestSuite\TestCase;

class TestController extends Controller
{
    /**
     * {@inheritDoc}
     */
    public function render($view = null, $layout = null)
    {
    }
}

/**
 * @coversDefaultClass \App\Error\AppExceptionRenderer
 */
class AppExceptionRendererTest extends TestCase
{
    /**
     * Data provider for `testTemplate` test case.
     *
     * @return array
     */
    public function templateProvider() : array
    {
        return [
            '400 exception' => [
                new NotFoundException('hello'),
                'error400',
            ],
            '500 exception' => [
                new InternalErrorException('hello'),
                'error500',
            ],
        ];
    }

    /**
     * Test error detail on response
     *
     * @param \Exception $exception Expected error.
     * @param string $expected Template.
     * @return void
     *
     * @dataProvider templateProvider
     * @covers ::_template()
     */
    public function testTemplate(\Exception $exception, $expected)
    {
        $renderer = new AppExceptionRenderer($exception);
        $renderer->controller = new TestController();
        $renderer->controller->viewClass = '\App\View\AppView';
        $response = $renderer->render();
        static::assertEquals($expected, $renderer->template);
    }
}
