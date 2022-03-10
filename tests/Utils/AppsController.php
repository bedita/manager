<?php
namespace App\Test\Utils;

use App\Controller\Admin\ApplicationsController;

/**
 * Test class
 *
 * @uses \App\Controller\Admin\ApplicationsController
 */
class AppsController extends ApplicationsController
{
    protected $resourceType = 'applications';
    protected $properties = ['name'];
}
