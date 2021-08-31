<?php
namespace App\Controller;

use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;

/**
 * Lock Controller
 */
class LockController extends AppController
{
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
     * @param bool $locked The value, true or false
     * @return void
     */
    protected function lock(bool $locked): void
    {
        $type = $this->request->getParam('object_type');
        $id = $this->request->getParam('id');
        $meta = compact('locked');
        $data = compact('id', 'type', 'meta');
        $payload = json_encode(compact('data'));
        try {
            $this->apiClient = ApiClientProvider::getApiClient();
            $this->apiClient->patch(
                sprintf('/%s/%s', $type, $id),
                $payload,
                ['Content-Type' => 'application/json']
            );
        } catch (BEditaClientException $ex) {
            $this->loadComponent('App.Flash', ['clear' => true]);
            $this->Flash->error(__($ex->getMessage()));
        }
    }
}
