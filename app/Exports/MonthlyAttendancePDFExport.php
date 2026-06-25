<?php

namespace App\Exports;

use PDF;
use Carbon\Carbon;
use App\Models\Semester;
use App\Models\StudentEnrollment;
use App\Models\Section;
use App\Models\AttendanceLog;
use App\Models\AcademicYear;

class MonthlyAttendancePdfExport
{
    protected $sectionId;
    protected $month;
    protected $year;
    protected $semesterId;
    protected $semesterName;
    protected $academicYearName;
    protected $holidays;

    public function __construct($sectionId, $month, $academicYearName, $semesterId, $holidays = [])
    {
        $this->sectionId       = $sectionId;
        $this->month           = (int) $month;
        $this->semesterId      = $semesterId;
        $this->semesterName    = $this->determineSemester($this->month);
        $this->academicYearName= $academicYearName;
        $this->holidays        = $holidays;

        // Set year for generating month dates
        // Extract starting year from academicYearName (format: "2025-2026")
        $years = explode('-', $academicYearName);
        $this->year = ($this->month >= 6 && $this->month <= 12) ? (int)$years[0] : (int)$years[1];
    }

    private function determineSemester($month)
    {
        $month = (int)$month;
        if ($month >= 6 && $month <= 10) {
            return 'First Semester';
        } elseif (($month >= 11 && $month <= 12) || ($month >= 1 && $month <= 4)) {
            return 'Second Semester';
        } else {
            return 'First Semester';
        }
    }

    public function downloadPDF()
    {
        $sectionId = $this->sectionId;
        $month     = $this->month;
        $year      = $this->year;
        $semester  = Semester::find($this->semesterId);

        $startOfMonth = Carbon::create($year, $month, 1)->startOfMonth();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth()->setTime(23,59,59);

        // Students
        $students = StudentEnrollment::with('student')
            ->where('section_id', $sectionId)
            ->where('semester_id', $semester->semester_id)
            ->get();

        // Attendance logs
        $attendances = AttendanceLog::whereHas('enrollment', function($q) use ($sectionId, $semester){
                $q->where('section_id', $sectionId)
                  ->where('semester_id', $semester->semester_id);
            })
            ->whereBetween('date_time', [$startOfMonth, $endOfMonth])
            ->get()
            ->groupBy('enrollment_id')
            ->map(fn($logs) => $logs->keyBy(fn($log) => Carbon::parse($log->date)->format('Y-m-d')))
            ->toArray();

        // Section info
        $schoolId    = "305834";
        $schoolName  = "Talavera Senior High School";
        $sectionData = Section::with('track_strand', 'adviser')->find($sectionId);
        $strandName  = $sectionData->track_strand->strand ?? '';
        $trackName   = $sectionData->track_strand->track ?? '';
        $sectionName = $sectionData->section_name ?? '';
        $gradeLevel  = $sectionData->grade_level ?? '';

        $adviserName = '';
        if ($sectionData && $sectionData->adviser) {
            $ad = $sectionData->adviser;
            $mi = $ad->middle_name ? strtoupper(substr($ad->middle_name,0,1)) . '.' : '';
            $adviserName = strtoupper("{$ad->first_name} {$mi} {$ad->last_name}");
        }

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

    
   $viewData = [
    'students'      => $students,
    'attendances'   => $attendances,
    'section'       => $sectionName,
    'gradeLevel'    => $gradeLevel,
    'schoolName'    => $schoolName,
    'schoolId'      => $schoolId,
    'strandName'    => $strandName,
    'trackName'     => $trackName,
    'month'         => $month,
    'semester'      => $this->semesterName,
    'year'          => $year,
    'monthName'     => Carbon::create($year, $month, 1)->format('F'),
    'daysInMonth'   => $startOfMonth->daysInMonth,
    'acadYear'      => $this->academicYearName,
    'holidays'      => $this->holidays,
    'adviserName'   => $adviserName,
    'weekdayCount'  => $weekdayCount,
    // Summary data
    'maleInitial' => $maleInitial,
    'femaleInitial' => $femaleInitial,
    'totalInitial' => $totalInitial,
    'lateMale' => $lateMale ?? 0,
    'lateFemale' => $lateFemale ?? 0,
    'lateTotal' => $lateTotal ?? 0,
    'registeredMale' => $registeredMale,
    'registeredFemale' => $registeredFemale,
    'registeredTotal' => $registeredTotal,
    'percEnrolMale' => $percEnrolMale,
    'percEnrolFemale' => $percEnrolFemale,
    'percEnrolTotal' => $percEnrolTotal,
    'avgDailyMale' => $avgDailyMale,
    'avgDailyFemale' => $avgDailyFemale,
    'avgDailyTotal' => $avgDailyTotal,
    'percMale' => $percMale,
    'percFemale' => $percFemale,
    'percTotal' => $percTotal,
    'absent5Male' => $absent5Male,
    'absent5Female' => $absent5Female,
    'absent5Total' => $absent5Total,
    'nlsMale' => $nlsMale,
    'nlsFemale' => $nlsFemale,
    'nlsTotal' => $nlsTotal,
    'transOutMale' => $transOutMale,
    'transOutFemale' => $transOutFemale,
    'transOutTotal' => $transOutTotal,
    'transInMale' => $transInMale,
    'transInFemale' => $transInFemale,
    'transInTotal' => $transInTotal,
    'shiftOutMale' => $shiftOutMale,
    'shiftOutFemale' => $shiftOutFemale,
    'shiftOutTotal' => $shiftOutTotal,
    'shiftInMale' => $shiftInMale,
    'shiftInFemale' => $shiftInFemale,
    'shiftInTotal' => $shiftInTotal,

];


        $pdf = PDF::loadView('exports.sf2_pdf', $viewData)
                  ->setPaper('A4', 'landscape');

        $filename = "Monthly_Attendance_{$sectionName}_{$this->academicYearName}_{$month}.pdf";

        return $pdf->download($filename);
    }
}
