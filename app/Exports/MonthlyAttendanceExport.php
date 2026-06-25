<?php

namespace App\Exports;

use App\Models\Student;
use App\Models\AttendanceLog;
use App\Models\Section;
use App\Models\StudentEnrollment;
use App\Models\AcademicYear;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class MonthlyAttendanceExport implements FromView, WithEvents, ShouldAutoSize
{
   protected $sectionId;
    protected $month;
    protected $year;
    protected $semesterId; // Now we store semester_id
    protected $studentsCount;
    protected $adviserName;
    protected $holidays;

     public function __construct($sectionId, $month, $academicYearName, $semesterId, $holidays = [])
    {
        $this->sectionId = $sectionId;
        $this->month     = (int) $month;
        $this->year      = (int) $academicYearName;
        $this->semesterId = $semesterId;
        $this->semesterName = $this->determineSemester($this->month);
        $this->holidays = $holidays;
    }

     private function determineSemester($month)
    {
        $month = (int) $month;
        if ($month >= 6 && $month <= 10) {
            return 'First Semester';
        } elseif (($month >= 11 && $month <= 12) || ($month >= 1 && $month <= 3)) {
            return 'Second Semester';
        } else {
            return 'First Semester';
        }
    }

    public function view(): View
    {
        $startOfMonth = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $endOfMonth   = Carbon::create($this->year, $this->month, 1)->endOfMonth();

        $students = StudentEnrollment::with('student')
        ->where('section_id', $this->sectionId)
        ->where('semester_id', $this->semesterId)
        ->get();



        $this->studentsCount = $students->count();


$startOfMonth = \Carbon\Carbon::create($this->year, $this->month, 1, 0, 0, 0, 'Asia/Manila');
$endOfMonth   = $startOfMonth->copy()->endOfMonth()->setTime(23, 59, 59);

$attendances = AttendanceLog::whereHas('enrollment', function ($q) {
        $q->where('section_id', $this->sectionId)
          ->where('semester_id', $this->semesterId)
          ->whereNull('date_archived');
    })
    ->whereBetween('date_time', [
        $startOfMonth->toDateTimeString(),
        $endOfMonth->toDateTimeString()
    ])
    ->selectRaw('enrollment_id, DATE(CONVERT_TZ(`date_time`, "+00:00", "+08:00")) as att_date, status')
    ->orderBy('date_time', 'asc')
    ->get()
    ->groupBy('enrollment_id')
    ->map(function ($logs) {
        return $logs->keyBy('att_date');
    })
    ->toArray();



        // 🔹 School + Section Data
        $schoolId    = "305834";
        $schoolName  = "Talavera Senior High School";
        $monthName   = Carbon::create()->month($this->month)->format('F');
        $daysInMonth = Carbon::create($this->year, $this->month, 1)->daysInMonth;

        $sectionData = Section::with('track_strand', 'adviser')->find($this->sectionId);
        $strandName  = $sectionData->track_strand->strand ?? '';
        $trackName   = $sectionData->track_strand->track ?? '';
        $section     = $sectionData->section_name ?? '';
        $gradeLevel  = $sectionData->grade_level ?? '';
    
          if ($this->month >= 6 && $this->month <= 12) {
            $academicYear = $this->year . '-' . ($this->year + 1);
        } else {
            $academicYear = ($this->year - 1) . '-' . $this->year;
        }

        $adviserName = '';
        if ($sectionData && $sectionData->adviser) {
            $adviser = $sectionData->adviser;
            $middleInitial = $adviser->middle_name ? strtoupper(substr($adviser->middle_name, 0, 1)) . "." : '';
            $adviserName = strtoupper("{$adviser->first_name} {$middleInitial} {$adviser->last_name}");
        }
        $this->adviserName = $adviserName;

        return view('exports.sf2', [
            'students'     => $students,
            'attendances'  => $attendances,
            'schoolId'     => $schoolId,
            'schoolName'   => $schoolName,
            'monthName'    => $monthName,
            'year'         => $this->year,
            'month'        => $this->month,
             'semester'     => $this->semesterName,
            'daysInMonth'  => $daysInMonth,
            'section'      => $section,
            'gradeLevel'   => $gradeLevel,
            'strandName'   => $strandName,
            'trackName'    => $trackName,
            'academicYear' => $academicYear,
            'holidays'     => $this->holidays ?? [],
        ]);
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

               // === LEFT LOGO ===
$leftLogo = new Drawing();
$leftLogo->setName('School Logo');
$leftLogo->setDescription('School Seal');
$leftLogo->setPath(public_path('images/kagawaran.png'));
$leftLogo->setWidthAndHeight(120, 80); // adjust width & height
$leftLogo->setCoordinates('A1'); // leftmost
$leftLogo->setOffsetX(20); // konting space sa gilid
$leftLogo->setWorksheet($sheet);

// === RIGHT LOGO ===
$rightLogo = new Drawing();
$rightLogo->setName('DepEd Logo');
$rightLogo->setDescription('DepEd Seal');
$rightLogo->setPath(public_path('images/deped.png'));
$rightLogo->setWidthAndHeight(120, 80); // adjust width & height
$rightLogo->setCoordinates('AD1'); // rightmost (adjust depende sa columns mo, baka AI1)
$rightLogo->setOffsetX(5);
$rightLogo->setWorksheet($sheet);

// === HEADER TITLE (CENTER) ===
$sheet->mergeCells('C1:Z1'); // adjust depende kung gaano kalapad table
$sheet->setCellValue('C1', 'School Form 2 (SF2) Daily Attendance Report of Learners');

// Style ng header
$sheet->getStyle('C1')->applyFromArray([
    'font' => ['bold' => true, 'size' => 16],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER,
        'wrapText'   => true
    ]
]);
// Taasan yung row height para magkasya logos + header
$sheet->getRowDimension(1)->setRowHeight(40); // adjust px depende sa laki ng logo


                // === Attendance Table ===
$daysInMonth = Carbon::create($this->year, $this->month, 1)->daysInMonth;
$weekdayCount = 0;
for ($d = 1; $d <= $daysInMonth; $d++) {
    $dayName = Carbon::create($this->year, $this->month, $d)->format('D');
    if (!in_array($dayName, ['Sat', 'Sun'])) {
        $weekdayCount++;
    }
}

$totalCols = 2 + $weekdayCount + 2;
$endCol = Coordinate::stringFromColumnIndex($totalCols);

$startTableRow = 12; 
$tableEndRow   = $startTableRow + $this->studentsCount + 2; // includes TOTAL row

// === HEADER ROW ONLY ===
$sheet->getStyle("A{$startTableRow}:{$endCol}{$startTableRow}")->applyFromArray([
    'font' => [
        'name' => 'sans-serif',
        'size' => 7,
    ],
    'alignment' => [

        'vertical' => 'center',
        'wrapText' => true
    ],
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
    ]
]);

