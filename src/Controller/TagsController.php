<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\Model\TagsController as ModelTagsController;
use BEdita\SDK\BEditaClientException;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\Utility\Hash;

/**
 * Tags Controller
 *
 * @property \App\Controller\Component\ProjectConfigurationComponent $ProjectConfiguration
 */
class TagsController extends ModelTagsController
{
    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('ProjectConfiguration');
        $this->Security->setConfig('unlockedActions', ['create', 'patch', 'delete']);
    }

    /**
     * @inheritDoc
     */
    public function index(): ?Response
    {
        parent::index();
        $this->viewBuilder()->setTemplate('/Pages/Model/Tags/index');
        $this->set('hideSidebar', true);
        $this->set('redirTo', ['_name' => 'tags:index']);
        $this->ProjectConfiguration->read();

        return null;
    }

    /**
     * Create new tag (ajax).
     *
     * @return \Cake\Http\Response|null
     */
    public function create(): ?Response
    {
        $this->getRequest()->allowMethod(['post']);
        $this->viewBuilder()->setClassName('Json');
        $response = $error = null;
        try {
            $body = (array)$this->getRequest()->getData();
            $response = $this->apiClient->post('/model/tags', json_encode($body));
        } catch (BEditaClientException $e) {
            $error = $e->getMessage();
            $this->log($error, 'error');
            $this->set('error', $error);
        }
        $this->set('response', $response);
        $this->set('error', $error);
        $this->setSerialize(['response', 'error']);

        return null;
    }

    /**
     * Delete single tag (ajax).
     *
     * @param string $id Tag ID.
     * @return \Cake\Http\Response|null
     */
    public function delete(string $id): ?Response
    {
        $this->getRequest()->allowMethod(['post']);
        $this->viewBuilder()->setClassName('Json');
        $response = $error = null;
        try {
            $this->apiClient->delete(sprintf('/model/tags/%s', $id));
            $response = 'ok';
        } catch (BEditaClientException $e) {
            $error = $e->getMessage();
            $this->log($error, 'error');
            $this->set('error', $error);
        }
        $this->set('response', $response);
        $this->set('error', $error);
        $this->setSerialize(['response', 'error']);

        return null;
    }

    /**
     * Save tag (ajax).
     *
     * @param string $id Tag ID.
     * @return \Cake\Http\Response|null
     */
    public function patch(string $id): ?Response
    {
        $this->getRequest()->allowMethod(['patch']);
        $this->viewBuilder()->setClassName('Json');
        $response = $error = null;
        try {
            $body = (array)$this->getRequest()->getData();
            $this->apiClient->patch(sprintf('/model/tags/%s', $id), json_encode($body));
            $response = 'ok';
        } catch (BEditaClientException $e) {
            $error = $e->getMessage();
            $this->log($error, 'error');
            $this->set('error', $error);
        }
        $this->set('response', $response);
        $this->set('error', $error);
        $this->setSerialize(['response', 'error']);

        return null;
    }

    /**
     * Search tags (ajax)
     *
     * @return \Cake\Http\Response|null
     */
    public function search(): ?Response
    {
        $this->getRequest()->allowMethod(['get']);
        $this->viewBuilder()->setClassName('Json');
        $data = $error = null;
        try {
            $query = $this->getRequest()->getQueryParams();
            $response = $this->apiClient->get('/model/tags', $query);
            $data = (array)Hash::get($response, 'data');
        } catch (BEditaClientException $e) {
            $error = $e->getMessage();
            $this->log($error, 'error');
            $this->set('error', $error);
        }
        $this->set('data', $data);
        $this->set('error', $error);
        $this->setSerialize(['data', 'error']);

        return null;
    }

    /**
     * @inheritDoc
     */
    public function beforeRender(EventInterface $event): ?Response
    {
        parent::beforeRender($event);
        $this->set('moduleLink', ['_name' => 'tags:index']);

        return null;
    }

    /**
     * {@inheritDoc}
     *
     * This to avoid extra perms check for "admin" role.
     *
     * @codeCoverageIgnore
     */
    public function beforeFilter(EventInterface $event): ?Response
    {
        return null;
    }
}
