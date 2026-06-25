<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\AttendanceLog;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Services\SemaphoreService; 
use App\Models\Section;
use App\Models\User;

class AttendanceLogController extends Controller
{


    public function store(Request $request, SemaphoreService $semaphore)
{
    $code = $request->input('qr_code');

    $currentAcadYear = AcademicYear::where('status', 'current')->first();
    $currentSemester = Semester::where('status', 'current')
        ->where('academic_year_id', $currentAcadYear?->academic_year_id)
        ->first();

    if (!$currentSemester) {
        return back()->with('error', 'No active semester found.');
    }

    $enrollment = StudentEnrollment::with(['student.guardians', 'section'])
        ->whereHas('student', function ($q) use ($code) {
            $q->where('lrn', $code);
        })
        ->where('semester_id', $currentSemester->semester_id)
        ->first();

    if (!$enrollment) {
        return back()->with('error', 'No active enrollment found for this student.');
    }

    if ($enrollment->status !== 'Active') {
        return back()->with('error', 'This student is no longer active and cannot log attendance.');
    }

    $student = $enrollment->student;
    $section = $enrollment->section;
    $today   = now('Asia/Manila')->toDateString();
    $currentTime = now('Asia/Manila');

    // ===== GET ALL ATTENDANCE LOGS FOR TODAY =====
    $attendanceLogs = AttendanceLog::where('enrollment_id', $enrollment->enrollment_id)
        ->whereDate('date_time', $today)
        ->orderBy('date_time', 'asc')
        ->get();

    // Check if student already scanned 4 times today
    if ($attendanceLogs->count() >= 4) {
        return redirect()->back()->with([
            'student' => $student,
            'error' => 'Maximum attendance scans reached for today.'
        ]);
    }

    // Determine status based on the last scan
    $lastLog = $attendanceLogs->last(); // get last record
    $lastStatus = $lastLog?->status ?? null;
    $status = ($lastStatus === 'Time In' || $lastStatus === null) ? 'Time Out' : 'Time In';
    if ($lastStatus === null) $status = 'Time In'; // first scan

    // Save attendance
    AttendanceLog::create([
        'enrollment_id' => $enrollment->enrollment_id,
        'date_time'     => $currentTime,
        'status'        => $status,
    ]);

    $fullName    = trim("{$student->first_name} {$student->middle_name} {$student->last_name}");
    $sectionInfo = "Grade {$section->grade_level} - {$section->section_name}";

        foreach ($student->guardians as $guardian) {
            // Skip if no phone number or guardian is archived
            if (empty($guardian->phone_number) || $guardian->date_archived !== null) {
                continue;
            }

            $guardianNumber = $this->normalizeNumber($guardian->phone_number);
            $message = "TSHS Attendance Notice\n\n"
                    . "Student: {$fullName}\n"
                    . "Section: {$sectionInfo}\n"
                    . "Date: " . $currentTime->format('F d, Y') . "\n"
                    . "Time: " . $currentTime->format('h:i A') . "\n"
                    . "Status: {$status}\n"
                    . "Guardian Type: {$guardian->guardian_type}\n\n";

            $semaphore->sendSMS($guardianNumber, $message);
        }


    // ===== UPDATE LATEST SCANS SESSION =====
    $latestScans = session('latest_scans', []);
    $newScan = [
        'first_name'   => $student->first_name,
        'middle_name'  => $student->middle_name,
        'last_name'    => $student->last_name,
        'grade_level'  => $section->grade_level ?? 'N/A',
        'section_name' => $section->section_name ?? 'N/A',
        'image'        => $student->image ?? 'images/default-student.png',
        'date'         => $currentTime->format('F d, Y'),
        'time'         => $currentTime->format('h:i A'),
        'status'       => $status,
    ];

    array_unshift($latestScans, $newScan);
    if (count($latestScans) > 5) {
        array_pop($latestScans);
    }

    session(['latest_scans' => $latestScans]);
    session(['student' => $latestScans[0]]);
    session(['previous_scans' => array_slice($latestScans, 1)]);

    return redirect()->back();
}


