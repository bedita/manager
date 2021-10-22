<?php
namespace App\Controller\Administration;

/**
 * Applications Controller
 */
class ApplicationsController extends AdministrationBaseController
{
    /**
     * Resource type in use
     *
     * @var string
     */
    protected $resourceType = 'applications';

    /**
     * {@inheritDoc}
     */
    protected $properties = ['name', 'description', 'enabled'];
}
