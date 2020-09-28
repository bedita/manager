<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Http\Response;

/**
 * History Controller
 *
 * @property \App\Controller\Component\HistoryComponent $History
 */
class HistoryController extends AppController
{
    /**
     * {@inheritDoc}
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('History');
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
        $this->setHistory($id, $historyId);

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
        $this->setHistory($id, $historyId);

        return $this->redirect(['_name' => 'modules:view', 'object_type' => $this->request->getParam('object_type')] + compact('id'));
    }

    /**
     * Load history data and write into session by ID.
     *
     * @param string|int $id The object type.
     * @param string|int $historyId Object ID.
     * @return void
     */
    protected function setHistory($id, $historyId): void
    {
        $objectType = $this->request->getParam('object_type');
        $options = compact('objectType', 'id', 'historyId') + [
            'ApiClient' => $this->apiClient,
            'Request' => $this->request,
            'Schema' => $this->Schema,
        ];
        $this->History->write($options);
    }
}
