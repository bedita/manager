<?php
namespace App\Controller\Administration;

use App\Controller\AppController;
use BEdita\SDK\BEditaClientException;
use Cake\Event\Event;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\Response;

/**
 * Administration Controller
 */
class AdministrationBaseController extends AppController
{
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
            $response = $this->apiClient->get(sprintf('/admin/%s', $this->resourceType), $query);
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
}
