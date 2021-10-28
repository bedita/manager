<?php
namespace App\Controller\Admin;

/**
 * Config Controller
 *
 * @property \App\Controller\Component\PropertiesComponent $Properties
 */
class ConfigController extends AdministrationBaseController
{
    /**
     * Resource type in use
     *
     * @var string
     */
    protected $resourceType = 'config';

    /**
     * {@inheritDoc}
     */
    protected $properties = ['name', 'context', 'content', 'application_id'];
}
