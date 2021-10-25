<?php
namespace App\Controller\Admin;

use Cake\Http\Response;

/**
 * Endpoints Controller
 *
 * @property \App\Controller\Component\PropertiesComponent $Properties
 */
class EndpointsController extends AdministrationBaseController
{
    /**
     * Resource type in use
     *
     * @var string
     */
    protected $resourceType = 'endpoints';

    /**
     * {@inheritDoc}
     */
    protected $properties = ['name', 'description'];
}
