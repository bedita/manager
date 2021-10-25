<?php
namespace App\Controller\Admin;

/**
 * Roles Controller
 *
 * @property \App\Controller\Component\PropertiesComponent $Properties
 */
class RolesController extends AdministrationBaseController
{
    /**
     * Endpoint
     *
     * @var string
     */
    protected $endpoint = '/roles';

    /**
     * Resource type in use
     *
     * @var string
     */
    protected $resourceType = 'roles';

    /**
     * {@inheritDoc}
     */
    protected $properties = ['name', 'description'];
}
