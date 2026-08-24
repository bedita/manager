<?php
declare(strict_types=1);

namespace App\Controller;

use BEdita\WebTools\Utility\ApiTools;
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
     * Get history using query parameters.
     * This is a proxy for /api/history endpoint.
     *
     * @return void
     */
    public function get(): void
    {
        $this->viewBuilder()->setClassName('Json');
        $this->getRequest()->allowMethod('get');
        $query = $this->getRequest()->getQueryParams();
        $response = ApiTools::cleanResponse((array)$this->apiClient->get('/history', $query));
        $data = $response['data'];
        $meta = $response['meta'];
        $this->set(compact('data', 'meta'));
        $this->setSerialize(['data', 'meta']);
    }

    /**
     * Get objects list: id, title, uname only / no relationships, no links.
     *
     * @return void
     */
    public function objects(): void
    {
        $this->viewBuilder()->setClassName('Json');
        $this->getRequest()->allowMethod('get');
        $query = array_merge(
            $this->getRequest()->getQueryParams(),
            ['fields' => 'id,title,uname'],
        );
        $response = ApiTools::cleanResponse((array)$this->apiClient->get('objects', $query));
        $data = $response['data'];
        $meta = $response['meta'];
        $this->set(compact('data', 'meta'));
        $this->setSerialize(['data', 'meta']);
    }

    /**
     * Get history data by ID
     *
     * @param string|int $id Object ID.
     * @param int $page Page number.
     * @return void
     */
    public function info(string|int $id, int $page): void
    {
        $this->viewBuilder()->setClassName('Json');
        $this->getRequest()->allowMethod('get');
        $schema = (array)$this->Schema->getSchema($this->getRequest()->getParam('object_type'));
        $response = $this->History->fetch($id, $schema, compact('page'));
        $data = $response['data'];
        $meta = $response['meta'];
        $this->set(compact('data', 'meta'));
        $this->setSerialize(['data', 'meta']);
    }

    /**
     * Clone an object from a specific point of the history.
     *
     * @param string|int $id Object ID.
     * @param string|int $historyId History object ID.
     * @return \Cake\Http\Response|null
     */
    public function clone(string|int $id, string|int $historyId): ?Response
    {
        $this->setHistory($id, $historyId, false);

        return $this->redirect(['_name' => 'modules:clone', 'object_type' => $this->getRequest()->getParam('object_type')] + compact('id'));
    }

    /**
     * Restore an object from a specific point of the history.
     *
     * @param string|int $id Object ID.
     * @param string|int $historyId History object ID.
     * @return \Cake\Http\Response|null
     */
    public function restore(string|int $id, string|int $historyId): ?Response
    {
        $this->setHistory($id, $historyId, true);

        return $this->redirect(['_name' => 'modules:view', 'object_type' => $this->getRequest()->getParam('object_type')] + compact('id'));
    }

    /**
     * Load history data and write into session by ID.
     *
     * @param string|int $id The object type.
     * @param string|int $historyId Object ID.
     * @param bool $keepUname Keep previous uname.
     * @return void
     */
    protected function setHistory(string|int $id, string|int $historyId, bool $keepUname): void
    {
        $objectType = $this->getRequest()->getParam('object_type');
        $options = compact('objectType', 'id', 'historyId', 'keepUname') + [
            'ApiClient' => $this->apiClient,
            'Request' => $this->getRequest(),
            'Schema' => $this->Schema,
        ];
        $this->History->write($options);
    }
}
