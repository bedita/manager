<?php
declare(strict_types=1);

namespace App\View\Helper;

use App\Utility\CacheTools;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\View\Helper;

/**
 * System helper
 */
class SystemHelper extends Helper
{
    /**
     * Get the minimum value between post_max_size and upload_max_filesize.
     *
     * @return int
     */
    public function getMaxFileSize(): int
    {
        $postMaxSize = intVal(substr(ini_get('post_max_size'), 0, -1));
        $uploadMaxFilesize = intVal(substr(ini_get('upload_max_filesize'), 0, -1));

        return min($postMaxSize, $uploadMaxFilesize) * 1024 * 1024;
    }

    /**
     * Get pagination size available from cache or config.
     *
     * @return array
     */
    public function paginationSizeAvailable(): array
    {
        $defaultConfig = (array)Configure::read('Pagination.sizeAvailable');
        $config = Cache::read(CacheTools::cacheKey('config.Pagination'));
        if (!empty($config)) {
            $content = (array)json_decode((string)Hash::get($config, 'attributes.content'), true);

            return (array)Hash::get($content, 'sizeAvailable', $defaultConfig);
        }

        return $defaultConfig;
    }
}
