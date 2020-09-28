<?php
namespace App\Controller\Component;

use Cake\Controller\Component;

/**
 * History component
 */
class HistoryComponent extends Component
{
    /**
     * Key for history data to store.
     *
     * @var string
     */
    protected $key = 'history.%s.attributes';

    /**
     * Load object history from session by ID.
     * If found, overwrite object.attributes and delete history key from session.
     *
     * @param string|int $id The ID
     * @param array $object The object
     * @return void
     */
    public function load($id, array &$object): void
    {
        if (empty($id) || empty($object)) {
            return;
        }
        $key = sprintf($this->key, $id);
        $session = $this->getController()->request->getSession();
        $data = (string)$session->read($key);
        if (empty($data)) {
            return;
        }
        $data = (array)json_decode($data, true);
        $object['attributes'] = $data;
        $session->delete($key);
    }

    /**
     * Write the history data to session.
     *
     * @param array $options The options
     * @return void
     */
    public function write(array $options): void
    {
        /** @var string $objectType */
        $objectType = (string)$options['objectType'];
        /** @var string $id */
        $id = (string)$options['id'];
        /** @var string $historyId */
        $historyId = (string)$options['historyId'];
        /** @var \BEdita\SDK\BEditaClient; $ApiClient */
        $ApiClient = $options['ApiClient'];

        // get history by $objectType, $id and $historyId
        // retrieve all history before (and including) $historyId
        $historyResponse = $ApiClient->get('/history', [
            'filter' => [
                'resource_type' => 'objects',
                'resource_id' => $id,
                'id' => ['le' => $historyId],
            ],
            'sort' => 'created',
            'page_size' => 100,
        ]);

        /** @var \App\Controller\Component\SchemaComponent $Schema */
        $Schema = $options['Schema'];
        $schema = $Schema->getSchema($objectType);
        $attributes = array_fill_keys(array_keys($schema['properties']), null);

        // rebuild attributes along history items
        foreach ($historyResponse['data'] as $item) {
            foreach ($item['meta']['changed'] as $key => $value) {
                $attributes[$key] = $value;
            }
        }
        /** @var \Cake\Http\ServerRequest $Request */
        $Request = $options['Request'];
        $title = $Request->getQuery('title');
        if ($title) {
            $attributes['title'] = $title;
        }

        // write attributes into session
        $key = sprintf($this->key, $id);
        $session = $this->getController()->request->getSession();
        $session->write($key, json_encode($attributes));
    }
}
