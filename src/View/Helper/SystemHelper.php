<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\View\Helper;

use App\Utility\System;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\View\Helper;

/**
 * System helper
 */
class SystemHelper extends Helper
{
    /**
     * Default placeholders configuration for media types
     *
     * @var array
     */
    protected $defaultPlaceholders = [
        'audio' => ['controls' => 'boolean', 'autoplay' => 'boolean'],
        'files' => ['download' => 'boolean'],
        'images' => ['width' => 'integer', 'height' => 'integer', 'bearing' => 'integer', 'pitch' => 'integer', 'zoom' => 'integer'],
        'videos' => ['controls' => 'boolean', 'autoplay' => 'boolean'],
    ];

    /**
     * Accepted mime types for upload
     *
     * @var array
     */
    protected $defaultUploadAccepted = [
        'audio' => [
            'audio/*',
        ],
        'files' => [
            'application/msword', // .doc, .dot
            'application/pdf', // .pdf
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
            'application/vnd.ms-excel', // .xls, .xlt, .xla
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
            'application/vnd.ms-powerpoint', // .ppt, .pot, .pps, .ppa
            'application/vnd.openxmlformats-officedocument.presentationml.presentation', // .pptx
            'application/x-mpegURL',
            'audio/*',
            'text/csv',
            'text/html',
            'text/plain',
            'text/rtf',
            'text/xml',
            'image/*',
            'video/*',
        ],
        'images' => [
            'image/*',
        ],
        'videos' => [
            'application/x-mpegURL',
            'video/*',
        ],
        'media' => [
            'application/msword', // .doc, .dot
            'application/pdf', // .pdf
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
            'application/vnd.ms-excel', // .xls, .xlt, .xla
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
            'application/vnd.ms-powerpoint', // .ppt, .pot, .pps, .ppa
            'application/vnd.openxmlformats-officedocument.presentationml.presentation', // .pptx
            'application/x-mpegURL',
            'audio/*',
            'image/*',
            'text/csv',
            'text/html',
            'text/plain',
            'text/rtf',
            'text/xml',
            'video/*',
        ],
    ];

    /**
     * Not accepted mime types and extesions for upload
     *
     * @var array
     */
    protected $defaultUploadForbidden = [
        'mimetypes' => [
            'application/javascript',
            'application/x-cgi',
            'application/x-perl',
            'application/x-php',
            'application/x-ruby',
            'application/x-shellscript',
            'text/javascript',
            'text/x-perl',
            'text/x-php',
            'text/x-python',
            'text/x-ruby',
            'text/x-shellscript',
        ],
        'extensions' => [
            'cgi',
            'exe',
            'js',
            'perl',
            'php',
            'py',
            'rb',
            'sh',
        ],
    ];

    /**
     * Maximum resolution for images
     *
     * @var string
     */
    protected $defaultUploadMaxResolution = '4096x2160'; // 4K

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
            if (System::compareBEditaApiVersion($apiVersion, $requiredApiVersion)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if BEdita API version is greater or equal to required version.
     *
     * @param string $requiredApiVersion Required API version
     * @return bool
     */
    public function isBEditaApiVersionGte(string $requiredApiVersion): bool
    {
        $project = (array)$this->getView()->get('project');
        $apiVersion = Hash::get($project, 'version');

        return empty($apiVersion) ? false : version_compare($apiVersion, $requiredApiVersion) >= 0;
    }

    /**
     * Placeholders config
     *
     * @return array
     */
    public function placeholdersConfig(): array
    {
        return (array)Configure::read('Placeholders', $this->defaultPlaceholders);
    }

    /**
     * Upload config
     *
     * @return array
     */
    public function uploadConfig(): array
    {
        $accepted = (array)Configure::read('uploadAccepted', $this->defaultUploadAccepted);
        $forbidden = (array)Configure::read('uploadForbidden', $this->defaultUploadForbidden);
        $maxResolution = (string)Configure::read('uploadMaxResolution', $this->defaultUploadMaxResolution);

        return compact('accepted', 'forbidden', 'maxResolution');
    }

    /**
     * Get alert background color
     *
     * @return string
     */
    public function alertBgColor(): string
    {
        if (Configure::read('Recovery')) {
            return '#FE2F03';
        }
        $request = $this->getView()->getRequest();
        $prefix = $request->getParam('prefix');
        if (Configure::read(sprintf('AlertMessageByArea.%s.color', $prefix))) {
            return Configure::read(sprintf('AlertMessageByArea.%s.color', $prefix));
        }

        return Configure::read('AlertMessage.color') ?? '';
    }

    /**
     * Get alert message
     *
     * @return string
     */
    public function alertMsg(): string
    {
        if (Configure::read('Recovery')) {
            return __('Recovery Mode - Access restricted to admin users');
        }
        $request = $this->getView()->getRequest();
        $prefix = $request->getParam('prefix');
        if (Configure::read(sprintf('AlertMessageByArea.%s.text', $prefix))) {
            return Configure::read(sprintf('AlertMessageByArea.%s.text', $prefix));
        }
        $message = Configure::read('AlertMessage.text') ?? '';
        $user = $this->getView()->get('user');
        if ($user && in_array('admin', $user->get('roles')) && !$this->checkBeditaApiVersion()) {
            $message .= ' ' . __('API version required: {0}', Configure::read('BEditaAPI.versions'));
        }

        return $message;
    }
}
