<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use BEdita\SDK\BEditaClientException;
use Cake\Event\Event;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\Response;

/**
 * Administration Controller
 */
abstract class AdministrationBaseController extends AppController
{
    /**
     * Endpoint
     *
     * @var string
     */
    protected $endpoint = '/admin';

    /**
     * Resource type in use
     *
     * @var string
     */
    protected $resourceType = null;

    /**
     * Readonly flag view.
     *
     * @var bool
     */
    protected $readonly = true;

    /**
     * Properties to show in index columns
     *
     * @var array
     */
    protected $properties = [];

    /**
     * Meta to show in index columns
     *
     * @var array
     */
    protected $meta = ['created', 'modified'];

    /**
     * {@inheritDoc}
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Properties');
    }

    /**
     * Restrict `model` module access to `admin`
     *
     * {@inheritDoc}
     */
    public function beforeFilter(Event $event): ?Response
    {
        $res = parent::beforeFilter($event);
        if ($res !== null) {
            return $res;
        }

        $roles = $this->Auth->user('roles');
        if (empty($roles) || !in_array('admin', $roles)) {
            throw new UnauthorizedException(__('Module access not authorized'));
        }

        return null;
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index(): ?Response
    {
        $this->request->allowMethod(['get']);
        $query = $this->request->getQueryParams();

        try {
            $endpoint = $this->resourceType === 'roles' ? $this->endpoint : sprintf('%s/%s', $this->endpoint, $this->resourceType);
            $response = $this->apiClient->get($endpoint, $query);
        } catch (BEditaClientException $e) {
            $this->log($e, 'error');
            $this->Flash->error($e->getMessage(), ['params' => $e]);

            return $this->redirect(['_name' => 'dashboard']);
        }

        $this->set('resources', (array)$response['data']);
        $this->set('meta', (array)$response['meta']);
        $this->set('links', (array)$response['links']);
        $this->set('resourceType', $this->resourceType);
        $this->set('properties', $this->properties);
        $this->set('metaColumns', $this->meta);
        $this->set('filter', []);
        $this->set('schema', (array)$this->Schema->getSchema($this->resourceType));
        $this->set('readonly', $this->readonly);

        return null;
    }

    /**
     * Save data
     *
     * @return \Cake\Http\Response|null
     */
    public function save(): ?Response
    {
        $this->request->allowMethod(['post']);
        try {
            $this->apiClient->save($this->resourceType, (array)$this->request->getData());
        } catch (BEditaClientException $e) {
            $this->log($e, 'error');
            $this->Flash->error($e->getMessage(), ['params' => $e]);
        }

        return $this->redirect(['_name' => sprintf('admin:list:%s', $this->resourceType)]);
    }

    /**
     * Remove roles by ID
     *
     * @param string $id The role ID
     * @return Response|null
     */
    public function remove(string $id): ?Response
    {
        $this->request->allowMethod(['post']);
        try {
            $endpoint = $this->resourceType === 'roles' ? $this->endpoint : sprintf('%s/%s', $this->endpoint, $this->resourceType);
            $this->apiClient->delete(sprintf('%s/%s', $endpoint, $id));
        } catch (BEditaClientException $e) {
            $this->log($e, 'error');
            $this->Flash->error($e->getMessage(), ['params' => $e]);
        }

        return $this->redirect(['_name' => sprintf('admin:list:%s', $this->resourceType)]);
    }
}
