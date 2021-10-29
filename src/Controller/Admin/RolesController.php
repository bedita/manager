<?php
namespace App\Controller\Admin;

use BEdita\SDK\BEditaClientException;
use Cake\Http\Response;

/**
 * Roles Controller
 *
 * @property \App\Controller\Component\PropertiesComponent $Properties
 */
class RolesController extends AdministrationBaseController
{
    /**
     * {@inheritDoc}
     */
    protected $endpoint = '/roles';

    /**
     * {@inheritDoc}
     */
    protected $resourceType = 'roles';

    /**
     * {@inheritDoc}
     */
    protected $readonly = false;

    /**
     * {@inheritDoc}
     */
    protected $properties = ['name', 'description'];

    /**
     * Save roles
     *
     * @return \Cake\Http\Response|null
     */
    public function save(): ?Response
    {
        $this->request->allowMethod(['post']);
        try {
            $this->apiClient->save('roles', (array)$this->request->getData());
        } catch (BEditaClientException $e) {
            $this->log($e, 'error');
            $this->Flash->error($e->getMessage(), ['params' => $e]);
        }

        return $this->redirect(['_name' => 'admin:list:roles']);
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
            $this->apiClient->delete(sprintf('/roles/%s', $id));
        } catch (BEditaClientException $e) {
            $this->log($e, 'error');
            $this->Flash->error($e->getMessage(), ['params' => $e]);
        }

        return $this->redirect(['_name' => 'admin:list:roles']);
    }
}
