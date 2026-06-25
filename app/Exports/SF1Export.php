<?php

namespace App\Exports;

use App\Models\StudentEnrollment;
use App\Models\Section;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class SF1Export implements FromView, WithEvents, ShouldAutoSize
{
    protected $academicYearId, $semesterId, $sectionId;
    protected $studentsCount = 0;
    protected $studentsMale = 0;
    protected $studentsFemale = 0;

    public function __construct($academicYearId, $semesterId, $sectionId)
    {
        $this->academicYearId = $academicYearId;
        $this->semesterId = $semesterId;
        $this->sectionId = $sectionId;
    }

    public function view(): View
    {
        $students = StudentEnrollment::with('student')
            ->where('section_id', $this->sectionId)
            ->where('semester_id', $this->semesterId)
            ->whereNull('date_archived')
            ->get()
            ->sortBy(fn($e) => ($e->student->gender === 'Male' ? 1 : 2) . $e->student->last_name);

        $this->studentsCount = $students->count();

        $this->studentsMale = $students->filter(fn($s) => strtolower($s->student->gender ?? '') === 'male')->count();
        $this->studentsFemale = $students->filter(fn($s) => strtolower($s->student->gender ?? '') === 'female')->count();

        $schoolId = "305834";
        $schoolName = "Talavera Senior High School";
        $sectionData = Section::with('track_strand', 'adviser')->find($this->sectionId);
        $strandName  = $sectionData->track_strand->strand ?? '';
        $trackName   = $sectionData->track_strand->track ?? '';

        if ($sectionData && $sectionData->adviser) {
            $adviser = $sectionData->adviser;
            $this->adviserName = trim("{$adviser->first_name} {$adviser->middle_name} {$adviser->last_name}");
        } else {
            $this->adviserName = 'No Adviser';
        }

        $this->semesterStartDate = Semester::find($this->semesterId)->start_date ?? '';
        $this->semesterEndDate = Semester::find($this->semesterId)->end_date ?? '';

        return view('exports.sf1', [
            'students'     => $students,
            'schoolId'     => $schoolId,
            'schoolName'   => $schoolName,
            'semesterName' => Semester::find($this->semesterId)->name ?? '',
            'section'      => $sectionData->section_name ?? '',
            'gradeLevel'   => $sectionData->grade_level ?? '',
            'strandName'   => $strandName,
            'trackName'    => $trackName,
            'acadName'     => AcademicYear::find($this->academicYearId)->name ?? '',
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // === LOGOS ===
                $leftLogo = new Drawing();
                $leftLogo->setPath(public_path('images/kagawaran.png'));
                $leftLogo->setWidthAndHeight(120, 80);
                $leftLogo->setCoordinates('A1');
                $leftLogo->setWorksheet($sheet);

                $rightLogo = new Drawing();
                $rightLogo->setPath(public_path('images/deped.png'));
                $rightLogo->setWidthAndHeight(120, 80);
                $rightLogo->setCoordinates('P1');
                $rightLogo->setWorksheet($sheet);

                // === HEADER TITLE ===
                $sheet->mergeCells('C1:Z1');
                $sheet->setCellValue('C1', 'School Form 1 (SF1) School Register for Senior High School');
                $sheet->getStyle('C1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                ]);

                // === DEFAULT ROW SETTINGS ===
                $sheet->getDefaultRowDimension()->setRowHeight(20);

                // === APPLY WRAP TEXT + CENTER TO ALL HEADER CELLS ===
                $sheet->getStyle('A8:Q9')->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                    'font' => [
                        'bold' => true,
                        'size' => 6,
                    ],
                ]);

                // === ROTATE "SEX" HEADER ===
                $sheet->getStyle('C9')->getAlignment()
                    ->setTextRotation(-90)
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getColumnDimension('C')->setWidth(5);

                // === AUTO HEIGHT FOR HEADER ROWS ===
                $sheet->getRowDimension(8)->setRowHeight(-1);
                $sheet->getRowDimension(9)->setRowHeight(-1);

                // === COLUMN WIDTHS ===
                $columnWidths = [
                    'A' => 15, 'B' => 35, 'C' => 5, 'D' => 12, 'E' => 8,
                    'F' => 15, 'G' => 18, 'H' => 15, 'I' => 15, 'J' => 15,
                    'K' => 20, 'L' => 20, 'M' => 20, 'N' => 10, 'O' => 15,
                    'P' => 10, 'Q' => 12,
                ];
                foreach ($columnWidths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                    $sheet->getColumnDimension($col)->setAutoSize(false);
                }

                // === LEGEND SECTION START ===
                $combinedTotalRow = $this->studentsCount + 12;
                $legendHeaderRow = $combinedTotalRow + 2;

                // Legend Title
                $sheet->mergeCells("A{$legendHeaderRow}:H{$legendHeaderRow}");
                $sheet->setCellValue("A{$legendHeaderRow}", "Legend: List and Code of Indicators under REMARKS column");
                $sheet->getStyle("A{$legendHeaderRow}")->applyFromArray([
                    'font' => ['bold' => true],
                ]);

                // --- Legend Table Header ---
                $legendTableHeaderRow = $legendHeaderRow + 1;
                $sheet->mergeCells("A{$legendTableHeaderRow}:A{$legendTableHeaderRow}");
                $sheet->mergeCells("B{$legendTableHeaderRow}:B{$legendTableHeaderRow}");
                $sheet->mergeCells("C{$legendTableHeaderRow}:D{$legendTableHeaderRow}"); // Required Info wider
                $sheet->mergeCells("E{$legendTableHeaderRow}:E{$legendTableHeaderRow}");
                $sheet->mergeCells("F{$legendTableHeaderRow}:F{$legendTableHeaderRow}");
                $sheet->mergeCells("G{$legendTableHeaderRow}:H{$legendTableHeaderRow}"); // wider

                $sheet->setCellValue("A{$legendTableHeaderRow}", "Indicator");
                $sheet->setCellValue("B{$legendTableHeaderRow}", "Code");
                $sheet->setCellValue("C{$legendTableHeaderRow}", "Required Information");
                $sheet->setCellValue("E{$legendTableHeaderRow}", "Indicator");
                $sheet->setCellValue("F{$legendTableHeaderRow}", "Code");
                $sheet->setCellValue("G{$legendTableHeaderRow}", "Required Information");

                $sheet->getStyle("A{$legendTableHeaderRow}:H{$legendTableHeaderRow}")->applyFromArray([
                    'font' => ['bold' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // --- Legend Data ---
                $legendDataStart = $legendTableHeaderRow + 1;
                $legendDataEnd = $legendDataStart + 3;

                $sheet->mergeCells("A{$legendDataStart}:A{$legendDataEnd}");
                $sheet->mergeCells("B{$legendDataStart}:B{$legendDataEnd}"); // B to be centered
                $sheet->mergeCells("C{$legendDataStart}:D{$legendDataEnd}"); // Required Info
                $sheet->mergeCells("E{$legendDataStart}:E{$legendDataEnd}");
                $sheet->mergeCells("F{$legendDataStart}:F{$legendDataEnd}");
                $sheet->mergeCells("G{$legendDataStart}:H{$legendDataEnd}");

                $sheet->setCellValue("A{$legendDataStart}", "Transferred Out\n\nTransferred In");
                $sheet->setCellValue("B{$legendDataStart}", "T/O\n\nT/I");
                $sheet->setCellValue("C{$legendDataStart}", "Name of School, Date of 1st Attendance \nand Date of Last Attendance if Transferred Out");
                $sheet->setCellValue("E{$legendDataStart}", "CCT Recipient\n\nBalik Aral\n\nSpecial Needs Education\n\nAccelerated");
                $sheet->setCellValue("F{$legendDataStart}", "CCT\n\nB/A\n\nSN\n\nACL");
                $sheet->setCellValue("G{$legendDataStart}", "CCT Control/reference number & Effectivity Date\n\nName of School last attended & Year\n\nSpecify Exceptionality of the Learner\n\nSpecify Level & Effectivity Date");

                // --- Apply styles with bold font ---
                $sheet->getStyle("A{$legendDataStart}:H{$legendDataEnd}")->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'font' => ['size' => 6, 'bold' => true], // <-- added bold
                ]);

                // --- Center column B separately ---
                $sheet->getStyle("B{$legendDataStart}:B{$legendDataEnd}")
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


                // --- Center column B separately ---
                $sheet->getStyle("B{$legendDataStart}:B{$legendDataEnd}")
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


                // =========================
                // REGISTERED SUMMARY (I–K)
                // =========================
                $summaryHeaderRow = $legendHeaderRow;


// Column headers
$registeredHeaderRow = $legendTableHeaderRow;
$sheet->fromArray(
    [["Registered", "Beginning of Semester", "End of Semester"]],
    null,
    "J{$registeredHeaderRow}"
);
$sheet->getStyle("J{$registeredHeaderRow}:L{$registeredHeaderRow}")->applyFromArray([
    'font' => ['bold' => true],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER,
    ],
]);

// Data rows
$summaryDataStart = $registeredHeaderRow + 1;
$summaryData = [
    ["MALE",   $this->studentsMale ?? 0, ""],
    ["FEMALE", $this->studentsFemale ?? 0, ""],
    ["TOTAL",  ($this->studentsMale + $this->studentsFemale) ?? 0, ""],
];

foreach ($summaryData as $i => $rowData) {
    $r = $summaryDataStart + $i;
    $sheet->fromArray([$rowData], null, "J{$r}");
    
    // Bold lang sa first column (MALE, FEMALE, TOTAL)
    $sheet->getStyle("J{$r}")->applyFromArray([
        'font' => ['bold' => true],
    ]);

    // Borders at alignment sa buong row
    $sheet->getStyle("J{$r}:L{$r}")->applyFromArray([
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical'   => Alignment::VERTICAL_CENTER,
        ],
    ]);
}




           // =========================
// PREPARED BY (L–N)
// =========================
$prepStartRow = $summaryHeaderRow;

// "Prepared by:" (bold)
$sheet->mergeCells("N{$prepStartRow}:Q{$prepStartRow}");
$sheet->setCellValue("N{$prepStartRow}", "Prepared by:");
$sheet->getStyle("N{$prepStartRow}")->getFont()->setBold(true);

// Adviser name (underline only, not bold)
$adviserName = "                      " . ($this->adviserName ?? "") . "                      ";
$sheet->mergeCells("N" . ($prepStartRow + 1) . ":Q" . ($prepStartRow + 1));
$sheet->setCellValue("N" . ($prepStartRow + 1), $adviserName);
$sheet->getStyle("N" . ($prepStartRow + 1)) // top-left cell only
      ->getFont()
      ->setUnderline(\PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE);
$sheet->getStyle("N" . ($prepStartRow + 1) . ":Q" . ($prepStartRow + 1))
      ->getAlignment()
      ->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Signature text (bold)
$sheet->mergeCells("N" . ($prepStartRow + 2) . ":Q" . ($prepStartRow + 2));
$sheet->setCellValue("N" . ($prepStartRow + 2), "(Signature of Adviser over Printed Name)");
$sheet->getStyle("N" . ($prepStartRow + 2))->getFont()->setBold(true);
$sheet->getStyle("N" . ($prepStartRow + 2) . ":Q" . ($prepStartRow + 2))
      ->getAlignment()
      ->setHorizontal(Alignment::HORIZONTAL_CENTER);

// =========================
// SEMESTER DATES SIDE-BY-SIDE
// =========================
$semesterLabelRow = $prepStartRow + 3;
$semesterDateRow  = $prepStartRow + 4;

$beginningDate = $this->semesterStartDate 
    ? \Carbon\Carbon::parse($this->semesterStartDate)->format('d/m/Y') . ' 12:00 AM' 
    : '';

$endDate = $this->semesterEndDate 
    ? \Carbon\Carbon::parse($this->semesterEndDate)->format('d/m/Y') . ' 12:00 AM' 
    : '';

// Beginning of Semester label (bold)
$sheet->mergeCells("N{$semesterLabelRow}:O{$semesterLabelRow}");
$sheet->setCellValue("N{$semesterLabelRow}", "Beginning of the Semester   Date:");
$sheet->getStyle("N{$semesterLabelRow}")->getFont()->setBold(true);

// Beginning of Semester date (bold)
$sheet->mergeCells("N{$semesterDateRow}:O{$semesterDateRow}");
$sheet->setCellValue("N{$semesterDateRow}", $beginningDate);
$sheet->getStyle("N{$semesterDateRow}:O{$semesterDateRow}")->applyFromArray([
    'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER,
    ],
]);


// End of Semester label (bold)
$sheet->mergeCells("P{$semesterLabelRow}:Q{$semesterLabelRow}");
$sheet->setCellValue("P{$semesterLabelRow}", "End of the Semester   Date:");
$sheet->getStyle("P{$semesterLabelRow}")->getFont()->setBold(true);

// End of Semester date (bold)
$sheet->mergeCells("P{$semesterDateRow}:Q{$semesterDateRow}");
$sheet->setCellValue("P{$semesterDateRow}", $endDate);
$sheet->getStyle("P{$semesterDateRow}:Q{$semesterDateRow}")->applyFromArray([
    'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER,
    ],
]);


                // === FONT STYLE ===
                $sheet->getStyle("A{$legendHeaderRow}:Q" . ($semesterDateRow))->applyFromArray([
                    'font' => ['name' => 'Sans Serif', 'size' => 6],
                ]);
            },
        ];
    }
}
