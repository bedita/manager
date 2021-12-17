<?php
namespace App\Controller\Admin;

/**
 * Async Jobs Controller
 *
 * @property \App\Controller\Component\PropertiesComponent $Properties
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
    protected $deleteonly = true;

    /**
     * {@inheritDoc}
     */
    protected $properties = ['service', 'scheduled_from', 'expires', 'max_attempts', 'locked_until'];

    /**
     * {@inheritDoc}
     */
    protected $meta = ['created', 'modified', 'completed'];
}
