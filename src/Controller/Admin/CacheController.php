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
namespace App\Controller\Admin;

use Cake\Cache\Cache;
use Cake\Http\Response;
use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;

/**
 * Cache Controller
 */
class CacheController extends AdministrationBaseController
{
    /**
     * Perform cache clear
     *
     * @return \Cake\Http\Response|null
     */
    public function clear(): ?Response
    {
        $prefixes = Cache::configured();
        foreach ($prefixes as $prefix) {
            Cache::clear($prefix);
        }
        $filesystem = new Filesystem(new LocalFilesystemAdapter(CACHE));
        $filesystem->deleteDirectory('twig_view');

        return $this->redirect($this->referer());
    }
}
