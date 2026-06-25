<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\AttendanceLog;
use App\Exports\MonthlyAttendanceExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Section;
use App\Models\StudentEnrollment;

class TeacherDashboardController extends Controller
{
    /**
     * Convert a Manila date (Y-m-d) to app-timezone UTC-range for queries.
     * Returns array [startDateTime, endDateTime] in app timezone to use in whereBetween.
     *
     * This makes date filters reliable even if DB stores timestamps in a different timezone.
     */
    protected function manilaDayRange(string $manilaDate): array
    {
        $manilaTz = 'Asia/Manila';
        $appTz = config('app.timezone') ?: 'UTC';

        // start of day in Manila
        $startManila = Carbon::createFromFormat('Y-m-d H:i:s', $manilaDate . ' 00:00:00', $manilaTz);
        $endManila = Carbon::createFromFormat('Y-m-d H:i:s', $manilaDate . ' 23:59:59', $manilaTz);

        // convert to app timezone (likely UTC or whatever your DB uses)
        $startApp = $startManila->setTimezone($appTz)->toDateTimeString();
        $endApp = $endManila->setTimezone($appTz)->toDateTimeString();

        return [$startApp, $endApp];
    }

    /**
     * Convert Manila start and end date for a month (Y-m) into app timezone range.
     */
    protected function manilaMonthRange(string $manilaMonth): array
    {
        // manilaMonth format: 'YYYY-MM'
        $manilaTz = 'Asia/Manila';
        $appTz = config('app.timezone') ?: 'UTC';

        $startManila = Carbon::createFromFormat('Y-m-d H:i:s', $manilaMonth . '-01 00:00:00', $manilaTz)->startOfDay();
        $endManila = (clone $startManila)->endOfMonth()->setTime(23,59,59);

        $startApp = $startManila->setTimezone($appTz)->toDateTimeString();
        $endApp = $endManila->setTimezone($appTz)->toDateTimeString();

        return [$startApp, $endApp];
    }

