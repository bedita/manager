<?php
namespace App\Test\Utils;

use App\Core\Filter\ImportFilter;
use App\Core\Result\ImportResult;

/**
 * Dummy filter for test
 *
 * @codeCoverageIgnore
 */
class DummyImportFilter extends ImportFilter
{
    /**
     * @inheritDoc
     */
    protected static $serviceName = '';

    /**
     * @inheritDoc
     * @param string $filename The file name
     * @param string $filepath The file path
     * @param array $options The import options
     * @return \App\Core\Result\ImportResult The result
     */
    public function import($filename, $filepath, ?array $options = []): ImportResult
    {
        return $this->createAsyncJob($filename, $filepath, $options);
    }

    /**
     * Call parent, to bypass method protection, for test purpose
     *
     * @param string $filename The file name
     * @param string $filepath The file path
     * @param array $options The options
     * @return \App\Core\Result\ImportResult
     * @throws \LogicException When method is called but missing the async job service name.
     */
    public function createAsyncJob($filename, $filepath, ?array $options = []): ImportResult
    {
        return parent::createAsyncJob($filename, $filepath, $options);
    }
}
