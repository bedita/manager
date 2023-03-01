<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Controller\Component;

use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Controller\Component;
use Cake\Utility\Hash;

/**
 * Handles thumbs.
 *
 * @property-read \App\Controller\Component\FlashComponent $Flash
 * @property-read \App\Controller\Component\QueryComponent $Query
 */
class ThumbsComponent extends Component
{
    /**
     * @inheritDoc
     */
    protected $_defaultConfig = [
        'queryParams' => ['preset' => 'default'],
        'objectTypes' => ['images', 'videos'],
    ];

    /**
     * Components
     *
     * @var array
     */
    protected $components = ['Flash', 'Query'];

    /**
     * Retrieve thumbnails URL of related objects in `meta.url` if present.
     *
     * @param array|null $response Related objects response.
     * @return void
     */
    public function urls(?array &$response): void
    {
        if (empty($response) || empty($response['data'])) {
            return;
        }

        // extract ids of objects
        $types = $this->getConfig('objectTypes', []);
        $ids = (array)Hash::extract($response, sprintf('data.{n}[type=/%s/].id', join('|', $types)));
        if (empty($ids)) {
            return;
        }

        $thumbs = $this->getThumbs($ids);
        if ($thumbs === null) {
            // An error happened: let's try again by generating one thumbnail at a time.
            $thumbs = [];
            foreach ($ids as $id) {
                $thumbs += (array)$this->getThumbs([$id]);
            }
        }

        foreach ($response['data'] as &$object) {
            $thumbnail = Hash::get($object, 'attributes.provider_thumbnail');
            // if provider_thumbnail is found there's no need to extract it from thumbsResponse
            if ($thumbnail) {
                $object['meta']['thumb_url'] = $thumbnail;
                continue;
            }

            // extract url of the matching objectid's thumb
            $thumbnail = Hash::get($thumbs, sprintf('%d.url', $object['id']));
            if ($thumbnail !== null) {
                $object['meta']['thumb_url'] = $thumbnail;
            }
        }

        // Extract possible errors in creation of thumbnail(s)
        $errors = (array)Hash::extract($thumbs, '{*}[acceptable=false].message');

        if (!empty($errors)) {
            $this->Flash->error(__('There where errors creating the thumbnail(s)'), ['params' => $errors]);
        }
    }

    /**
     * Get thumbs by IDs
     *
     * @param array $ids The IDs
     * @return array|null
     */
    protected function getThumbs(array $ids): ?array
    {
        try {
            $params = $this->getController()->getRequest()->getQueryParams();
            $query = $this->Query->prepare($params);
            $url = sprintf('/media/thumbs?%s', http_build_query([
                'ids' => implode(',', $ids),
            ] + $this->getConfig('queryParams', [])));
            $apiClient = ApiClientProvider::getApiClient();
            $res = (array)$apiClient->get($url, $query);

            return (array)Hash::combine($res, 'meta.thumbnails.{*}.id', 'meta.thumbnails.{*}');
        } catch (BEditaClientException $e) {
            $this->getController()->log($e, 'error');

            return null;
        }
    }
}
