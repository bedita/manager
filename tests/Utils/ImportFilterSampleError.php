<?php
namespace App\Test\Utils;

use App\Core\Filter\ImportFilter;
use App\Core\Result\ImportResult;
use Cake\Http\Exception\InternalErrorException;

/**
 * Test sample filter
 */
class ImportFilterSampleError extends ImportFilter
{
    /**
     * @inheritDoc
     */
    public function import($filename, $filepath, ?array $options = []): ImportResult
    {
        throw new InternalErrorException('An expected exception');
    }
}
