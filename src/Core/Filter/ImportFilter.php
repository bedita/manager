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
use BEdita\SDK\BEditaApiClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Log\LogTrait;

/**
 * Import abstract class
 */
abstract class ImportFilter
{
    use LogTrait;

    /**
     * BEdita Api client
     *
     * @var \BEdita\SDK\BEditaApiClient
     */
    protected $apiClient = null;

    /**
     * Constructor
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->apiClient = ApiClientProvider::getApiClient();
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
    abstract public function import($filename, $filepath, ?array $options = []) : ImportResult;
}
