<?php
namespace App\Test\Utils;

use App\Controller\Admin\ConfigController;

/**
 * Test class
 *
 * @uses \App\Controller\Admin\ConfigController
 */
class CfgController extends ConfigController
{
    protected $resourceType = 'config';
    protected $properties = ['name'];
}
