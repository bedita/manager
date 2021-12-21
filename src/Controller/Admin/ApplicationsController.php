<?php
namespace App\Controller\Admin;

/**
 * Applications Controller
 *
 * @property \App\Controller\Component\PropertiesComponent $Properties
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
    protected $readonly = false;

    /**
     * {@inheritDoc}
     */
    protected $properties = ['name', 'text' => 'description', 'bool' => 'enabled'];
}
