<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Http\Response;
use Cake\Utility\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;

/**
 * Export component
 */
class ExportComponent extends Component
{
    /**
     * Spreadsheet columns int/letter mapping.
     *
     * @var array
     */
    public const LETTERS = [
        1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D', 5 => 'E', 6 => 'F', 7 => 'G', 8 => 'H', 9 => 'I', 10 => 'J',
        11 => 'K', 12 => 'L', 13 => 'M', 14 => 'N', 15 => 'O', 16 => 'P', 17 => 'Q', 18 => 'R', 19 => 'S', 20 => 'T',
        21 => 'U', 22 => 'V', 23 => 'W', 24 => 'X', 25 => 'Y', 26 => 'Z',
    ];

    /**
     * Default properties for spreadsheet
     *
     * @var array
     */
    protected $defaultSpreadheetProperties = [
        'creator' => 'BEdita Manager',
        'lastModifiedBy' => 'BEdita Manager',
        'title' => '',
        'subject' => '',
        'description' => '',
        'keywords' => '',
        'category' => '',
    ];

    /**
     * Create spreadsheet with data from rows, using properties.
     *
     * @param array $rows THe data
     * @param array $properties The properties
     * @return Spreadsheet
     */
    public function spreadsheet(array $rows, array $properties = []): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();

        // set properties
        $properties = array_merge($this->defaultSpreadheetProperties, $properties);
        foreach ($properties as $property => $value) {
            $spreadsheet->getProperties()->{sprintf('set%s', ucfirst($property))}($value);
        }

        // create the worksheet
        $spreadsheet->setActiveSheetIndex(0);

        // set first row, column names
        if (!empty($rows)) {
            $first = array_values(array_shift($rows));
            foreach ($first as $k => $v) {
                $spreadsheet->getActiveSheet()->setCellValue(sprintf('%s1', self::LETTERS[$k + 1]), $v);
            }
        }

        // fill data
        $spreadsheet->getActiveSheet()->fromArray($rows, null, 'A2');

        // set autofilter
        $spreadsheet->getActiveSheet()->setAutoFilter($spreadsheet->getActiveSheet()->calculateWorksheetDimension());

        return $spreadsheet;
    }

    /**
     * Create spreadsheet file into memory and redirect output to download it as csv
     *
     * @param Spreadsheet $spreadsheet The spreadsheet
     * @param string $filename The file name
     * @return void
     */
    public function csv(Spreadsheet $spreadsheet, string $filename = 'export.csv'): void
    {
        $options = compact('filename') + ['format' => 'text/csv'];
        $this->download($spreadsheet, 'Csv', $options);
    }

    /**
     * Create spreadsheet file into memory and redirect output to download it as ods
     *
     * @param Spreadsheet $spreadsheet The spreadsheet
     * @param string $filename The file name
     * @return void
     */
    public function ods(Spreadsheet $spreadsheet, string $filename = 'export.ods'): void
    {
        $options = compact('filename') + ['format' => 'application/vnd.oasis.opendocument.spreadsheet'];
        $this->download($spreadsheet, 'Ods', $options);
    }

    /**
     * Create spreadsheet file into memory and redirect output to download it as pdf
     *
     * @param Spreadsheet $spreadsheet The spreadsheet
     * @param string $filename The file name
     * @return void
     */
    public function pdf(Spreadsheet $spreadsheet, string $filename = 'export.pdf'): void
    {
        IOFactory::registerWriter('Pdf', Mpdf::class);
        $spreadsheet->getActiveSheet()->setShowGridLines(false);
        $spreadsheet->getActiveSheet()->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $spreadsheet->getActiveSheet()->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $options = compact('filename') + ['format' => 'application/pdf'];
        $this->download($spreadsheet, 'Pdf', $options);
    }

    /**
     * Create spreadsheet file into memory and redirect output to download it as xls
     *
     * @param Spreadsheet $spreadsheet The spreadsheet
     * @param string $filename The file name
     * @return void
     */
    public function xls(Spreadsheet $spreadsheet, string $filename = 'export.xls'): void
    {
        $options = compact('filename') + ['format' => 'application/vnd.ms-excel'];
        $this->download($spreadsheet, 'Xls', $options);
    }

    /**
     * Create spreadsheet file into memory and redirect output to download it as xlsx
     *
     * @param Spreadsheet $spreadsheet The spreadsheet
     * @param string $filename The file name
     * @return void
     */
    public function xlsx(Spreadsheet $spreadsheet, string $filename = 'export.xlsx'): void
    {
        $options = compact('filename') + ['format' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
        $this->download($spreadsheet, 'Xlsx', $options);
    }

    /**
     * Just exit. Useful having a separated function, to better handling unit tests.
     *
     * @return void
     * @codeCoverageIgnore
     */
    public function exit(): void
    {
        exit;
    }

    /**
     * Create spreadsheet and set it as download.
     *
     * @param Spreadsheet $spreadsheet The spreadsheet
     * @param string $extension The extension, can be 'Csv', 'Ods', 'Pdf', 'Xls', 'Xlsx'
     * @param array $options The options
     * @return void
     * @codeCoverageIgnore
     */
    protected function download(Spreadsheet $spreadsheet, string $extension, array $options): void
    {
        // Redirect output to a client’s web browser (Xlsx)
        header(sprintf('Content-Type: %s', Hash::get($options, 'format')));
        header(sprintf('Content-Disposition: attachment;filename="%s"', Hash::get($options, 'filename')));
        header(sprintf('Cache-Control: max-age=%d', Hash::get($options, 'max-age', 0)));

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, $extension);
        $writer->save('php://output');
    }
}
