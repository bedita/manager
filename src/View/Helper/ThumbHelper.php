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
use Cake\Core\Configure;
use Cake\Log\LogTrait;
use Cake\View\Helper;

class ThumbHelper extends Helper
{
    use LogTrait;

    /**
     * @var int Thumb not available
     */
    public const NOT_AVAILABLE = -10;

    /**
     * @var int Thumb not ready
     */
    public const NOT_READY = -20;

    /**
     * @var int Thumb not acceptable
     */
    public const NOT_ACCEPTABLE = -30;

    /**
     * @var int Thumb has no url
     */
    public const NO_URL = -40;

    /**
     * @var int Thumb is OK
     */
    public const OK = 1;

    /**
     * Verify status of image thumb.
     * Return int representing status.
     * Possible values:
     *
     *   NOT_AVAILABLE: something went wrong during api call
     *   NOT_READY: thumb is available, but not ready
     *   NOT_ACCEPTABLE: image is not acceptable, api won't create thumb
     *   NO_URL: url not present in api response
     *   OK: thumb available, ready and with a proper url
     *
     * @param int $imageId The image ID
     * @param string|null $preset The preset name for thumbs options
     * @param string|null $url The thumb url to populate when static::OK
     * @return int|null
     */
    public function status($imageId, $preset = 'default', &$url = '') : ?int
    {
        try {
            $apiClient = ApiClientProvider::getApiClient();
            $response = $apiClient->thumbs($imageId, compact('preset'));
            if (!empty($response['meta']['thumbnails'][0])) {
                $thumb = $response['meta']['thumbnails'][0];
                // check thumb is ready
                if (!$this->isReady($thumb)) {
                    return static::NOT_READY;
                }
                // check thumb is acceptable
                if (!$this->isAcceptable($thumb)) {
                    return static::NOT_ACCEPTABLE;
                }
                // check thumb has url
                if (!$this->hasUrl($thumb)) {
                    return static::NO_URL;
                }
                $url = $thumb['url'];
            }
        } catch (\Exception $e) {
            $this->log($e, 'error');

            return static::NOT_AVAILABLE;
        }

        return static::OK;
    }

    /**
     * Obtain thumbnail using API thumbs.
     *
     * @param int $imageId The image ID.
     * @param string|null $preset The preset name for thumbs options.
     * @return string|int The url if available, the status code otherwise (see Thumb constants).
     */
    public function url($imageId, $preset = 'default')
    {
        $url = null;
        $status = $this->status($imageId, $preset, $url);
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
    private function isAcceptable($thumb = []) :bool
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
    private function isReady($thumb = []) :bool
    {
        if (!empty($thumb['ready']) && $thumb['ready'] === true) {
            return true;
        }

        return false;
    }

    /**
     * Verify if thumb has url
     *
     * @param array $thumb The thumbnail data
     * @return bool the url availability
     */
    private function hasUrl($thumb = []) :bool
    {
        if (!empty($thumb['url'])) {
            return true;
        }

        return false;
    }
}
