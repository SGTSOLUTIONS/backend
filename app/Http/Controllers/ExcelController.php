<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelController extends Controller
{
    public function processExcelFiles()
    {
        $folder = storage_path('app/excels');
        $files  = glob($folder . '/*.xlsx');

        $summaryData = [['Filename', 'Category', 'Count']];

        foreach ($files as $file) {
            echo "🔄 Processing: $file\n";

            $spreadsheet = IOFactory::load($file);
            $sheet       = $spreadsheet->getActiveSheet();
            $highestRow  = $sheet->getHighestRow();
            $highestCol  = $sheet->getHighestColumn();
            $header      = $sheet->rangeToArray("A1:$highestCol" . '1')[0];

            // 1) Unmerge all existing merged ranges
            foreach ($sheet->getMergeCells() as $range => $_) {
                $sheet->unmergeCells($range);
            }

            // 2) Find "X- Area Variations" column index
            $xColIndex = null;
            foreach ($header as $i => $title) {
                if (stripos($title, 'X- Area') !== false && stripos($title, 'Variation') !== false) {
                    $xColIndex = $i + 1;
                    break;
                }
            }
            if (!$xColIndex) {
                echo "⚠️ Column not found in $file\n";
                continue;
            }

            $newColIndex  = $xColIndex + 1;
            $xColLetter   = Coordinate::stringFromColumnIndex($xColIndex);
            $newColLetter = Coordinate::stringFromColumnIndex($newColIndex);

            // 3) Insert "X-Variation Category" header if not present
            if (!$sheet->getCell($newColLetter . '1')->getValue()) {
                $sheet->insertNewColumnBefore($newColIndex);
                $sheet->setCellValue($newColLetter . '1', 'X-Variation Category');
            }

            // 4) Fill down blanks in that column from the row above
            for ($r = 2; $r <= $highestRow; $r++) {
                if (!$sheet->getCell($newColLetter . $r)->getValue()) {
                    $prev = $sheet->getCell($newColLetter . ($r - 1))->getValue();
                    $sheet->setCellValue($newColLetter . $r, $prev);
                }
            }

            // 5) Sort all data rows by the new column
            $data = $sheet->rangeToArray("A2:" . $sheet->getHighestColumn() . $highestRow, null, true, false);
            $sortKey = $newColIndex - 1; // zero‑based
            usort($data, fn($a, $b) => strcmp($a[$sortKey], $b[$sortKey]));

            // remove old rows and re‑insert sorted
            $sheet->removeRow(2, $highestRow - 1);
            $sheet->fromArray($data, null, 'A2');

            // 6) Fill down any blanks again (just in case)
            for ($r = 2; $r <= $highestRow; $r++) {
                if (!$sheet->getCell($newColLetter . $r)->getValue()) {
                    $prev = $sheet->getCell($newColLetter . ($r - 1))->getValue();
                    $sheet->setCellValue($newColLetter . $r, $prev);
                }
            }

            // 7) Build summary counts
            $counts = [];
            for ($r = 2; $r <= $highestRow; $r++) {
                $cat = $sheet->getCell($newColLetter . $r)->getValue();
                if ($cat) {
                    $counts[$cat] = ($counts[$cat] ?? 0) + 1;
                }
            }
            foreach ($counts as $cat => $cnt) {
                $summaryData[] = [basename($file), $cat, $cnt];
            }

            // 8) Save updated workbook
            $newFile = str_replace('.xlsx', '_updated.xlsx', $file);
            (new Xlsx($spreadsheet))->save($newFile);
            echo "✅ Saved: $newFile\n";
        }

        // 9) Write summary.xlsx
        $summarySheet = new Spreadsheet();
        $summarySheet->getActiveSheet()->fromArray($summaryData, null, 'A1');
        (new Xlsx($summarySheet))->save(storage_path('app/excels/summary.xlsx'));

        return "🎉 All files processed and summary.xlsx created.";
    }

    private function categorizeXVariation($value)
    {
        if (empty($value) || strtoupper(trim($value)) === 'N/A') {
            return 'No MIS Area';
        }
        try {
            $x = floatval(str_replace('x', '', strtolower(trim($value))));
            if ($x <= 0.5) return '0.5';
            if ($x <= 1.0) return '1.0';
            if ($x <= 1.5) return '1.5';
            if ($x <= 2.0) return '2.0';
            if ($x <= 2.5) return '2.5';
            if ($x <= 3.0) return '3.0';
            if ($x <= 3.5) return '3.5';
            if ($x <= 4.0) return '4.0';
            return '> 4.0';
        } catch (\Exception $e) {
            return 'Invalid';
        }
    }
}
