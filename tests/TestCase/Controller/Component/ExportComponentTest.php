<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\ExportComponent;
use App\Test\TestCase\Controller\AppControllerTest;
use Cake\Controller\ComponentRegistry;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

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
        $this->Export = new ExportComponent($registry);
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
        $spreadsheet = $this->Export->spreadsheet([['name', 'surname'], ['John', 'Doe'], ['Johanna', 'Doe']], ['title' => 'Test Spreadsheet']);
        $actual = get_class($spreadsheet);
        $expected = \PhpOffice\PhpSpreadsheet\Spreadsheet::class;
        static::assertEquals($expected, $actual);
    }

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
     * @param integer $number The column number
     * @param string $expected The column string
     * @return void
     * @covers ::column()
     * @dataProvider columnProvider()
     */
    public function testColumn(int $number, string $expected): void
    {
        // call protected method using AppControllerTest->invokeMethod
        $test = new AppControllerTest(new ServerRequest());
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
     * @param string $method The method to call
     * @param string $format The content type format
     * @return void
     * @covers ::csv()
     * @covers ::ods()
     * @covers ::xlsx()
     * @covers ::download()
     * @dataProvider formatsProvider()
     */
    public function testFormats(string $method, string $format): void
    {
        $spreadsheet = $this->Export->spreadsheet([], []);
        $filename = sprintf('test.%s', $method);
        $actual = $this->Export->{$method}($spreadsheet, $filename);
        $options = compact('filename', 'format');
        $extension = ucfirst($method);
        static::assertEquals($format, $actual['contentType']);
    }
}
