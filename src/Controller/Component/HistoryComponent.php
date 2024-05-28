<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2022 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Controller\Component;

use App\Utility\Applications;
use App\View\Helper\CalendarHelper;
use App\View\Helper\CategoriesHelper;
use App\View\Helper\SchemaHelper;
use BEdita\WebTools\ApiClientProvider;
use Cake\Controller\Component;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * History component
 */
class HistoryComponent extends Component
{
    /**
     * Calendar helper
     *
     * @var \App\View\Helper\CalendarHelper
     */
    protected $Calendar;

    /**
     * Categories helper
     *
     * @var \App\View\Helper\CategoriesHelper
     */
    protected $Categories;

    /**
     * Schema helper
     *
     * @var \App\View\Helper\SchemaHelper
     */
    protected $Schema;

    /**
     * Key for history data to store.
     *
     * @var string
     */
    protected $key = 'history.%s.attributes';

    /**
     * {@inheritDoc}
     * {@codeCoverageIgnore}
     */
    public function initialize(array $config): void
    {
        $view = new \Cake\View\View();
        $this->Calendar = new CalendarHelper($view);
        $this->Categories = new CategoriesHelper($view);
        $this->Schema = new SchemaHelper($view);

        parent::initialize($config);
    }

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
        $session = $this->getController()->getRequest()->getSession();
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
        /** @var bool $keepUname */
        $keepUname = (bool)$options['keepUname'];
        /** @var \BEdita\SDK\BEditaClient $ApiClient */
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
        $attributes = array_fill_keys(
            array_keys(
                array_filter(
                    $schema['properties'],
                    function ($schema) {
                        return empty($schema['readOnly']);
                    }
                )
            ),
            ''
        );

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

        // if keep uname, recover it from object
        if ($keepUname) {
            $response = $ApiClient->getObject($id, $objectType);
            $attributes['uname'] = Hash::get((array)$response, 'data.attributes.uname');
        }

        // write attributes into session
        $key = sprintf($this->key, $id);
        $session = $this->getController()->getRequest()->getSession();
        $session->write($key, json_encode($attributes));
    }

    /**
     * Fetch history by ID.
     *
     * @param string|int $id The ID
     * @param array $schema The schema for object type
     * @param array $options The options
     * @return array
     */
    public function fetch($id, array $schema, array $options): array
    {
        $response = (array)ApiClientProvider::getApiClient()->get('/history', array_merge(
            [
                'filter' => ['resource_id' => $id],
                'include' => 'user',
                'page_size' => 100,
            ],
            $options
        ));
        $this->formatResponseData($response, $schema);

        return $response;
    }

    /**
     * Parse response data from history and format values considering schema.
     *
     * @param array $response The response to parse and format
     * @param array $schema The schema
     * @return void
     */
    private function formatResponseData(array &$response, array $schema): void
    {
        if (empty($response['data']) || empty($schema)) {
            return;
        }
        $data = Hash::get($response, 'data');
        $included = Hash::combine(Hash::get($response, 'included', []), '{n}.id', '{n}');
        foreach ($data as &$history) {
            $changed = (array)Hash::get($history, 'meta.changed');
            $formatted = [];
            foreach ($changed as $field => $value) {
                $label = $this->label($field);
                $content = $this->content($field, $schema, $value);
                $formatted[$field] = sprintf('<div class="history-field"><label>%s</label>%s</div>', $label, $content);
            }
            $history['meta']['changed'] = $formatted;
            $history['meta']['user'] = Hash::get($included, Hash::get($history, 'meta.user_id'));
            $applicationId = (string)Hash::get($history, 'meta.application_id');
            $history['meta']['application_name'] = Applications::getName($applicationId);
        }
        $response['data'] = $data;
    }

    /**
     * Get label by field
     *
     * @param string $field The field
     * @return string
     */
    public function label(string $field): string
    {
        if ($field === 'date_ranges') {
            return __('Calendar');
        }

        return __(Inflector::humanize($field));
    }

    /**
     * Get content by field, schema and value
     *
     * @param string $field The field
     * @param array $schema The object schema
     * @param mixed $value The value
     * @return string
     */
    public function content(string $field, array $schema, $value): string
    {
        if ($field === 'date_ranges') {
            return sprintf(
                '<date-ranges-list inline-template><div class="index-date-ranges" :class="show-all"><div>%s</div></date-ranges-list>',
                $this->Calendar->list($value)
            );
        }
        if ($field === 'categories') {
            $this->Categories->getView()->set('schema', $schema);

            return $this->Categories->control('categories', $value);
        }
        $fieldSchema = (array)Hash::get($schema, sprintf('properties.%s', $field));

        $content = $this->Schema->format($value, $fieldSchema);
        if (empty($content)) {
            return '-';
        }

        return $content;
    }
}
