<?php

namespace App\Http\Controllers;
use App\Models\StudentEnrollment;
use App\Models\Semester;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Imports\StudentEnrollmentImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\AcademicYear;  

class StudentEnrollmentController extends Controller
{
public function index()
{
  $section = Section::where('user_id', auth()->id())
                  ->whereNull('date_archived')
                  ->first();

if (!$section) {
    return redirect()->back()->with('error', 'No active section found.');
}

$sectionId = $section->section_id;

$studentEnrollments = StudentEnrollment::select('student_enrollments.*')
    ->join('students', 'student_enrollments.student_id', '=', 'students.student_id')
    ->with(['student', 'section', 'semester.academicYear'])
    ->where('section_id', $sectionId)
    ->whereHas('semester', function ($query) {
        $query->where('status', 'current')
              ->whereHas('academicYear', function ($subQuery) {
                  $subQuery->where('status', 'current');
              });
    })
    ->orderByRaw("FIELD(status, 'Active', 'NLS', 'Dropped', 'Transferred', 'Unenrolled')")
    ->orderBy('students.last_name', 'asc')
    ->paginate(20);


    $students = Student::whereNull('date_archived')
        ->orderBy('last_name', 'asc')
        ->get();

    $semesters = Semester::whereNull('date_archived')
        ->where('status', 'current')
        ->orderBy('name')
        ->get();

    $sections = Section::where('section_id', $sectionId)->get();

    return view('teacher.studentEnrollment', compact('studentEnrollments', 'students', 'semesters', 'sections'));
}



public function store(Request $request)
{
    $validated = $request->validate([
        "student_id"  => ['required', 'exists:students,student_id'],
        "semester_id" => ['required', 'exists:semesters,semester_id'],
    ]);

    $sectionId = session('adviser_section_id');

    $semester = Semester::where('semester_id', $validated['semester_id'])
        ->where('status', 'current')
        ->first();

    if (!$semester) {
        return back()->with('error', 'Selected semester is not active.');
    }

    $activeAcadYear = $semester->academicYear;
    if (!$activeAcadYear || $activeAcadYear->status !== 'current') {
        return back()->with('error', 'The academic year for this semester is not active.');
    }

    
    $alreadyEnrolled = StudentEnrollment::where('student_id', $validated['student_id'])
        ->where('semester_id', $semester->semester_id)
        ->exists();

    if ($alreadyEnrolled) {
        return back()->with('error', 'Student is already enrolled in the active semester.');
    }

    try {
        StudentEnrollment::create([
            'student_id'  => $validated['student_id'],
            'section_id'  => $sectionId,
            'status' => 'Active',
            'semester_id' => $validated['semester_id'],
        ]);

        return redirect()->route('teacher.studentEnrollments')->with('success', 'Student enrolled successfully.');
    } catch (\Exception $e) {
        return back()->with('error', $e->getMessage());
    }
}

public function update(Request $request, $id)
{
    $validated = $request->validate([
        "status" => ['required', 'string'],
        "remarks" => ['nullable', 'string'],
    ]);

    try {
        $student = StudentEnrollment::findOrFail($id);

        // Update status
        $student->status = $validated['status'];

        // Update remarks ONLY if NLS
        if ($validated['status'] === 'NLS') {
            $student->remarks = $validated['remarks'];
            $student->date_archived = now();
        }
        else {
            // Clear remarks if active
            $student->remarks = null;
            $student->date_archived = null;
        }

        $student->save();

        return redirect()->route('teacher.studentEnrollments')
                         ->with('success', 'Student enrollment updated successfully.');
    } catch (\Exception $e) {
        return redirect()->back()
                         ->with('error', 'Failed to update student enrollment, please try again.');
    }
}