// === LEARNER ROWS + TOTALS (general font style) ===
$sheet->getStyle("A" . ($startTableRow+1) . ":{$endCol}{$tableEndRow}")->applyFromArray([
    'font' => [
        'name' => 'Arial', // mas safe kaysa "sans-serif"
        'size' => 7,
    ],
    'alignment' => [
        'vertical' => 'center',
        'wrapText' => true
    ],
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN]
    ]
]);

// Column A (No.) = center
$sheet->getStyle("A" . ($startTableRow+1) . ":A{$tableEndRow}")
    ->getAlignment()->setHorizontal('center');

// Column B (Learner Name) = left
$sheet->getStyle("B" . ($startTableRow+1) . ":B{$tableEndRow}")
    ->getAlignment()->setHorizontal('left');

// Attendance columns (C .. hanggang last date col) = center
$attendanceStartCol = Coordinate::stringFromColumnIndex(3); // C
$attendanceEndCol   = Coordinate::stringFromColumnIndex($totalCols - 2);
$sheet->getStyle("{$attendanceStartCol}" . ($startTableRow+1) . ":{$attendanceEndCol}{$tableEndRow}")
    ->getAlignment()->setHorizontal('center');





// Totals (last 2 columns) = center
$totalStartCol = Coordinate::stringFromColumnIndex($totalCols - 1);
$totalEndCol   = Coordinate::stringFromColumnIndex($totalCols);
$sheet->getStyle("{$totalStartCol}" . ($startTableRow+1) . ":{$totalEndCol}{$tableEndRow}")
    ->getAlignment()->setHorizontal('center');



                // === GUIDELINES (LEFT SIDE) ===
                      // === GUIDELINES START ===
            $guidelinesRowStart = $tableEndRow + 2;

            // Title "GUIDELINES:"
            $sheet->setCellValue("A{$guidelinesRowStart}", "GUIDELINES:");
            $sheet->mergeCells("A{$guidelinesRowStart}:M{$guidelinesRowStart}");
           $sheet->getStyle("A{$guidelinesRowStart}:M{$guidelinesRowStart}")
                 ->getFont()->setBold(true)->setName('sans-serif')->setSize(6);


            // General instructions (1 & 2)
            $sheet->setCellValue("A".($guidelinesRowStart+1), 
                "1. The attendance shall be accomplished daily. Refer to the codes for checking learners’ attendance.");
            $sheet->mergeCells("A".($guidelinesRowStart+1).":M".($guidelinesRowStart+1));

            $sheet->setCellValue("A".($guidelinesRowStart+2), 
                "2. Dates shall be written in the columns after Learner’s Name.");
            $sheet->mergeCells("A".($guidelinesRowStart+2).":M".($guidelinesRowStart+2));

            // Item 3 header
            $sheet->setCellValue("A".($guidelinesRowStart+3), "3. To compute the following:");
            $sheet->mergeCells("A".($guidelinesRowStart+3).":M".($guidelinesRowStart+3));

            // === a. Percentage of Enrolment ===
            $formulaRow = $guidelinesRowStart+5;
            $sheet->setCellValue("A{$formulaRow}", "     a. Percentage of Enrolment =");
            $sheet->mergeCells("A{$formulaRow}:B{$formulaRow}");
            $sheet->setCellValue("C{$formulaRow}", "Registered Learners as of end of the month");
            $sheet->mergeCells("C{$formulaRow}:G{$formulaRow}");

            $sheet->setCellValue("C".($formulaRow+1), "Enrolment as of 1st Friday of the school year");
            $sheet->mergeCells("C".($formulaRow+1).":G".($formulaRow+1));
            $sheet->getStyle("C".($formulaRow+1).":G".($formulaRow+1))->applyFromArray([
                'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN]]
            ]);
            $sheet->setCellValue("H{$formulaRow}", "× 100");

            // === b. Average Daily Attendance ===
            $formulaRow += 3;
            $sheet->setCellValue("A{$formulaRow}", "     b. Average Daily Attendance =");
            $sheet->mergeCells("A{$formulaRow}:B{$formulaRow}");
            $sheet->setCellValue("C{$formulaRow}", "Total Daily Attendance");
            $sheet->mergeCells("C{$formulaRow}:G{$formulaRow}");

            $sheet->setCellValue("C".($formulaRow+1), "Number of School Days in reporting month");
            $sheet->mergeCells("C".($formulaRow+1).":G".($formulaRow+1));
            $sheet->getStyle("C".($formulaRow+1).":G".($formulaRow+1))->applyFromArray([
                'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN]]
            ]);

            // === c. Percentage of Attendance ===
            $formulaRow += 3;
            $sheet->setCellValue("A{$formulaRow}", "     c. Percentage of Attendance for the month =");
            $sheet->mergeCells("A{$formulaRow}:B{$formulaRow}");
            $sheet->setCellValue("C{$formulaRow}", "Average Daily Attendance");
            $sheet->mergeCells("C{$formulaRow}:G{$formulaRow}");

            $sheet->setCellValue("C".($formulaRow+1), "Registered Learners as of end of the month");
            $sheet->mergeCells("C".($formulaRow+1).":G".($formulaRow+1));
            $sheet->getStyle("C".($formulaRow+1).":G".($formulaRow+1))->applyFromArray([
                'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN]]
            ]);
            $sheet->setCellValue("H{$formulaRow}", "× 100");

            // === Continue with guidelines 4, 5, 6 ===
            $nextRow = $formulaRow + 3;
            $sheet->setCellValue("A{$nextRow}", 
                "4. Every end of the month, the class adviser will submit this form to the office of the principal for recording of summary table into the School Form 4. The summary table will be used for submission to the Division Office.");
            $sheet->mergeCells("A{$nextRow}:M{$nextRow}");

            $sheet->setCellValue("A".($nextRow+1), 
                "5. The attendance of learners is for the current school year only.");
            $sheet->mergeCells("A".($nextRow+1).":M".($nextRow+1));

            $sheet->setCellValue("A".($nextRow+2), 
                "6. * Beginning of School Year cut-off report is every 1st Friday of the school year");
            $sheet->mergeCells("A".($nextRow+2).":M".($nextRow+2));

            // Align text
           $sheet->getStyle("A{$guidelinesRowStart}:M".($nextRow+2))->applyFromArray([
                'font' => [
                    'name' => 'sans-serif',
                    'size' => 6
                ],
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => Alignment::VERTICAL_TOP
                ]
            ]);

    

                // === CODES + REASONS (CENTER) ===
