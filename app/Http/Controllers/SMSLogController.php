<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\AttendanceLog;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Section;
use App\Models\SMSLog;
use App\Services\TwilioService;
use App\Services\SemaphoreService; 
use App\Models\StudentEnrollment;


class SMSLogController extends Controller
{

public function sendSMSAll(SemaphoreService $semaphore)
{
    $today = now('Asia/Manila')->toDateString();

    $currentAcadYear = AcademicYear::where('status', 'current')->first();
    $currentSemester = Semester::where('status', 'current')
        ->where('academic_year_id', $currentAcadYear?->academic_year_id)
        ->first();

    if (!$currentSemester) {
        return redirect()->back()->with('error', 'No active semester found.');
    }

    $sections = Section::with(['adviser'])->get();

    $sender = auth()->user();
    if (!$sender) {
        return redirect()->back()->with('error', 'You must be logged in to send SMS.');
    }

    $studentsWithoutTimeOutToday = StudentEnrollment::where('semester_id', $currentSemester->semester_id)
        ->with(['attendanceLogs' => function($q) use ($today) {
            $q->whereDate('date_time', $today)
              ->orderBy('date_time', 'asc');
        }])
        ->get()
        ->filter(function($enrollment) {
            $logs = $enrollment->attendanceLogs;
            $lastLog = $logs->last();
            return $lastLog && $lastLog->status === 'Time In';
        })
        ->count();

    if ($studentsWithoutTimeOutToday == 0) {
        return redirect()->back()->with('error', 'No students with Time In today without Time Out. SMS will not be sent.');
    }

    $sentCount = 0;
    $skippedAdvisers = [];

    foreach ($sections as $section) {
        $adviser = $section->adviser;
        if (!$adviser || !$adviser->phone_number) continue;

        // Check if SMS already sent today (Asia/Manila timezone)
        $todayStart = now('Asia/Manila')->startOfDay();
        $todayEnd   = now('Asia/Manila')->endOfDay();

        $alreadySentToday = SMSLog::where('user_id', $adviser->user_id)
            ->whereBetween('sent_at', [$todayStart, $todayEnd])
            ->exists();

        if ($alreadySentToday) {
            $skippedAdvisers[] = $adviser->first_name . ' ' . $adviser->last_name;
            continue;
        }

        $studentsWithoutTimeOut = StudentEnrollment::where('section_id', $section->section_id)
            ->where('semester_id', $currentSemester->semester_id)
            ->with(['student', 'attendanceLogs' => function($q) use ($today) {
                $q->whereDate('date_time', $today)
                  ->orderBy('date_time', 'asc');
            }])
            ->get()
            ->filter(function($enrollment) {
                $logs = $enrollment->attendanceLogs;
                $lastLog = $logs->last();
                return $lastLog && $lastLog->status === 'Time In';
            });

        if ($studentsWithoutTimeOut->isEmpty()) continue;

        $middleInitial = $adviser->middle_name ? strtoupper(substr($adviser->middle_name, 0, 1)) . '.' : '';
        $adviserFullName = trim("{$adviser->first_name} {$middleInitial} {$adviser->last_name}");
        $sectionInfo = "Grade {$section->grade_level} - {$section->section_name}";

        $message = "TSHS Attendance Report\n\n";
        $message .= "Adviser: {$adviserFullName}\n";
        $message .= "Section: {$sectionInfo}\n";
        $message .= "Students without Time Out:\n";

        foreach ($studentsWithoutTimeOut as $enrollment) {
            $student = $enrollment->student;
            if (!$student) continue;
            $mi = $student->middle_name ? strtoupper(substr($student->middle_name,0,1)) . '.' : '';
            $message .= "- {$student->first_name} {$mi} {$student->last_name}\n";
        }

        $message .= "Please check attendance and notify guardians.";

        try {
            $semaphore->sendSMS($this->formatNumber($adviser->phone_number), $message);

            // Save sent_at in Asia/Manila timezone
            SMSLog::create([
                'user_id'=> $adviser->user_id,
                'guardian_id' => null,
                'message' => $message,
                'sent_at' => now('Asia/Manila'),
            ]);

            $sentCount++;
        } catch (\Exception $e) {
            continue;
        }
    }

    $finalMessage = $sentCount > 0
        ? "{$sentCount} attendance report(s) sent successfully."
        : 'All Advisers have already received SMS today.';

    return redirect()->back()->with($sentCount > 0 ? 'success' : 'error', $finalMessage);
}



