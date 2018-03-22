<?php

namespace App\Core\Filter;

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
     * @param BEditaApiClient|null $apiClient the api client
     */
    public function __construct($apiClient = null)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Import function.
     * Elaborate a file content, using options (if available); return a ImportResult result.
     *
     * @param string $filename The file name
     * @param string $filepath The file path
     * @param array $options The import options
     * @return App\Core\Result\ImportResult The result
     */
    public function import($filename, $filepath, ?array $options = []) : ImportResult
    {
        return new ImportResult($filename, 0, 0, '', '');
    }
}