$codesRow = $guidelinesRowStart;
$startCol = "N";
$endCol   = "T";

// Title
$sheet->setCellValue("{$startCol}{$codesRow}", "1. CODES FOR CHECKING ATTENDANCE");
$sheet->mergeCells("{$startCol}{$codesRow}:{$endCol}{$codesRow}");
$sheet->getStyle("{$startCol}{$codesRow}:{$endCol}{$codesRow}")
    ->getFont()->setBold(true);

// Codes list
$sheet->setCellValue("{$startCol}".($codesRow+1), "(blank) = Present; (X) = Absent; Tardy (half ");
$sheet->setCellValue("{$startCol}".($codesRow+2), " shaded Upper for Late Comer, Lower for Cutting) ");


// === REASONS/CAUSES ===
$reasonRow = $codesRow + 3;
$sheet->setCellValue("{$startCol}{$reasonRow}", "2. REASONS/CAUSES FOR NLS");
$sheet->mergeCells("{$startCol}{$reasonRow}:{$endCol}{$reasonRow}");
$sheet->getStyle("{$startCol}{$reasonRow}:{$endCol}{$reasonRow}")
    ->getFont()->setBold(true);

// Domestic-Related Factors
$reasonRow++;
$sheet->setCellValue("{$startCol}{$reasonRow}", "a. Domestic-Related Factors");
$sheet->getStyle("{$startCol}{$reasonRow}")->getFont()->setBold(true);
$sheet->setCellValue("N".($reasonRow+1), "a.1. Had to take care of siblings");
$sheet->setCellValue("N".($reasonRow+2), "a.2. Early marriage/pregnancy");
$sheet->setCellValue("N".($reasonRow+3), "a.3. Parents’ attitude toward schooling");
$sheet->setCellValue("N".($reasonRow+4), "a.4. Family problems");

