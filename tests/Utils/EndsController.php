<?php
namespace App\Test\Utils;

use App\Controller\Admin\EndpointsController;

/**
 * Test class
 *
 * @uses \App\Controller\Admin\EndpointsController
 */
class EndsController extends EndpointsController
{
    protected $resourceType = 'endpoints';
    protected $properties = ['name'];
}
