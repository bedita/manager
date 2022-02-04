<?php
namespace App\Controller\Admin;

use Cake\Cache\Cache;
use Cake\Filesystem\Folder;
use Cake\Http\Response;

/**
 * Cache Controller
 */
class CacheController extends AdministrationBaseController
{
    /**
     * Perform cache clear
     *
     * @return Response|null
     */
    public function clear(): ?Response
    {
        $prefixes = Cache::configured();
        foreach ($prefixes as $prefix) {
            Cache::clear(false, $prefix);
        }
        $twigCachePath = CACHE . 'twigView';
        $folder = new Folder($twigCachePath);
        $folder->delete();

        return $this->redirect($this->referer());
    }
}
