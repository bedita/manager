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

namespace App\Shell;

use Cake\Filesystem\Folder;
use Cake\Shell\CacheShell as BaseCacheShell;

/**
 * Extend `CacheShell::clearAll` to remove Twig compiled files.
 */
class CacheShell extends BaseCacheShell
{
    /**
     * {@inheritDoc}
     */
    public function clearAll()
    {
        parent::clearAll();
        $twigCachePath = CACHE . 'twigView';
        $folder = new Folder($twigCachePath);
        if (file_exists($twigCachePath) && !$folder->delete()) {
            $this->error("Error removing Twig cache files in $twigCachePath");

            return;
        }

        $this->out("<success>Cleared twig cache</success>");
    }
}
