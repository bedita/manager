<?php
namespace App\Controller\Administration;

/**
 * Async Jobs Controller
 */
class AsyncJobsController extends AdministrationBaseController
{
    /**
     * Resource type in use
     *
     * @var string
     */
    protected $resourceType = 'async_jobs';

    /**
     * {@inheritDoc}
     */
    protected $properties = ['service', 'scheduled_from', 'expires', 'max_attempts', 'locked_until'];

    /**
     * {@inheritDoc}
     */
    protected $meta = ['created', 'modified', 'completed'];
}