// Individual-Related Factors
$reasonRow += 5; // dati 8 or 6, binawasan
$sheet->setCellValue("{$startCol}{$reasonRow}", "b. Individual-Related Factors");
$sheet->getStyle("{$startCol}{$reasonRow}")->getFont()->setBold(true);
$sheet->setCellValue("N".($reasonRow+1), "b.1. Illness");
$sheet->setCellValue("N".($reasonRow+2), "b.2. Death");
$sheet->setCellValue("N".($reasonRow+3), "b.3. Drug abuse");
$sheet->setCellValue("N".($reasonRow+4), "b.4. Poor academic performance");
$sheet->setCellValue("N".($reasonRow+5), "b.5. Lack of interest/Distractions");
$sheet->setCellValue("N".($reasonRow+6), "b.6. Hunger/Malnutrition");

// School-Related Factors
$reasonRow += 7; // dati 8, binawasan
$sheet->setCellValue("{$startCol}{$reasonRow}", "c. School-Related Factors");
$sheet->getStyle("{$startCol}{$reasonRow}")->getFont()->setBold(true);
$sheet->setCellValue("N".($reasonRow+1), "c.1. Teacher factor");
$sheet->setCellValue("N".($reasonRow+2), "c.2. Physical condition of classroom");
$sheet->setCellValue("N".($reasonRow+3), "c.3. Peer influence");

// Geographical/Environmental
$reasonRow += 4; // dati 5
$sheet->setCellValue("{$startCol}{$reasonRow}", "d. Geographical/Environmental");
$sheet->getStyle("{$startCol}{$reasonRow}")->getFont()->setBold(true);
$sheet->setCellValue("N".($reasonRow+1), "d.1. Distance between home and school ");
$sheet->setCellValue("N".($reasonRow+2), "d.2. Armed conflict (incl. Tribal wars & clan feuds)");
$sheet->setCellValue("N".($reasonRow+3), "d.3. Calamities/Disasters");

