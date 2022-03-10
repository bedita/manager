<?php
namespace App\Test\Utils;

use App\Controller\Admin\AdministrationBaseController;

/**
 * Test class
 *
 * @uses \App\Controller\Admin\AdministrationBaseController
 */
class WrongAdminBaseController extends AdministrationBaseController
{
    /**
     * Resource type currently used
     *
     * @var string
     */
    protected $resourceType = 'wrongtype';
}
