<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\ExportComponent;
use App\Test\TestCase\Controller\AppControllerTest;
use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Component\ExportComponent} Test Case
 *
 * @coversDefaultClass \App\Controller\Component\ExportComponent
 */
class ExportComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Component\ExportComponent
     */
    public $Export;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->Export = new ExportComponent($registry);
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Export);

        parent::tearDown();
    }

    /**
     * Provider for `testCheckFormat`.
     *
     * @return array
     */
    public function checkFormatProvider(): array
    {
        return [
            'false' => [
                'pdf',
                false,
            ],
            'true' => [
                'ods',
                true,
            ],
        ];
    }

    /**
     * Test `checkFormat` method
     *
     * @return void
     * @covers ::checkFormat()
     * @dataProvider checkFormatProvider()
     */
    public function testCheckFormat(string $format, bool $expected): void
    {
        $actual = $this->Export->checkFormat($format);
        static::assertEquals($expected, $actual);
    }

    /**
     * Provider for `testFormat`.
     *
     * @return array
     */
    public function formatProvider(): array
    {
        return [
            '1 A' => [
                'csv', // format
                [['Name', 'Surname'], ['John', 'Doe'], ['Anna', 'Doe']], // rows
                'test.csv', // filename
                ['title' => 'CSV test file'], // properties
                [
                    'content' => '"Name","Surname"' . "\n" . '"John","Doe"' . "\n" . '"Anna","Doe"' . "\n",
                    'contentType' => 'text/csv',
                ], // expected
            ],
        ];
    }

    /**
     * Test `format` method
     *
     * @return void
     * @covers ::format()
     * @covers ::csv()
     * @dataProvider formatProvider()
     */
    public function testFormat(string $format, array $rows, string $filename, array $properties, array $expected): void
    {
        $actual = $this->Export->format($format, $rows, $filename, $properties);
        static::assertEquals($expected, $actual);
    }

    /**
     * Provider for `testColumn`.
     *
     * @return array
     */
    public function columnProvider(): array
    {
        return [
            '1 A' => [
                1,
                'A',
            ],
            '26 Z' => [
                26,
                'Z',
            ],
            '27 AA' => [
                27,
                'AA',
            ],
            '52 AZ' => [
                52,
                'AZ',
            ],
            '53 BA' => [
                53,
                'BA',
            ],
        ];
    }

    /**
     * Test `column` method
     *
     * @param int $number The column number
     * @param string $expected The column string
     * @return void
     * @covers ::column()
     * @dataProvider columnProvider()
     */
    public function testColumn(int $number, string $expected): void
    {
        // call protected method using AppControllerTest->invokeMethod
        $test = new AppControllerTest();
        $actual = $test->invokeMethod($this->Export, 'column', [$number]);
        static::assertEquals($expected, $actual);
    }

    /**
     * Provider for `testFormats()`.
     *
     * @return array
     */
    public function formatsProvider(): array
    {
        return [
            'csv' => [
                'csv',
                'text/csv',
            ],
            'ods' => [
                'ods',
                'application/vnd.oasis.opendocument.spreadsheet',
            ],
            'xlsx' => [
                'xlsx',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ],
        ];
    }

    /**
     * Test `csv`, `ods`, `xlsx` methods
     *
     * @param string $format The content type format
     * @param string $contentType The content type
     * @return void
     * @covers ::csv()
     * @covers ::ods()
     * @covers ::xlsx()
     * @covers ::download()
     * @dataProvider formatsProvider()
     */
    public function testFormats(string $format, string $contentType): void
    {
        $filename = sprintf('test.%s', $format);
        $actual = $this->Export->format($format, [], $filename, []);
        $options = compact('filename', 'format');
        static::assertEquals($contentType, $actual['contentType']);
    }
}