// Financial-Related
$reasonRow += 4; // dati 5
$sheet->setCellValue("{$startCol}{$reasonRow}", "e. Financial-Related");
$sheet->getStyle("{$startCol}{$reasonRow}")->getFont()->setBold(true);
$sheet->setCellValue("N".($reasonRow+1), "e.1. Child labor, work");

// Others
$reasonRow += 2; // dati 3
$sheet->setCellValue("{$startCol}{$reasonRow}", "f. Others (Specify)");
$sheet->getStyle("{$startCol}{$reasonRow}")->getFont()->setBold(true);

// === APPLY SINGLE OUTLINE BORDER (BLACK) ===
$endRow = $reasonRow + 1; // mas maiksi na rin
$sheet->getStyle("{$startCol}{$codesRow}:{$endCol}{$endRow}")->applyFromArray([
    'font' => [
        'name' => 'sans-serif',
        'size' => 5
    ],
    'borders' => [
        'outline' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000']
        ]
    ]
]);

// Compress row height (para mas dikit pa lalo)
for ($r = $codesRow; $r <= $endRow; $r++) {
    $sheet->getRowDimension($r)->setRowHeight(12); // default ~15, ginawa 12
}



// === SUMMARY TABLE (Right side) ===
$summaryRow = $guidelinesRowStart; 

// Month Label
$sheet->setCellValue("V{$summaryRow}", "Month :");
$sheet->mergeCells("V{$summaryRow}:W{$summaryRow}");
$sheet->getStyle("V{$summaryRow}:W{$summaryRow}")->applyFromArray([
    'font' => [
        'bold' => true,
        'name' => 'sans-serif',   
        'size' => 6          
    ],
    'alignment' => [
        'horizontal' => 'center',
        'vertical'   => 'center'
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000']
        ]
    ]
]);

// Month Value
$monthName = \Carbon\Carbon::create()->month($this->month)->format('F');
$sheet->setCellValue("V" . ($summaryRow + 1), $monthName);
$sheet->mergeCells("V" . ($summaryRow + 1) . ":W" . ($summaryRow + 1));
$sheet->getStyle("V" . ($summaryRow + 1) . ":W" . ($summaryRow + 1))->applyFromArray([
    'font' => [
        'bold' => true,
        'name' => 'sans-serif',
        'size' => 6
    ],
    'alignment' => [
        'horizontal' => 'center',
        'vertical'   => 'center'
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000']
        ]
    ]
]);

// No. of Days Label (two lines)
$sheet->setCellValue("X{$summaryRow}", "No. of Days of\nClasses");
$sheet->mergeCells("X{$summaryRow}:Y{$summaryRow}");

$sheet->getStyle("X{$summaryRow}:Y{$summaryRow}")->applyFromArray([
    'font' => [
        'bold' => true,
        'name' => 'sans-serif',   
        'size' => 6          
    ],
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        'wrapText'   => true
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000']
        ]
    ]
]);

// Increase row height
$sheet->getRowDimension($summaryRow)->setRowHeight(20);



// No. of Days Value (FIXED: hanggang Y lang, wag isama Z)
$sheet->setCellValue("X" . ($summaryRow + 1), $weekdayCount);
$sheet->mergeCells("X" . ($summaryRow + 1) . ":Y" . ($summaryRow + 1));
$sheet->getStyle("X" . ($summaryRow + 1) . ":Y" . ($summaryRow + 1))->applyFromArray([
    'font' => [
        'bold' => true,
        'name' => 'sans-serif',   
        'size' => 6
    ],
    'alignment' => [
        'horizontal' => 'center',
        'vertical'   => 'center'
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000']
        ]
    ]
]);

// Summary Header
$sheet->setCellValue("Z{$summaryRow}", "Summary");
$sheet->mergeCells("Z{$summaryRow}:AC{$summaryRow}");
$sheet->getStyle("Z{$summaryRow}:AC{$summaryRow}")->applyFromArray([
    'font' => [
        'bold' => true,
        'name' => 'sans-serif',   
        'size' => 6          
    ],
    'alignment' => [
        'horizontal' => 'center',
        'vertical'   => 'center'
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000']
        ]
    ]
]);

// Header row for M/F/TOTAL
$headerRow = $summaryRow + 1;
// Set values
$sheet->setCellValue("AA{$headerRow}", "M");
$sheet->setCellValue("AB{$headerRow}", "F");
$sheet->setCellValue("AC{$headerRow}", "TOTAL");

