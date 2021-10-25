<?php
namespace App\Controller\Administration;

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
    protected $properties = ['name', 'description', 'enabled'];
}
