<?php

namespace App\Core\Filter;

use App\ApiClientProvider;
use App\Core\Result\ImportResult;
use BEdita\SDK\BEditaApiClient;

/**
 * Import abstract class
 */
abstract class ImportFilter
{
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
