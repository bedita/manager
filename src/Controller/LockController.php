<?php
namespace App\Controller;

use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Controller\Controller;

/**
 * Lock Controller
 */
class LockController extends Controller
{
    /**
     * BEdita4 API client
     *
     * @var \BEdita\SDK\BEditaClient
     */
    protected $apiClient = null;

    /**
     * Add lock
     *
     * @return void
     */
    public function add(): void
    {
        $this->lock(true);
        $this->redirect($this->referer());
    }

    /**
     * Remove lock
     *
     * @return void
     */
    public function remove(): void
    {
        $this->lock(false);
        $this->redirect($this->referer());
    }

    /**
     * Perform lock/unlock on an object.
     *
     * @param bool $val The value, true or false
     * @return void
     */
    protected function lock(bool $val): void
    {
        $type = $this->request->getParam('object_type');
        $id = $this->request->getParam('id');
        $meta = ['locked' => $val];
        $data = compact('id', 'type', 'meta');
        try {
            $this->apiClient = ApiClientProvider::getApiClient();
            $this->apiClient->patch(
                sprintf('/%s/%s', $type, $id),
                json_encode($data),
                ['Content-Type' => 'application/json']
            );
        } catch (BEditaClientException $ex) {
            $this->loadComponent('App.Flash', ['clear' => true]);
            $this->Flash->error(__($ex->getMessage()));
        }
    }
}
