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
namespace App\Core\Filter;

use App\Core\Result\ImportResult;
use BEdita\WebTools\ApiClientProvider;
use Cake\Filesystem\File;
use Cake\Log\LogTrait;
use Cake\Utility\Hash;

/**
 * Import abstract class
 */
abstract class ImportFilter
{
    use LogTrait;

    /**
     * BEdita Api client
     *
     * @var \BEdita\SDK\BEditaClient
     */
    protected $apiClient = null;

    /**
     * Filter import result
     *
     * @var \App\Core\Result\ImportResult
     */
    protected $result = null;

    /**
     * The service name used by async job.
     * If defined the import is intended as asynchronous.
     *
     * @var string
     */
    protected static $serviceName = null;

    /**
     * Constructor
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->apiClient = ApiClientProvider::getApiClient();
        $this->result = new ImportResult();
    }

    /**
     * Return the service name of associated async job.
     *
     * @return string|null
     * @codeCoverageIgnore
     */
    public static function getServiceName(): ?string
    {
        return static::$serviceName;
    }

    /**
     * Import function.
     * Elaborate a file content, using options (if available); return a ImportResult result.
     *
     * @param string $filename The file name
     * @param string $filepath The file path
     * @param array $options The import options
     * @return \App\Core\Result\ImportResult The result
     */
    abstract public function import($filename, $filepath, ?array $options = []): ImportResult;

    /**
     * Upload file used to import data and create async job linking to it.
     *
     * @param string $filename The file name
     * @param string $filepath The file path
     * @param array $options The options
     * @return \App\Core\Result\ImportResult
     * @throws \LogicException When method is called but missing the async job service name.
     */
    protected function createAsyncJob($filename, $filepath, ?array $options = []): ImportResult
    {
        if (empty(static::getServiceName())) {
            throw new \LogicException('Cannot create async job without service name defined.');
        }

        // upload file to import
        $file = new File($filepath);
        $headers = ['Content-Type' => $file->mime()];
        $result = $this->apiClient->upload($filename, $filepath, $headers);

        // create async_job
        $body = [
            'data' => [
                'type' => 'async_jobs',
                'attributes' => [
                    'service' => static::getServiceName(),
                    'payload' => [
                        'streamId' => Hash::get($result, 'data.id'),
                        'filename' => $filename,
                        'options' => (array)$options,
                    ],
                ],
            ],
        ];

        $asyncJob = $this->apiClient->post('/admin/async_jobs', json_encode($body));

        $this->result->addMessage('info', (string)__('Job {0} to import file "{1}" scheduled.', Hash::get($asyncJob, 'data.id'), $filename));

        return $this->result;
    }
}
