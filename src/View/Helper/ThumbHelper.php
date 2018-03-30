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
     * @return string|null
     */
    public function url($imageId, $preset = 'default') : ?string
    {
        $result = null;
        try {
            $apiClient = $this->_View->viewVars['apiClient'];
            $response = $apiClient->thumbs($imageId, compact('preset'));
            if (!empty($response['meta']['thumbnails'][0]['url'])) {
                return $response['meta']['thumbnails'][0]['url'];
            }
        } catch (\Exception $e) {
            $this->log($e, 'error');
        }

        return $result;
    }
}
