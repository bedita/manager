<?php
namespace App\Controller\Admin;

use Cake\Event\Event;
use Cake\Http\Response;
use Cake\Utility\Hash;

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
    protected $readonly = false;

    /**
     * {@inheritDoc}
     */
    protected $properties = ['name', 'context', 'json' => 'content', 'applications' => 'application_id'];

    /**
     * {@inheritDoc}
     */
    public function beforeFilter(Event $event): ?Response
    {
        parent::beforeFilter($event);
        $response = $this->apiClient->get('/admin/applications', ['filter' => ['enabled' => 1]]);
        $applications = ['' => __('No application')] + Hash::combine($response['data'], '{n}.id', '{n}.attributes.name');
        $this->set('applications', $applications);

        return null;
    }
}
