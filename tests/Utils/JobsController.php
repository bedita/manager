<?php
namespace App\Test\Utils;

use App\Controller\Admin\AsyncJobsController;

/**
 * Test class
 *
 * @uses \App\Controller\Admin\AsyncJobsController
 */
class JobsController extends AsyncJobsController
{
    protected $resourceType = 'async_jobs';
    protected $properties = ['name'];
}
