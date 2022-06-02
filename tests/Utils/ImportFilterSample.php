<?php
namespace App\Test\Utils;

use App\Core\Filter\ImportFilter;
use App\Core\Result\ImportResult;

/**
 * Test sample filter
 */
class ImportFilterSample extends ImportFilter
{
    /**
     * @inheritDoc
     */
    public function import($filename, $filepath, ?array $options = []): ImportResult
    {
        return new ImportResult($filename, 10, 0, 0, 'ok', '', ''); // ($created, $updated, $errors, $info, $warn, $error)
    }

    /**
     * @inheritDoc
     */
    public static function getServiceName(): ?string
    {
        return 'ImportFilterSampleService';
    }
}
