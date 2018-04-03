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
     * Obtain thumbnail using API thumbs
     *
     * @param int $imageId The image ID
     * @param string|null $preset The preset name for thumbs options
     * @return string|null The url if available, null otherwise
     */
    public function url($imageId, $preset = 'default') : ?string
    {
        try {
            $apiClient = ApiClientProvider::getApiClient();
            $response = $apiClient->thumbs($imageId, compact('preset'));
            if (!empty($response['meta']['thumbnails'][0])) {
                $thumb = $response['meta']['thumbnails'][0];
                // check thumb is ready
                if (!$this->isReady($thumb)) {
                    $this->log(sprintf('Thumb for image %d not created: not ready', $imageId), 'warn');

                    return null;
                }
                // check thumb is acceptable
                if (!$this->isAcceptable($thumb)) {
                    $this->log(sprintf('Thumb for image %d not created: not acceptable', $imageId), 'error');

                    return null;
                }
                // check thumb has url
                if (!$this->hasUrl($thumb)) {
                    $this->log(sprintf('Missing url for thumb for image %d', $imageId), 'error');

                    return null;
                }

                return $thumb['url'];
            }
        } catch (\Exception $e) {
            $this->log($e, 'error');
        }

        return null;
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
