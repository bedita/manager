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
     * {@inheritDoc}
     */
    protected $endpoint = '/roles';

    /**
     * {@inheritDoc}
     */
    protected $resourceType = 'roles';

    /**
     * {@inheritDoc}
     */
    protected $readonly = false;

    /**
     * {@inheritDoc}
     */
    protected $properties = ['name', 'text' => 'description'];
}
