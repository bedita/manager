<?php
declare(strict_types=1);

namespace App\View\Helper;

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
     * Return false when API version is less than required, true otherwise.
     *
     * @return bool
     */
    public function checkBeditaApiVersion(): bool
    {
        $project = (array)$this->getView()->get('project');
        $apiVersion = Hash::get($project, 'version');
        if (empty($apiVersion)) {
            return true;
        }
        $requiredApiVersions = (array)Configure::read('BEditaAPI.versions');
        foreach ($requiredApiVersions as $requiredApiVersion) {
            $apiMajor = substr($apiVersion, 0, strpos($apiVersion, '.'));
            $requiredApiMajor = substr($requiredApiVersion, 0, strpos($requiredApiVersion, '.'));
            if ($apiMajor === $requiredApiMajor && version_compare($apiVersion, $requiredApiVersion) >= 0) {
                return true;
            }
        }

        return false;
    }
}