    private function formatNumber($number)
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

public function sendSMSFromAdviser(SemaphoreService $semaphore)
{
    $today = now('Asia/Manila')->toDateString();
    $adviser = auth()->user(); // current logged-in adviser

    if (!$adviser) {
        return redirect()->back()->with('error', 'You must be logged in as an adviser.');
    }

    $sections = Section::where('user_id', $adviser->user_id)->get();

    $guardianStudentsMap = [];
    $anyStudentWithoutTimeOut = false;

    foreach ($sections as $section) {
        $currentAcadYear = AcademicYear::where('status', 'current')->first();
        $currentSemester = Semester::where('status', 'current')
            ->where('academic_year_id', $currentAcadYear?->academic_year_id)
            ->first();
        if (!$currentSemester) continue;

        $students = StudentEnrollment::where('section_id', $section->section_id)
            ->where('semester_id', $currentSemester->semester_id)
            ->with(['student.guardians', 'attendanceLogs' => function($q) use ($today) {
                $q->whereDate('date_time', $today)
                  ->orderBy('date_time', 'desc');
            }])
            ->get();

        foreach ($students as $enrollment) {
            $student = $enrollment->student;
            if (!$student) continue;

            $lastAttendance = $enrollment->attendanceLogs->first(); // last scan today
            if (!$lastAttendance || $lastAttendance->status !== 'Time In') continue;

            if ($student->guardians->isEmpty()) continue;

            $anyStudentWithoutTimeOut = true;

          foreach ($student->guardians as $guardian) {
                if (!$guardian->phone_number || $guardian->date_archived !== null) continue;

                $guardianStudentsMap[$guardian->guardian_id]['guardian'] = $guardian;
                $guardianStudentsMap[$guardian->guardian_id]['students'][] = [
                    'name' => trim("{$student->first_name} {$student->middle_name} {$student->last_name}"),
                    'section' => "Grade {$section->grade_level} - {$section->section_name}"
                ];
            }

        }
    }

    if (!$anyStudentWithoutTimeOut) {
        return redirect()->back()->with('error', 'No students with Time In today without Time Out. SMS will not be sent.');
    }

    $smsSent = false;

    foreach ($guardianStudentsMap as $guardianData) {
        $guardian = $guardianData['guardian'];
        $students = $guardianData['students'];

        // Skip if SMS already sent today
        $alreadySent = \App\Models\SMSLog::where('guardian_id', $guardian->guardian_id)
            ->whereDate('sent_at', $today)
            ->exists();

        if ($alreadySent) continue;

        $smsSent = true;

        $message = "TSHS Attendance Alert\n\n";
        foreach ($students as $s) {
            $message .= "Student: {$s['name']}\n";
            $message .= "Section: {$s['section']}\n\n";
        }
        $message .= "Time Out not scanned today.\nPlease remind your child to scan out.";

        try {
            $semaphore->sendSMS($this->formatNumber($guardian->phone_number), $message);

            \App\Models\SMSLog::create([
                'user_id'=> null,
                'guardian_id'      => $guardian->guardian_id,
                'message'          => $message,
                'sent_at'          => now('Asia/Manila'),
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Failed to send SMS to guardian {$guardian->first_name} {$guardian->last_name}: " . $e->getMessage());
        }
    }

    if (!$smsSent) {
        return redirect()->back()->with('error', 'All guardians have already received SMS today.');
    }

    return redirect()->back()->with('success', 'SMS sent to guardians successfully.');
}


public function index()
{
    $smsLogs = SMSLog::with(['sender', 'recipient', 'guardian'])
        ->orderBy('sent_at', 'desc')
        ->paginate(10);

    return view('dashboard.sms', compact('smsLogs'));
}




}