    public function index()
    {
        $sectionId = session('adviser_section_id');
        $userId = session('adviser_user_id');

        if (!$sectionId || !$userId) {
            auth()->logout();
            return redirect('/login')->with('error', 'No section assigned or session expired.');
        }

        // ===== GET ACTIVE SEMESTER AND ACADEMIC YEAR =====
        $currentAcadYear = AcademicYear::where('status', 'current')->first();
        $currentSemester = Semester::where('status', 'current')
            ->where('academic_year_id', $currentAcadYear?->academic_year_id)
            ->first();

        if (!$currentAcadYear || !$currentSemester) {
            return view('teacher.dashboard', [
                'totalStudents' => 0,
                'presentToday' => 0,
                'lateToday' => 0,
                'excusedToday' => 0,
                'absentToday' => 0,
                'averageAttendance' => 0,
                'trendLabels' => [],
                'trendRates' => [],
                'todayAttendance' => collect(),
                'topAbsentLabels' => [],
                'topAbsentCounts' => [],
                'hasAttendance' => false,
                'noClassToday' => false
            ]);
        }

        $manilaTz = 'Asia/Manila';
        $todayManila = Carbon::now($manilaTz)->toDateString();        // 'YYYY-MM-DD'
        $monthY = Carbon::now($manilaTz)->format('Y-m');              // 'YYYY-MM'

        // Definitive "present" statuses used consistently everywhere
        $presentStatuses = ['Time In', 'Present', 'Late'];

        // ===== TOTAL STUDENTS =====
        $totalStudents = StudentEnrollment::where('section_id', $sectionId)
            ->where('semester_id', $currentSemester->semester_id)
            ->whereHas('student', fn($q) => $q->whereNull('date_archived'))
            ->count();

        // ===== TODAY'S ATTENDANCE LOGS =====
        [$startTodayApp, $endTodayApp] = $this->manilaDayRange($todayManila);

        // Subquery: per-enrollment summary for today (is_excused and first_time_in from present statuses)
        $alToday = AttendanceLog::select(
                'enrollment_id',
                DB::raw("MAX(CASE WHEN status='Excused' THEN 1 ELSE 0 END) as is_excused"),
                // earliest date_time among present-like statuses
                DB::raw("MIN(CASE WHEN status IN ('Time In','Present','Late') THEN date_time END) as first_time_in")
            )
            ->whereBetween('date_time', [$startTodayApp, $endTodayApp])
            ->groupBy('enrollment_id');

        $todayAttendance = collect();
        $presentToday = $lateToday = $excusedToday = $absentToday = 0;
        $noClassToday = false;

        // Count any attendance log today for that section/enrollment (any status) — use same day range
        $attendanceCountToday = AttendanceLog::whereBetween('date_time', [$startTodayApp, $endTodayApp])
            ->whereHas('enrollment', fn($q) =>
                $q->where('section_id', $sectionId)
                  ->where('semester_id', $currentSemester->semester_id)
                  ->where('status', 'Active')
                  ->whereHas('student', fn($s) => $s->whereNull('date_archived'))
            )->count();

        if ($attendanceCountToday > 0) {
            // Build the student list + computed status for today
            $todayAttendance = StudentEnrollment::where('section_id', $sectionId)
                ->where('semester_id', $currentSemester->semester_id)
                ->where('status', 'Active')
                ->whereHas('student', fn($q) => $q->whereNull('date_archived'))
                ->with('student')
                ->leftJoinSub($alToday, 'al', fn($join) => $join->on('al.enrollment_id', '=', 'student_enrollments.enrollment_id'))
                ->select(
                    'student_enrollments.enrollment_id',
                    'student_enrollments.student_id',
                    DB::raw("CASE
                        WHEN al.is_excused = 1 THEN 'Excused'
                        WHEN al.first_time_in IS NULL THEN 'Absent'
                        WHEN TIME(al.first_time_in) > '08:00:00' THEN 'Late'
                        ELSE 'Present'
                    END as status")
                )
                ->get()
                ->map(function($enrollment) {
                    $enrollment->first_name = $enrollment->student->first_name;
                    $enrollment->last_name = $enrollment->student->last_name;
                    return $enrollment;
                });

            // Count today totals
            $presentToday = $todayAttendance->whereIn('status', ['Present', 'Late'])->count();
            $lateToday = $todayAttendance->where('status', 'Late')->count();
            $excusedToday = $todayAttendance->where('status', 'Excused')->count();
            $absentToday = $todayAttendance->where('status', 'Absent')->count();

            // Keep only absent list for the UI panel
            $todayAttendance = $todayAttendance->where('status', 'Absent')->values();
        } else {
            $noClassToday = true;
        }

        // ===== AVERAGE ATTENDANCE THIS MONTH =====
        [$startMonthApp, $endMonthApp] = $this->manilaMonthRange($monthY);

        $sumPresentByDay = AttendanceLog::whereHas('enrollment', fn($q) =>
                $q->where('section_id', $sectionId)
                  ->where('semester_id', $currentSemester->semester_id)
                  ->where('status', 'Active')
                  ->whereHas('student', fn($s) => $s->whereNull('date_archived'))
            )
            ->whereBetween('date_time', [$startMonthApp, $endMonthApp])
            ->whereIn('status', $presentStatuses)
            ->select(DB::raw('DATE(date_time) as date'), DB::raw('COUNT(DISTINCT enrollment_id) as present_count'))
            ->groupBy(DB::raw('DATE(date_time)'))
            ->get();

        $daysWithData = $sumPresentByDay->count();
        $averageAttendance = 0;
        if ($totalStudents > 0 && $daysWithData > 0) {
            $dailyRates = $sumPresentByDay->map(fn($row) => ($row->present_count / $totalStudents) * 100)->all();
            $averageAttendance = round(array_sum($dailyRates) / count($dailyRates), 2);
        }

        // ===== ATTENDANCE TREND (LAST 7 MANILA DAYS, skip weekends) =====
        $periodManilaStart = Carbon::now($manilaTz)->subDays(6)->startOfDay();
        $periodManilaEnd = Carbon::now($manilaTz)->endOfDay();

        // Convert period to app timezone strings for query
        $appTz = config('app.timezone') ?: 'UTC';
        $periodStartApp = $periodManilaStart->setTimezone($appTz)->toDateTimeString();
        $periodEndApp = $periodManilaEnd->setTimezone($appTz)->toDateTimeString();

        $trendRaw = AttendanceLog::whereHas('enrollment', fn($q) =>
                $q->where('section_id', $sectionId)
                  ->where('semester_id', $currentSemester->semester_id)
                  ->where('status', 'Active')
                  ->whereHas('student', fn($s) => $s->whereNull('date_archived'))
            )
            ->whereBetween('date_time', [$periodStartApp, $periodEndApp])
            ->whereIn('status', $presentStatuses)
            ->select(DB::raw('DATE(date_time) as date'), DB::raw('COUNT(DISTINCT enrollment_id) as present_count'))
            ->groupBy(DB::raw('DATE(date_time)'))
            ->pluck('present_count', 'date');

        $trendLabels = [];
        $trendRates = [];

        $period = CarbonPeriod::create($periodManilaStart, $periodManilaEnd);
        foreach ($period as $date) {
            // skip weekends (Sunday=0, Saturday=6)
            if (in_array($date->dayOfWeek, [0,6])) continue;
            $d = $date->toDateString(); // Manila date label
            $count = (int) ($trendRaw[$d] ?? 0);
            $trendLabels[] = $date->format('M j');
            $trendRates[] = ($totalStudents > 0) ? round(($count / $totalStudents) * 100, 2) : 0;
        }

        $hasAttendance = collect($trendRates)->sum() > 0;

        // ===== TOP 5 MOST ABSENT STUDENTS (this month) =====
        // Get unique Manila dates (strings) in the month that have present-status logs.
        // We'll pull distinct DATE(date_time) after converting to app timezone — MySQL DATE() uses server timezone,
        // so we used whereBetween on app timezone. The selected DATE() will reflect server/app timezone; this works
        // because our whereBetween uses app-time boundaries derived from Manila.
        $attendanceDates = AttendanceLog::whereHas('enrollment', fn($q) =>
                $q->where('section_id', $sectionId)
                  ->where('semester_id', $currentSemester->semester_id)
                  ->where('status', 'Active')
                  ->whereHas('student', fn($s) => $s->whereNull('date_archived'))
            )
            ->whereBetween('date_time', [$startMonthApp, $endMonthApp])
            ->whereIn('status', $presentStatuses)
            ->select(DB::raw('DATE(date_time) as date'))
            ->distinct()
            ->orderBy('date', 'asc')
            ->pluck('date')
            ->map(function($dateString) use ($manilaTz, $appTz) {
                // Convert the app-time date (server) to Manila date string to compare consistently later.
                // We'll normalize so comparisons are based on Manila day boundaries.
                $dt = Carbon::createFromFormat('Y-m-d', $dateString, $appTz)->startOfDay()->setTimezone($manilaTz);
                return $dt->toDateString();
            })->unique()->values()->toArray();

        // If there are no attendanceDates in the month, topAbsent remains empty
        $topAbsent = collect();
        if (count($attendanceDates) > 0) {
            // Load enrollments once
            $enrollments = StudentEnrollment::where('section_id', $sectionId)
                ->where('semester_id', $currentSemester->semester_id)
                ->where('status', 'Active')
                ->whereHas('student', fn($q) => $q->whereNull('date_archived'))
                ->with('student')
                ->get();

            // Build a map of enrollment_id => set of Manila-date strings that have present logs for that enrollment
            $enrollmentPresentMap = [];

            // Query attendance logs grouped by enrollment and date (in app-time), but convert to Manila date key
            $logsForEnrollments = AttendanceLog::whereIn('enrollment_id', $enrollments->pluck('enrollment_id')->toArray())
                ->whereBetween('date_time', [$startMonthApp, $endMonthApp])
                ->whereIn('status', $presentStatuses)
                ->select('enrollment_id', DB::raw('DATE(date_time) as date'))
                ->get();

            foreach ($logsForEnrollments as $log) {
                // convert this server/app date to Manila date string key
                $appTz = config('app.timezone') ?: 'UTC';
                $manilaDate = Carbon::createFromFormat('Y-m-d', $log->date, $appTz)->startOfDay()->setTimezone($manilaTz)->toDateString();
                $enrollmentPresentMap[$log->enrollment_id][$manilaDate] = true;
            }

            // Compute absences (dates in attendanceDates not present in enrollmentPresentMap)
            $topAbsent = $enrollments->map(function($enrollment) use ($attendanceDates, $enrollmentPresentMap) {
                $absences = 0;
                foreach ($attendanceDates as $mDate) {
                    $hasPresent = isset($enrollmentPresentMap[$enrollment->enrollment_id]) &&
                                  !empty($enrollmentPresentMap[$enrollment->enrollment_id][$mDate]);
                    if (!$hasPresent) $absences++;
                }
                $enrollment->absences = $absences;
                return $enrollment;
            })->filter(fn($e) => $e->absences > 0)
              ->sortByDesc('absences')
              ->take(5)
              ->values();
        }

        $topAbsentLabels = $topAbsent->map(fn($e) => $e->student->last_name . ', ' . $e->student->first_name)->values();
        $topAbsentCounts = $topAbsent->pluck('absences')->values();

        return view('teacher.dashboard', [
            'totalStudents' => $totalStudents,
            'presentToday' => $presentToday,
            'lateToday' => $lateToday,
            'excusedToday' => $excusedToday,
            'absentToday' => $absentToday,
            'averageAttendance' => $averageAttendance,
            'todayAttendance' => $todayAttendance,
            'trendLabels' => $trendLabels,
            'trendRates' => $trendRates,
            'topAbsentLabels' => $topAbsentLabels,
            'topAbsentCounts' => $topAbsentCounts,
            'hasAttendance' => $hasAttendance,
            'noClassToday' => $noClassToday
        ]);
    }

    public function attendanceView()
    {
        $sectionId = session('adviser_section_id');
        $userId = session('adviser_user_id');

        if (!$sectionId || !$userId) {
            auth()->logout();
            return redirect('/login')->with('error', 'No section assigned or session expired.');
        }

        $manilaTz = 'Asia/Manila';
        [$startTodayApp, $endTodayApp] = $this->manilaDayRange(Carbon::now($manilaTz)->toDateString());

        $attendances = AttendanceLog::whereBetween('date_time', [$startTodayApp, $endTodayApp])
            ->whereHas('enrollment', fn($q) =>
                $q->where('section_id', $sectionId)
                  ->whereHas('student', fn($s) => $s->whereNull('date_archived'))
            )
            ->orderBy('date_time', 'asc')
            ->get();

        return view('teacher.attendanceview', compact('attendances'));
    }

    public function exportMonthlyAttendance(Request $request)
    {
        $sectionId = session('adviser_section_id');
        $month = $request->input('month');          // expect 'MM' or 'YYYY-MM' depending on UI - using 'YYYY-MM' recommended
        $year = $request->input('school_year');

        // Normalize to 'YYYY-MM'
        if (strlen($month) === 1) {
            $month = str_pad($month, 2, '0', STR_PAD_LEFT);
        }
        $monthY = $year . '-' . $month;

        return Excel::download(
            new MonthlyAttendanceExport($sectionId, $month, $year),
            'Monthly_Attendance_' . $year . '_' . $month . '.xlsx'
        );
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/homepage');
    }
}
