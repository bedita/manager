<?php
namespace App\Test\Utils;

/**
 * MyDummy import filter for test
 *
 * @codeCoverageIgnore
 */
class MyDummyImportFilter extends DummyImportFilter
{
    /**
     * @inheritDoc
     */
    protected static $serviceName = 'My.Dummy.Import.Service.Class';
}