// Apply style (bold, centered, borders, font size)
$sheet->getStyle("Z{$headerRow}:AC{$headerRow}")->applyFromArray([
    'font' => [
        'bold' => true,
        'name' => 'sans-serif',   
        'size' => 6          
    ],
    'alignment' => [
        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000']
        ]
    ]
]);

// Set equal width for all three columns
$sheet->getColumnDimension('AA')->setWidth(6);
$sheet->getColumnDimension('AB')->setWidth(6);
$sheet->getColumnDimension('AC')->setWidth(6);

// === Weekdays in month ===
$daysInMonth = Carbon::create($this->year, $this->month, 1)->daysInMonth;
$weekdays = [];
$holidays = $holidays ?? [];
for ($d = 1; $d <= $daysInMonth; $d++) {
    $date = Carbon::create($this->year, $this->month, $d);
    $dateStr = $date->toDateString();
    
    if (!in_array($date->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]) &&
        !in_array($dateStr, $holidays)) {
        $weekdays[] = $dateStr;
    }
}

$weekdayCount = count($weekdays);

// === Enrolment as of Start & End of Month ===
$monthStart = Carbon::create($this->year, $this->month, 1); 
$monthEnd   = $monthStart->copy()->endOfMonth();         


// ✅ Enrolled on or before end of month (initial)
$maleInitial = StudentEnrollment::where('section_id', $this->sectionId)
    ->where('semester_id', $this->semesterId)
    ->whereDate('date_created', '<=', $monthEnd)
    ->whereHas('student', fn($q) => $q->where('gender', 'Male'))
    ->count();

$femaleInitial = StudentEnrollment::where('section_id', $this->sectionId)
    ->where('semester_id', $this->semesterId)
    ->whereDate('date_created', '<=', $monthEnd)
    ->whereHas('student', fn($q) => $q->where('gender', 'Female'))
    ->count();

$totalInitial = $maleInitial + $femaleInitial;

$academicYear = AcademicYear::where('status', 'current')->first();

if ($academicYear) {
    $cutoffDate = Carbon::parse($academicYear->start_date)->firstOfMonth()->next(Carbon::FRIDAY);
    $schoolYearStart = Carbon::parse($academicYear->start_date);
    $schoolYearEnd   = Carbon::parse($academicYear->end_date);

    $lateMale = StudentEnrollment::where('section_id', $this->sectionId)
        ->where('semester_id', $this->semesterId)
        ->whereDate('date_created', '>', $cutoffDate)
        ->whereBetween('date_created', [$schoolYearStart, $schoolYearEnd])
        ->whereHas('student', fn($q) => $q->where('gender', 'Male'))
        ->count();

    $lateFemale = StudentEnrollment::where('section_id', $this->sectionId)
        ->where('semester_id', $this->semesterId)
        ->whereDate('date_created', '>', $cutoffDate)
        ->whereBetween('date_created', [$schoolYearStart, $schoolYearEnd])
        ->whereHas('student', fn($q) => $q->where('gender', 'Female'))
        ->count();

    $lateTotal = $lateMale + $lateFemale;
}

// === Registered Learners as of End of Month ===
$registeredMale = StudentEnrollment::where('section_id', $this->sectionId)
    ->where('semester_id', $this->semesterId)
    ->where('status', 'Active')
    ->whereHas('student', fn($q) => $q->where('gender', 'Male'))
    ->count();

$registeredFemale = StudentEnrollment::where('section_id', $this->sectionId)
    ->where('semester_id', $this->semesterId)
    ->where('status', 'Active')
    ->whereHas('student', fn($q) => $q->where('gender', 'Female'))
    ->count();

$registeredTotal = $registeredMale + $registeredFemale;

// === Percentage of Enrolment as of End of Month ===
$percEnrolMale   = $maleInitial   > 0 ? round(($registeredMale   / $maleInitial)   * 100, 0) . "%" : "0%";
$percEnrolFemale = $femaleInitial > 0 ? round(($registeredFemale / $femaleInitial) * 100, 0) . "%" : "0%";
$percEnrolTotal  = $totalInitial  > 0 ? round(($registeredTotal  / $totalInitial)  * 100, 0) . "%" : "0%";



$dailyMale = []; 
$dailyFemale = [];