    // 🔹 Admin manual attendance
    public function adminAttStore(Request $request, SemaphoreService $semaphore)
    {
        $validated = $request->validate([
            'enrollment_id' => 'required|exists:student_enrollments,enrollment_id',
            'date_time'     => 'required|date',
            'remarks'       => 'required|string',
            'status'        => 'required|string'
        ]);

        $userId = auth()->user()->user_id;
        $attendanceDateTime = Carbon::parse($validated['date_time'], 'Asia/Manila');

        $attendanceExists = AttendanceLog::where('enrollment_id', $validated['enrollment_id'])
            ->whereDate('date_time', $attendanceDateTime->toDateString())
            ->first();

        if ($attendanceExists) {
            return redirect()->back()->with('error', 'Attendance for this enrollment on this date already exists!');
        }

        $attendanceLog = AttendanceLog::create([
            'enrollment_id' => $validated['enrollment_id'],
            'date_time'     => $attendanceDateTime,
            'status'        => $validated['status'],
        ]);

        // Send SMS
        $enrollment = StudentEnrollment::with('student.guardian', 'section')
                        ->find($validated['enrollment_id']);
        $student = $enrollment->student;
        $guardian = $student->guardian ?? null;

        if ($guardian && $guardian->phone_number) {
            $guardianNumber = $this->normalizeNumber($guardian->phone_number);
            $fullName = trim("{$student->first_name} {$student->middle_name} {$student->last_name}");
            $sectionInfo = "Grade {$enrollment->section->grade_level} - {$enrollment->section->section_name}";

            $message = "TSHS Attendance Notice\n\n"
                     . "Student: {$fullName}\n"
                     . "Section: {$sectionInfo}\n"
                     . "Date: " . $attendanceDateTime->format('F d, Y') . "\n"
                     . "Time: " . $attendanceDateTime->format('h:i A') . "\n"
                     . "Status: {$validated['status']}\n\n";

            $semaphore->sendSMS($guardianNumber, $message);
        }

        return redirect()->route('attendances')->with('success', 'Attendance added successfully!');
    }

    // 🔹 Index for showing today's attendance
    public function index()
    {
        $today = now('Asia/Manila')->toDateString();

        $currentAcadYear = AcademicYear::where('status', 'current')->first();
        $currentSemester = Semester::where('status', 'current')
            ->where('academic_year_id', $currentAcadYear?->academic_year_id)
            ->first();

        $attendanceLogs = AttendanceLog::with(['enrollment.student', 'enrollment.section', 'addedBy'])
            ->whereDate('date_time', $today)
            ->when($currentSemester, function($query) use ($currentSemester) {
                $query->whereHas('enrollment', function($q) use ($currentSemester) {
                    $q->where('semester_id', $currentSemester->semester_id);
                });
            })
            ->join('student_enrollments', 'attendance_logs.enrollment_id', '=', 'student_enrollments.enrollment_id')
            ->join('sections', 'student_enrollments.section_id', '=', 'sections.section_id')
            ->whereNull('sections.date_archived')
            ->orderBy('sections.grade_level')
            ->orderBy('sections.section_name')
            ->select('attendance_logs.*')
            ->paginate(10);

        $sections = Section::whereNull('date_archived')
            ->orderBy('grade_level')
            ->orderBy('section_name')
            ->get();

        $enrollments = StudentEnrollment::with('student', 'section')
            ->when($currentSemester, function($q) use ($currentSemester) {
                $q->where('semester_id', $currentSemester->semester_id)
                  ->whereNull('date_archived');
            })
            ->get();

        return view('dashboard.attendance', compact('attendanceLogs', 'sections', 'enrollments'));
    }

    // 🔹 Filter by section
    public function filter(Request $request)
    {
        $selectedSection = $request->input('section');
        $today = now('Asia/Manila')->toDateString();

        $currentAcadYear = AcademicYear::where('status', 'current')->first();
        $currentSemester = Semester::where('status', 'current')
            ->where('academic_year_id', $currentAcadYear?->academic_year_id)
            ->first();

        $attendanceLogs = AttendanceLog::with(['enrollment.student', 'enrollment.section', 'addedBy'])
            ->whereDate('date_time', $today)
            ->when($currentSemester, function($query) use ($currentSemester) {
                $query->whereHas('enrollment', function($q) use ($currentSemester) {
                    $q->where('semester_id', $currentSemester->semester_id)
                      ->whereNull('date_archived');
                });
            })
            ->when($selectedSection, function($query) use ($selectedSection) {
                $query->whereHas('enrollment', function($q) use ($selectedSection) {
                    $q->where('section_id', $selectedSection)
                      ->whereNull('date_archived');
                });
            })
            ->join('student_enrollments', 'attendance_logs.enrollment_id', '=', 'student_enrollments.enrollment_id')
            ->join('sections', 'student_enrollments.section_id', '=', 'sections.section_id')
            ->whereNull('sections.date_archived')
            ->orderBy('sections.grade_level')
            ->orderBy('sections.section_name')
            ->select('attendance_logs.*')
            ->paginate(10)
            ->withQueryString();

        $sections = Section::whereNull('date_archived')
            ->orderBy('grade_level')
            ->orderBy('section_name')
            ->get();

        $enrollments = StudentEnrollment::with('student', 'section')
            ->when($currentSemester, function($q) use ($currentSemester) {
                $q->where('semester_id', $currentSemester->semester_id)
                  ->whereNull('date_archived');
            })
            ->get();

        return view('dashboard.attendance', compact('attendanceLogs', 'sections', 'enrollments', 'selectedSection'));
    }

    // 🔹 Normalize phone number
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
}
