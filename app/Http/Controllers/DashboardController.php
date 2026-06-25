<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();
        $todayDayOfWeek = Carbon::today()->format('N'); // 1=Mon, 7=Sun

        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        // ===== ACTIVE ENROLLMENTS =====
        $activeEnrollments = DB::table('student_enrollments')
            ->join('semesters', 'student_enrollments.semester_id', '=', 'semesters.semester_id')
            ->join('academic_years', 'semesters.academic_year_id', '=', 'academic_years.academic_year_id')
            ->where('semesters.status', 'current')
            ->where('academic_years.status', 'current')
            ->select('student_enrollments.enrollment_id')
            ->pluck('enrollment_id');

        $totalStudents = $activeEnrollments->count();

        // ===== PRESENT TODAY =====
        $presentToday = DB::table('attendance_logs')
            ->whereDate('attendance_logs.date_time', $today)
            ->whereIn('enrollment_id', $activeEnrollments)
            ->distinct('enrollment_id')
            ->count('enrollment_id');

        // ===== ABSENT TODAY (exclude weekends) =====
        $absentToday = 0;
        if (!in_array($todayDayOfWeek, [6, 7])) { // weekdays only
            $absentToday = $totalStudents - $presentToday;
        }

        // ===== LATE TODAY =====
        $lateToday = DB::table('attendance_logs')
            ->whereDate('attendance_logs.date_time', $today)
            ->whereIn('enrollment_id', $activeEnrollments)
            ->where('status', 'Late')
            ->distinct('enrollment_id')
            ->count('enrollment_id');

        // ===== SMS SENT TODAY =====
        $smsSentToday = DB::table('s_m_s_logs')
            ->whereDate('sent_at', $today)
            ->count();

        // ===== MONTHLY ATTENDANCE RATE =====
        // Generate all weekdays in the month
        $period = new \DatePeriod(
            $monthStart,
            new \DateInterval('P1D'),
            $monthEnd->addDay()
        );

        $schoolDays = [];
        foreach ($period as $date) {
            if (!in_array($date->format('N'), [6, 7])) { // exclude Sat & Sun
                $schoolDays[] = $date->format('Y-m-d');
            }
        }

        $totalSchoolDays = count($schoolDays);

        // Count total present for the month (only weekdays)
        $totalMonthlyPresent = DB::table('attendance_logs')
            ->join('student_enrollments', 'attendance_logs.enrollment_id', '=', 'student_enrollments.enrollment_id')
            ->join('semesters', 'student_enrollments.semester_id', '=', 'semesters.semester_id')
            ->join('academic_years', 'semesters.academic_year_id', '=', 'academic_years.academic_year_id')
            ->where('semesters.status', 'current')
            ->where('academic_years.status', 'current')
            ->whereIn(DB::raw('DATE(attendance_logs.date_time)'), $schoolDays)
            ->distinct('attendance_logs.enrollment_id', 'attendance_logs.date_time')
            ->count();

        // Compute Monthly Attendance Rate
        $monthlyAttendanceRate = 0;
        if ($totalStudents > 0 && $totalSchoolDays > 0) {
            $monthlyAttendanceRate = round(
                ($totalMonthlyPresent / ($totalStudents * $totalSchoolDays)) * 100,
                0
            );
        }

        // ===== RETURN TO VIEW =====
        return view('dashboard.index', [
            'totalStudents'  => $totalStudents,
            'presentToday'   => $presentToday,
            'absentToday'    => $absentToday,
            'lateToday'      => $lateToday,
            'smsSentToday'   => $smsSentToday,
            'attendanceRate' => $monthlyAttendanceRate,
        ]);
    }
}
