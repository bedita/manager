<?php
namespace App\Test\Utils;

use App\Controller\Admin\RolesController;

/**
 * Test class
 *
 * @uses \App\Controller\Admin\RolesController
 */
class RlsController extends RolesController
{
    protected $resourceType = 'roles';
    protected $properties = ['name'];
}
