<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\ExportComponent;
use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Test utility class, to mock some methods
 */
class MyExportComponent extends ExportComponent
{
    protected $test = null;
    public function download(Spreadsheet $spreadsheet, string $extension, array $options): void
    {
        $this->test = compact('spreadsheet', 'extension', 'options');
    }
    public function exit(): void
    {
        // do nothing
    }
}

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
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->Export = new MyExportComponent($registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Export);

        parent::tearDown();
    }

    /**
     * Test `spreadsheet` method
     *
     * @covers ::spreadsheet()
     * @return void
     */
    public function testSpreadsheet(): void
    {
        $spreadsheet = $this->Export->spreadsheet([], []);
        $actual = get_class($spreadsheet);
        $expected = \PhpOffice\PhpSpreadsheet\Spreadsheet::class;
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
            'pdf' => [
                'pdf',
                'application/pdf',
            ],
            'xls' => [
                'xls',
                'application/vnd.ms-excel',
            ],
            'xlsx' => [
                'xlsx',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ],
        ];
    }

    /**
     * Test `csv`, `ods`, `pdf`, `xls` and `xlsx` method
     *
     * @covers ::csv()
     * @covers ::ods()
     * @covers ::pdf()
     * @covers ::xls()
     * @covers ::xlsx()
     * @dataProvider formatsProvider()
     * @return void
     */
    public function testFormats(string $method, string $format): void
    {
        $spreadsheet = $this->Export->spreadsheet([], []);
        $filename = sprintf('test.%s', $method);
        $this->Export->{$method}($spreadsheet, $filename);
        $options = compact('filename', 'format');
        $extension = ucfirst($method);
        $expected = compact('spreadsheet', 'extension', 'options');
        $actual = $this->Export->test;
        static::assertEquals($expected, $actual);
    }
}