foreach ($weekdays as $day) {
$male = AttendanceLog::whereHas('enrollment.student', fn($q) => 
        $q->where('gender', 'Male')
    )
    ->whereHas('enrollment', fn($q) => 
        $q->where('section_id', $this->sectionId)
          ->where('semester_id', $this->semesterId)
          ->where('status', 'Active') // <--- filter only active students
    )
    ->whereDate('date_time', $day)
    ->whereNotNull('date_time')
    ->count();

$female = AttendanceLog::whereHas('enrollment.student', fn($q) => 
        $q->where('gender', 'Female')
    )
    ->whereHas('enrollment', fn($q) => 
        $q->where('section_id', $this->sectionId)
          ->where('semester_id', $this->semesterId)
          ->where('status', 'Active') // <--- only active
    )
    ->whereDate('date_time', $day)
    ->whereNotNull('date_time')
    ->count();


    $dailyMale[] = $male;
    $dailyFemale[] = $female;
}

$schoolDays = $weekdayCount;

$totalMaleAttendance = array_sum($dailyMale);
$totalFemaleAttendance = array_sum($dailyFemale);
$totalAttendance = $totalMaleAttendance + $totalFemaleAttendance;

$avgDailyMale = $schoolDays > 0 ? round($totalMaleAttendance / $schoolDays, 0) : 0;
$avgDailyFemale = $schoolDays > 0 ? round($totalFemaleAttendance / $schoolDays, 0) : 0;
$avgDailyTotal = $schoolDays > 0 ? round($totalAttendance / $schoolDays, 0) : 0;

$avgPercMale = $registeredMale > 0 ? round(($avgDailyMale / $registeredMale) * 100, 2) : 0;
$avgPercFemale = $registeredFemale > 0 ? round(($avgDailyFemale / $registeredFemale) * 100, 2) : 0;
$avgPercTotal = $registeredTotal > 0 ? round(($avgDailyTotal / $registeredTotal) * 100, 2) : 0;


// === Percentage of Attendance (based on registered students) ===
$percMale = $registeredMale > 0 ? round(($avgDailyMale / $registeredMale) * 100, 0) . "%" : "0%";
$percFemale = $registeredFemale > 0 ? round(($avgDailyFemale / $registeredFemale) * 100, 0) . "%" : "0%";
$percTotal = $registeredTotal > 0 ? round(($avgDailyTotal / $registeredTotal) * 100, 0) . "%" : "0%";

$absent5Male = $absent5Female = $absent5Total = 0;
$nlsMale = $nlsFemale = $nlsTotal = 0;
$transOutMale = $transOutFemale = $transOutTotal = 0;
$transInMale = $transInFemale = $transInTotal = 0;
$shiftOutMale = $shiftOutFemale = $shiftOutTotal = 0;
$shiftInMale = $shiftInFemale = $shiftInTotal = 0;

// === Build data table ===
$data = [
    "* Enrolment as of (1st Friday of the SY)" => [$maleInitial, $femaleInitial, $totalInitial],
    "Late enrolment during the month\n(beyond cut-off)" => [$lateMale, $lateFemale, $lateTotal],
    "Registered Learners as of end of month"  => [$registeredMale, $registeredFemale, $registeredTotal],
    "Percentage of Enrolment as of end of month" => [$percEnrolMale, $percEnrolFemale, $percEnrolTotal],
    "Average Daily Attendance (%)" => [$avgDailyMale, $avgDailyFemale, $avgDailyTotal],
    "Percentage of Attendance for the month" => [$percMale, $percFemale, $percTotal],
    "Number of students absent for 5 consecutive days" => [$absent5Male, $absent5Female, $absent5Total],
    "NLS"                                     => [$nlsMale, $nlsFemale, $nlsTotal],
    "Transferred Out"                         => [$transOutMale, $transOutFemale, $transOutTotal],
    "Transferred In"                          => [$transInMale, $transInFemale, $transInTotal],
    "Shifted Out"                             => [$shiftOutMale, $shiftOutFemale, $shiftOutTotal],
    "Shifted In"                              => [$shiftInMale, $shiftInFemale, $shiftInTotal],
];


