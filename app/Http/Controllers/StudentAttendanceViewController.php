<?php

namespace App\Http\Controllers;

use App\Exports\MonthlyAttendanceExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\StudentEnrollment;
use App\Models\AttendanceLog;
use App\Models\Student;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Services\SemaphoreService; 
use App\Exports\MonthlyAttendancePdfExport;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StudentAttendanceViewController extends Controller
{

public function exportMonthlyAttendance(Request $request)
{
    $month          = (int) $request->input('month');
    $academicYearId = $request->input('academic_year'); 
    $holidays       = $request->holidays ?? [];

    $academicYear = AcademicYear::findOrFail($academicYearId);

    // Determine semester based on month
    if ($month >= 6 && $month <= 10) { 
        $semesterName = 'First Semester';
    } elseif ($month >= 11 && $month <= 12) { 
        $semesterName = 'Second Semester';
    } elseif ($month >= 1 && $month <= 4) { 
        $semesterName = 'Second Semester';

        $startYear = Carbon::parse($academicYear->start_date)->addYear()->format('Y');
        $endYear   = Carbon::parse($academicYear->end_date)->addYear()->format('Y');
        $academicYearName = $startYear . ' - ' . $endYear;
    } else { 
        $semesterName = 'First Semester';
        $academicYearName = Carbon::parse($academicYear->start_date)->format('Y') 
                            . ' - ' . Carbon::parse($academicYear->end_date)->format('Y');
    }

    if (!isset($academicYearName)) {
        $academicYearName = Carbon::parse($academicYear->start_date)->format('Y') 
                            . ' - ' . Carbon::parse($academicYear->end_date)->format('Y');
    }

    // Get the semester record
    $semester = Semester::where('name', $semesterName)
        ->where('academic_year_id', $academicYear->academic_year_id)
        ->first();

    // Determine the start and end dates of the month
$startOfMonth = Carbon::create($academicYear->start_date)->startOfMonth();
$endOfMonth   = Carbon::create($academicYear->end_date)->endOfMonth();

// Get any enrollment that existed in this semester/month, even if archived
$enrollment = StudentEnrollment::where('semester_id', $semester->semester_id)
    ->where('date_created', '<=', $endOfMonth) // enrolled before or during the month
    ->where(function($q) use ($endOfMonth) {
        $q->whereNull('date_archived')             // still active
          ->orWhere('date_archived', '>=', $endOfMonth); // archived after this month
    })
    ->first();

if (!$enrollment) {
    return back()->with('error', 'No enrollments found for this semester.');
}

$sectionId = $enrollment->section_id;


    return Excel::download(
        new MonthlyAttendanceExport(
            $sectionId,
            $month,
            $academicYearName,
            $semester?->semester_id,
            $holidays 
        ),
        'Monthly_Attendance_' . $academicYearName .
        '_Sem-' . ($semester->name ?? $semesterName) . '_' . $month . '.xlsx'
    );
}



    public function index()
    {
        $sectionId = session('adviser_section_id');
        $userId = session('adviser_user_id');

        if (!$sectionId || !$userId) {
            auth()->logout();
            return redirect('/login')->with('error', 'No section assigned or session expired.');
        }

        $currentAcadYear = AcademicYear::where('status', 'current')->first();
        $currentSemester = Semester::where('status', 'current')
                            ->where('academic_year_id', $currentAcadYear?->academic_year_id)
                            ->first();

 $attendanceLogs = AttendanceLog::whereHas('enrollment', function ($q) use ($sectionId, $currentSemester) {
    $q->where('section_id', $sectionId);
    if ($currentSemester?->semester_id) {
        $q->where('semester_id', $currentSemester->semester_id);
    }
})
->whereDate('date_time', now('Asia/Manila')) // <-- use date_time instead of date
->with([
    'enrollment.student',   
    'enrollment.section', 
])
->orderBy('date_time', 'desc')
->paginate(20);


         $enrollments = StudentEnrollment::with(['student', 'section'])
        ->where('section_id', $sectionId)
        ->where('semester_id', $currentSemester?->semester_id)
        ->whereNull('date_archived')
        ->get();

        $users = User::whereNull('date_archived')
            ->orderBy('first_name')
            ->get();

        $academicYears = AcademicYear::orderBy('name', 'asc')->get();
        $semesters = Semester::orderBy('name', 'asc')->get();

        return view('teacher.attendanceview', compact('attendanceLogs', 'enrollments', 'users', 'academicYears', 'semesters'));
    }


  public function store(Request $request, SemaphoreService $semaphore)
{
    $validated = $request->validate([
        'enrollment_id' => 'required|exists:student_enrollments,enrollment_id',
        'status'        => 'required|string|in:Excused'
    ]);

    // Force status to "Excused" para walang lusot
    $status = "Excused";

    $userId = session('adviser_user_id');
    $now = Carbon::now('Asia/Manila')->format('Y-m-d H:i:s');

    try {

        // Check kung may existing record for same date (optional)
        $existing = AttendanceLog::where('enrollment_id', $validated['enrollment_id'])
            ->whereDate('date_time', Carbon::today('Asia/Manila'))
            ->first();

        if ($existing) {
            return redirect()->back()
                ->with('error', 'Attendance already exists for today.');
        }

        // Save EXCUSED only
        AttendanceLog::create([
            'enrollment_id' => $validated['enrollment_id'],
            'date_time'     => $now,
            'status'        => $status,
            'date_created'  => $now,
        ]);

        return redirect()->route('teacher.attendanceView')
            ->with('success', 'Excused attendance added successfully!')
        ;

    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Failed to add attendance: ' . $e->getMessage());
    }
}



public function update(Request $request, $id)
{
    try {
        $attendance = AttendanceLog::findOrFail($id);

        // Validation: only status allowed
        $validated = $request->validate([
            'status' => 'required|string|in:Excused'
        ]);

        // Force status to Excused only
        $attendance->update([
            'status' => 'Excused'
        ]);

        return redirect()->route('teacher.attendanceView')
            ->with('success', 'Attendance updated successfully!');
    } catch (\Exception $e) {
        return redirect()->back()
            ->with('error', 'Failed to update attendance. Please try again. ' . $e->getMessage());
    }
}



 private function normalizeNumber($number)
    {
        $number = preg_replace('/\D/', '', $number);

        if (substr($number, 0, 2) === '09') {
            return '63' . substr($number, 1);
        }

        if (substr($number, 0, 3) === '639') {
            return $number;
        }

        return $number;
    }
    
     public function downloadSF2PDF(Request $request)
{
    $month          = (int) $request->input('month');
    $academicYearId = $request->input('academic_year'); 
    $holidays       = $request->input('holidays', []);

    $academicYear = AcademicYear::findOrFail($academicYearId);

    // Same logic as Excel for determining semester and academic year
    if ($month >= 6 && $month <= 10) { 
        $semesterName = 'First Semester';
        $academicYearName = Carbon::parse($academicYear->start_date)->format('Y') 
                            . ' - ' . Carbon::parse($academicYear->end_date)->format('Y');
    } elseif ($month >= 11 && $month <= 12) { 
        $semesterName = 'Second Semester';
        $academicYearName = Carbon::parse($academicYear->start_date)->format('Y') 
                            . ' - ' . Carbon::parse($academicYear->end_date)->format('Y');
    } elseif ($month >= 1 && $month <= 4) { 
        $semesterName = 'Second Semester';
        $startYear = Carbon::parse($academicYear->start_date)->addYear()->format('Y');
        $endYear   = Carbon::parse($academicYear->end_date)->addYear()->format('Y');
        $academicYearName = $startYear . ' - ' . $endYear;
    } else { 
        $semesterName = 'First Semester';
        $academicYearName = Carbon::parse($academicYear->start_date)->format('Y') 
                            . ' - ' . Carbon::parse($academicYear->end_date)->format('Y');
    }

    // Get the semester record
    $semester = Semester::where('name', $semesterName)
        ->where('academic_year_id', $academicYear->academic_year_id)
        ->first();

    if (!$semester) {
        return back()->with('error', 'Semester not found for this academic year.');
    }

    // Find enrollment for section
    $enrollment = StudentEnrollment::where('semester_id', $semester->semester_id)
        ->first();

    if (!$enrollment) {
        return back()->with('error', 'No enrollments found for this semester.');
    }

    $sectionId = $enrollment->section_id;

    // Call the existing PDF export class (pass same variables as Excel)
    $exporter = new MonthlyAttendancePdfExport(
        $sectionId,
        $month,
        $academicYearName,
        $semester->semester_id,
        $holidays
    );

    return $exporter->downloadPDF();
}


}
