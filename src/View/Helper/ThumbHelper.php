<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\View\Helper;

use App\ApiClientProvider;
use BEdita\SDK\BEditaClientException;
use Cake\Core\Configure;
use Cake\Log\LogTrait;
use Cake\Utility\Hash;
use Cake\View\Helper;

/**
 * Helper class to create/retrieve image thumbnails
 *
 */
class ThumbHelper extends Helper
{
    use LogTrait;

    /**
     * @var int Thumb is OK
     */
    public const OK = 1;

    /**
     * Thumb not yet ready, creation in progress
     *
     * @var int
     */
    public const NOT_READY = -10;

    /**
     * Thumb request not acceptable, not able to create a thumbnail
     *
     * @var int
     */
    public const NOT_ACCEPTABLE = -20;

    /**
     * Thumb request problem, malformed response or missing data
     *
     * @var int
     */
    public const ERROR = -30;

    /**
     * Verify status of image thumb.
     * Return int representing status.
     * Possible values:
     *
     *   OK: thumb available, ready and with a proper url
     *   NOT_READY: thumb is available, but not ready
     *   NOT_ACCEPTABLE: image is not acceptable, api won't create thumb
     *   ERROR: url not present in API response or missing thumbnail data,
     *          something went wrong during api call
     *
     * @param string|int $imageId The image ID
     * @param array|null $options The thumbs options
     * @param string|null $url The thumb url to populate when static::OK
     * @return int Thumbnail processing status
     */
    public function status($imageId, ?array $options = [], &$url = '') : int
    {
        try {
            $apiClient = ApiClientProvider::getApiClient();
            $response = $apiClient->thumbs($imageId, $options);
        } catch (BEditaClientException $e) {
            $this->log($e, 'error');

            return static::ERROR;
        }

        if (empty($response)) {
            return static::ERROR;
        }

        $thumb = Hash::get($response, 'meta.thumbnails.0');
        if (empty($thumb) || empty($thumb['url'])) {
            return static::ERROR;
        }

        // check thumb is acceptable
        if (!$this->isAcceptable($thumb)) {
            return static::NOT_ACCEPTABLE;
        }

        $url = $thumb['url'];

        // check thumb is ready
        if (!$this->isReady($thumb)) {
            return static::NOT_READY;
        }

        return static::OK;
    }

    /**
     * Obtain thumbnail using API thumbs.
     *
     * @param string|int $imageId The image ID.
     * @param array|null $options The thumbs options.
     * @return string|int The url if available, the status code otherwise (see Thumb constants).
     */
    public function url($imageId, $options)
    {
        $url = null;
        $status = $this->status($imageId, $options, $url);
        if ($status === static::OK) {
            return $url;
        }

        return $status;
    }

    /**
     * Verify if thumb is acceptable
     *
     * @param array $thumb The thumbnail data
     * @return bool the acceptable flag
     */
    private function isAcceptable(array $thumb) :bool
    {
        if (isset($thumb['acceptable']) && $thumb['acceptable'] === false) {
            return false;
        }

        return true;
    }

    /**
     * Verify if thumb is ready
     *
     * @param array $thumb The thumbnail data
     * @return bool the ready flag
     */
    private function isReady(array $thumb) :bool
    {
        if (!empty($thumb['ready']) && $thumb['ready'] === true) {
            return true;
        }

        return false;
    }
}
