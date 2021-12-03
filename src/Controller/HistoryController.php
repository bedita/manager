<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Http\Response;

/**
 * History Controller
 *
 * @property \App\Controller\Component\HistoryComponent $History
 * @property \App\Controller\Component\SchemaComponent $Schema
 */
class HistoryController extends AppController
{
    /**
     * {@inheritDoc}
     * {@codeCoverageIgnore}
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('History');
        $this->loadComponent('Schema');
    }

    /**
     * Get history data by ID
     *
     * @param string|int $id Object ID.
     * @return void
     */
    public function info($id): void
    {
        $this->viewBuilder()->setClassName('Json');
        $this->request->allowMethod('get');
        $schema = (array)$this->Schema->getSchema($this->request->getParam('object_type'));
        $response = $this->History->fetch($id, $schema);
        $data = $response['data'];
        $meta = $response['meta'];
        $this->set(compact('data', 'meta'));
        $this->set('_serialize', ['data', 'meta']);
    }

    /**
     * Clone an object from a specific point of the history.
     *
     * @param string|int $id Object ID.
     * @param string|int $historyId History object ID.
     * @return \Cake\Http\Response|null
     */
    public function clone($id, $historyId): ?Response
    {
        $this->setHistory($id, $historyId, false);

        return $this->redirect(['_name' => 'modules:clone', 'object_type' => $this->request->getParam('object_type')] + compact('id'));
    }

    /**
     * Restore an object from a specific point of the history.
     *
     * @param string|int $id Object ID.
     * @param string|int $historyId History object ID.
     * @return \Cake\Http\Response|null
     */
    public function restore($id, $historyId): ?Response
    {
        $this->setHistory($id, $historyId, true);

        return $this->redirect(['_name' => 'modules:view', 'object_type' => $this->request->getParam('object_type')] + compact('id'));
    }

    /**
     * Load history data and write into session by ID.
     *
     * @param string|int $id The object type.
     * @param string|int $historyId Object ID.
     * @param bool $keepUname Keep previous uname.
     * @return void
     */
    protected function setHistory($id, $historyId, $keepUname): void
    {
        $objectType = $this->request->getParam('object_type');
        $options = compact('objectType', 'id', 'historyId', 'keepUname') + [
            'ApiClient' => $this->apiClient,
            'Request' => $this->request,
            'Schema' => $this->Schema,
        ];
        $this->History->write($options);
    }
}