 public function search(Request $request)
    {
        $search = $request->input('search');
        $sectionId = session('adviser_section_id');

       
        $currentAcadYear = AcademicYear::where('status', 'current')->first();
        $currentSemester = Semester::where('status', 'current')
                            ->where('academic_year_id', $currentAcadYear?->academic_year_id)
                            ->first();

        $studentEnrollments = StudentEnrollment::with(['student', 'section', 'semester.academicYear'])
            ->whereNull('date_archived')
            ->where('section_id', $sectionId)
            ->whereHas('semester', function ($query) {
                $query->where('status', 'current')
                      ->whereHas('academicYear', function ($subQuery) {
                          $subQuery->where('status', 'current');
                      });
            })
            ->when($search, function ($query, $search) {
                $query->whereHas('student', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('middle_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('lrn', 'like', "%{$search}%")
                      ->orWhereRaw("CONCAT(first_name, ' ', middle_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                      ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                });
            })
            ->orderBy('student_id', 'asc')
            ->paginate(20)
            ->appends(['search' => $search]); 

        $students = Student::whereNull('date_archived')
            ->orderBy('last_name', 'asc')
            ->get();

        $semesters = Semester::whereNull('date_archived')
            ->where('status', 'current')
            ->orderBy('name')
            ->get();

        $sections = Section::where('section_id', $sectionId)->get();

        return view('teacher.studentEnrollment', compact('studentEnrollments', 'students', 'semesters', 'sections', 'search'));
    }

public function searchByLRN(Request $request)
{
    $query = $request->query('query');
    $lrn = $request->query('lrn');

    $sectionId = session('adviser_section_id');
    $section = Section::find($sectionId);

    // If the session section is archived or not found, pick the first active section
    if (!$section || $section->date_archived) {
        $section = Section::where('user_id', auth()->id())
                          ->whereNull('date_archived')
                          ->first();

        if ($section) {
            session(['adviser_section_id' => $section->section_id]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No active section found for this adviser.'
            ]);
        }
    }

    $adviserGradeLevel = $section->grade_level;

    // ================================
    // CASE 1: LIVE SEARCH
    // ================================
    if ($query) {
        $students = Student::where('grade_level', $adviserGradeLevel)
                           ->whereNull('date_archived')
                           ->where(function($q) use ($query) {
                               $q->where('lrn', 'like', "%{$query}%")
                                 ->orWhere('first_name', 'like', "%{$query}%")
                                 ->orWhere('middle_name', 'like', "%{$query}%")
                                 ->orWhere('last_name', 'like', "%{$query}%")
                                 ->orWhereRaw("CONCAT(first_name,' ',middle_name,' ',last_name) LIKE ?", ["%{$query}%"])
                                 ->orWhereRaw("CONCAT(first_name,' ',last_name) LIKE ?", ["%{$query}%"]);
                           })
                           ->orderBy('last_name')
                           ->get();

        return response()->json([
            'success' => true,
            'students' => $students
        ]);
    }

    // ================================
    // CASE 2: EXACT LRN SEARCH
    // ================================
    if ($lrn) {
        $student = Student::where('lrn', $lrn)
                          ->where('grade_level', $adviserGradeLevel)
                          ->first();

        if (!$student) {
            return response()->json(['success' => false]);
        }

        // Get current AY + sem
        $activeAcadYear = AcademicYear::where('status', 'current')->first();
        $activeSemester = Semester::where('status', 'current')
                                  ->where('academic_year_id', $activeAcadYear?->academic_year_id)
                                  ->first();

        $father = $student->guardians->firstWhere('guardian_type', 'Father');
        $mother = $student->guardians->firstWhere('guardian_type', 'Mother');
        $other  = $student->guardians->firstWhere('guardian_type', 'Guardian');

        return response()->json([
            'success' => true,
            'student' => $student,
            'grade_level' => $adviserGradeLevel,
            'academic_year' => $activeAcadYear?->name,
            'semester' => $activeSemester?->name,
            'semester_id' => $activeSemester?->semester_id,

            'guardians' => [
        'father'   => $father ? $father->first_name . ' ' . $father->last_name : null,
        'mother'   => $mother ? $mother->first_name . ' ' . $mother->last_name : null,
        'guardian' => $other  ? $other->first_name  . ' ' . $other->last_name  : null,
             ]
        ]);
    }

    return response()->json(['success' => false]);
}


public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xls,xlsx',
    ]);

    // Get active section for the logged-in adviser
    $section = Section::where('user_id', auth()->id())
                      ->whereNull('date_archived')
                      ->first();

    if (!$section) {
        return back()->with('error', 'No active section found for this adviser.');
    }

    $sectionId = $section->section_id;

    // Get current semester
    $semesterId = Semester::where('status', 'current')->value('semester_id');

    $import = new StudentEnrollmentImport($sectionId, $semesterId);
    Excel::import($import, $request->file('file'));

    return back()->with('success', "{$import->importedCount} enrolled, {$import->skippedCount} skipped.");
}



}
