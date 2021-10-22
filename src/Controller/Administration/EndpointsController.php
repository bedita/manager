<?php
namespace App\Controller\Administration;

use Cake\Http\Response;

/**
 * Endpoints Controller
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