$currentRow = $summaryRow + 2;
foreach ($data as $label => $values) {
    // Label sa V–Z
    $sheet->setCellValue("V{$currentRow}", $label);
    $sheet->mergeCells("V{$currentRow}:Z{$currentRow}");

    // Values sa AA–AC
    $sheet->setCellValue("AA{$currentRow}", $values[0]);
    $sheet->setCellValue("AB{$currentRow}", $values[1]);
    $sheet->setCellValue("AC{$currentRow}", $values[2]);

    // === Borders + Font (apply to buong row) ===
    $sheet->getStyle("V{$currentRow}:AC{$currentRow}")->applyFromArray([
        'font' => [
            'name' => 'sans-serif',
            'size' => 5
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000']
            ]
        ]
    ]);

    // === Alignment (split) ===
    $sheet->getStyle("V{$currentRow}:Z{$currentRow}")->getAlignment()
        ->setHorizontal('left')
        ->setVertical('center')
        ->setWrapText(true)
        ->setIndent(1); // optional indent

    $sheet->getStyle("AA{$currentRow}:AC{$currentRow}")->getAlignment()
        ->setHorizontal('center')
        ->setVertical('center')
        ->setWrapText(true);

    // After NLS, insert dark separator row
    if ($label === "NLS") {
        $currentRow++;
        $sheet->mergeCells("V{$currentRow}:AC{$currentRow}");
        $sheet->getStyle("V{$currentRow}:AC{$currentRow}")->applyFromArray([
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => 'FF333333'] // dark gray/black background
            ]
        ]);
        // optional height
        $sheet->getRowDimension($currentRow)->setRowHeight(8);
    }

    $currentRow++;
}

// Certification
$certRow = $currentRow ;
$sheet->setCellValue("V{$certRow}", "I certify that this is a true and correct report.");
$sheet->mergeCells("V{$certRow}:AC{$certRow}");
$sheet->getStyle("V{$certRow}:AC{$certRow}")->applyFromArray([
    'bold' => true,
    'font' => ['name' => 'sans-serif', 'size' => 8],
    'alignment' => ['horizontal' => 'left']
]);

// Adviser Signature
$adviserRow = $certRow + 2;

// Adviser Name with underline (full cell underline)
$sheet->setCellValue("V{$adviserRow}", $this->adviserName);
$sheet->mergeCells("V{$adviserRow}:AC{$adviserRow}");
$sheet->getStyle("V{$adviserRow}:AC{$adviserRow}")->applyFromArray([
    'font' => [
        'name' => 'sans-serif',
        'size' => 9
    ],
    'borders' => [
        'bottom' => [ // underline effect across merged cell
            'borderStyle' => Border::BORDER_THIN
        ]
    ],
    'alignment' => [
        'horizontal' => 'center'
    ]
]);

// Label
$sheet->setCellValue("V".($adviserRow+1), "(Signature of Adviser over Printed Name)");
$sheet->mergeCells("V".($adviserRow+1).":AC".($adviserRow+1));
$sheet->getStyle("V".($adviserRow+1).":AC".($adviserRow+1))->applyFromArray([
    'font' => ['name' => 'sans-serif', 'size' => 6],
    'alignment' => ['horizontal' => 'center']
]);

// Attested by
$attestRow = $adviserRow + 3;
$sheet->setCellValue("V{$attestRow}", "Attested by:");
$sheet->mergeCells("V{$attestRow}:AC{$attestRow}");
$sheet->getStyle("V{$attestRow}:AC{$attestRow}")->applyFromArray([
    'font' => ['name' => 'sans-serif', 'size' => 6]
]);

$sheet->setCellValue("V".($attestRow+2), "MARY JANE M. ALVAREZ");
$sheet->mergeCells("V".($attestRow+2).":AC".($attestRow+2));
$sheet->getStyle("V".($attestRow+2).":AC".($attestRow+2))->applyFromArray([
    'font' => [
        'name' => 'sans-serif',
        'size' => 9
    ],
    'borders' => [
        'bottom' => [ // underline effect across merged cell
            'borderStyle' => Border::BORDER_THIN
        ]
    ],
    'alignment' => [
        'horizontal' => 'center'
    ]
]);

$sheet->setCellValue("V".($attestRow+3), "(Signature of School Head over Printed Name)");
$sheet->mergeCells("V".($attestRow+3).":AC".($attestRow+3));
$sheet->getStyle("V".($attestRow+3).":AC".($attestRow+3))->applyFromArray([
    'font' => ['name' => 'sans-serif', 'size' => 6],
    'alignment' => ['horizontal' => 'center']
]);





            },
        ];
    }
}
