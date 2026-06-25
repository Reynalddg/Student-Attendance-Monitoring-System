<?php

namespace App\Http\Controllers;

use App\Exports\SF1Export;
use chillerlan\QRCode\{QRCode, QROptions};
use Carbon\Carbon;
use App\Mail\SendStudentQrCodeMail;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Section;
use App\Models\Guardian;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Imports\StudentsImport;
use App\Models\StudentEnrollment;
use App\Exports\SF1PDFExport;


class StudentController extends Controller
{
   public function index()
{
    $currentAcadYear = AcademicYear::where('status', 'current')->first();
    $currentSemester = Semester::where('status', 'current')
                        ->where('academic_year_id', $currentAcadYear?->academic_year_id)
                        ->first();

    $students = Student::with( 'enrollments')
        ->whereNull('date_archived')
        ->orderBy('last_name', 'asc')
        ->orderBy('first_name', 'asc')
        ->paginate(20);

    $students->getCollection()->transform(function($student) use ($currentSemester) {
        $enrollment = $student->enrollments->firstWhere('semester_id', $currentSemester?->semester_id);
        $student->current_status = $enrollment?->status ?? 'No Enrollment';
        return $student;
    });

    $guardians = Guardian::whereNull('date_archived')
        ->get();

    return view('dashboard.student', compact('students', 'guardians'));
}


   public function store(Request $request)
{
    $validated = $request->validate([
        "first_name"  => ['required', 'string', 'min:2'],
        "middle_name" => ['nullable', 'string', 'min:2'],
        "last_name"   => ['required', 'string', 'min:2'],
        "suffix"   => ['nullable', 'string', 'min:2'],
        "lrn"         => ['required', 'digits:12', 'unique:students,lrn'],
        "barangay"    => ['required', 'string', 'min:3'],
        "municipality"=> ['required', 'string', 'min:6'],
        "province"    => ['required', 'string', 'min:6'],
        "gender"      => ['required', 'in:Male,Female'],
        'birthdate'   => ['required', 'date', 'before:today', 'after:1900-01-01'],
        "religion"    => ['required', 'string', 'min:6'],
        'grade_level' => ['required', 'string'],
        "remarks"   => ['nullable', 'string', 'min:2'],
        'image'       => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048']
    ]);

    $imagePath = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('StudentImage', 'public');
    }
    $validated['image'] = $imagePath;
    $validated['date_created'] = now('Asia/Manila');

    try {
        $student = Student::create($validated);

        // Generate QR code (still keep this)
        $fullName = preg_replace('/[^A-Za-z0-9]/', '_',
            $student->first_name . '_' . $student->middle_name . '_' . $student->last_name
        );
        $fileName = $fullName . '.png';
        $savePath = "C:/Users/reyna/capstone/studentQRCode/" . $fileName;

        if (!file_exists(dirname($savePath))) {
            mkdir(dirname($savePath), 0777, true);
        }

        $options = new \chillerlan\QRCode\QROptions([
            'outputType' => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel'   => \chillerlan\QRCode\QRCode::ECC_L,
            'scale'      => 8,
        ]);

        $qrcode = new \chillerlan\QRCode\QRCode($options);
        $qrcode->render($student->lrn, $savePath); 

        // ✅ Email sending removed

        return redirect()->route('students')->with('success', 'Student created successfully!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to create student. Please try again. Error: ' . $e->getMessage());
    }
}


   public function update(Request $request, $id)
{
    $student = Student::findOrFail($id);

    $validated = $request->validate([
        "first_name"  => ['required', 'string', 'min:2'],
        "middle_name" => ['nullable', 'string', 'min:2'],
        "last_name"   => ['required', 'string', 'min:2'],
        "suffix"   => ['nullable', 'string', 'min:2'],
        "lrn"         => ['required', 'digits:12', 'unique:students,lrn,' . $id . ',student_id'],
        "barangay"    => ['required', 'string', 'min:4'],
        "municipality"=> ['required', 'string', 'min:6'],
        "province"    => ['required', 'string', 'min:6'],
        "gender"      => ['required', 'in:Male,Female'],
        'birthdate'   => ['required', 'date', 'before:today', 'after:1900-01-01'],
        "religion"    => ['required', 'string', 'min:6'],
        "grade_level" => ['required', 'string'],
        "remarks"   => ['nullable', 'string', 'min:2'],
        'image'       => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048']
    ]);

    if ($request->hasFile('image')) {
        if ($student->image && Storage::disk('public')->exists($student->image)) {
            Storage::disk('public')->delete($student->image);
        }
        $validated['image'] = $request->file('image')->store('StudentImage', 'public');
    } else {
        $validated['image'] = $student->image;
    }

    $lrnChanged = $validated['lrn'] !== $student->lrn;

    $student->update($validated);

    if ($lrnChanged) {
        // Re-generate QR code if LRN changed
        $fullName = preg_replace('/[^A-Za-z0-9]/', '_',
            $student->first_name . '_' . $student->middle_name . '_' . $student->last_name
        );
        $fileName = $fullName . '.png';
        $savePath = "C:/Users/reyna/capstone/studentQRCode/" . $fileName;

        if (!file_exists(dirname($savePath))) {
            mkdir(dirname($savePath), 0777, true);
        }

        $options = new \chillerlan\QRCode\QROptions([
            'outputType' => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel'   => \chillerlan\QRCode\QRCode::ECC_L,
            'scale'      => 8,
        ]);

        $qrcode = new \chillerlan\QRCode\QRCode($options);
        $qrcode->render($student->lrn, $savePath);
    }

    // ✅ Email sending removed

    return redirect()->route('students')->with('success', 'Student updated successfully!');
}


    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->date_archived = Carbon::now('Asia/Manila');
        $student->save();

        return redirect()->route('students')->with('success', 'Student archived successfully!');
    }

public function search(Request $request)
{
    $search = $request->input('search');
    $sectionId = session('adviser_section_id');

    $currentAcadYear = AcademicYear::where('status', 'current')->first();
    $currentSemester = Semester::where('status', 'current')
                        ->where('academic_year_id', $currentAcadYear?->academic_year_id)
                        ->first();

    $students = Student::with([
            'enrollments.section', 
            'enrollments.semester.academicYear', 
            'guardian'
        ])
        ->whereNull('date_archived')
        ->whereHas('enrollments', function ($q) use ($sectionId, $currentSemester) {
            $q->where('section_id', $sectionId)
              ->where('semester_id', $currentSemester?->semester_id);
        })
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('lrn', 'like', "%{$search}%")

                  ->orWhereHas('guardian', function ($q2) use ($search) {
                      $q2->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('enrollments.section', function ($q3) use ($search) {
                      $q3->where('section_name', 'like', "%{$search}%");
                  })
                
                  ->orWhereRaw("CONCAT(first_name, ' ', middle_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
            });
        })
        ->orderBy('last_name')
        ->orderBy('first_name')
        ->paginate(5);

    $sections = Section::whereNull('date_archived')
        ->orderBy('grade_level')
        ->orderBy('section_name')
        ->get();

    $guardians = Guardian::whereNull('date_archived')
        ->orderBy('father_name')
        ->get();

        $academicYears = AcademicYear::whereNull('date_archived')
    ->orderBy('start_date', 'desc')
    ->get();

    $semesters = Semester::whereNull('date_archived')
    ->orderBy('semester_id', 'asc')
    ->get();

    return view('teacher.studentView', compact('students', 'sections', 'guardians', 'academicYears', 'semesters'));
}



   public function studentView()
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

    $adviserSection = Section::where('section_id', $sectionId)->first();

    $academicYears = AcademicYear::orderBy('name', 'asc')->get();
    $semesters = Semester::orderBy('name', 'asc')->get();


   $students = Student::with(['enrollments' => function($q) use ($currentSemester, $sectionId) {
        $q->where('semester_id', $currentSemester?->semester_id)
          ->where('section_id', $sectionId)
          ->whereNull('date_archived'); // optional: kung may archived flag sa enrollment
    }])
    ->whereHas('enrollments', function($q) use ($currentSemester, $sectionId) {
        $q->where('semester_id', $currentSemester?->semester_id)
          ->where('section_id', $sectionId)
          ->whereNull('date_archived');
    })
    ->whereNull('date_archived')
    ->orderBy('last_name', 'asc')
    ->orderBy('first_name', 'asc')
    ->paginate(20);

// Add current_status based on filtered enrollment
$students->getCollection()->transform(function($student) use ($currentSemester, $sectionId) {
    $enrollment = $student->enrollments->firstWhere('semester_id', $currentSemester?->semester_id);
    $student->current_status = $enrollment?->status ?? 'No Enrollment';
    return $student;
});


    $guardians = Guardian::whereNull('date_archived')
        ->get();


    // Sections and guardians
    $sections = Section::whereNull('date_archived')
        ->orderBy('grade_level', 'asc')
        ->orderBy('section_name', 'asc')
        ->get();

  

    return view('teacher.studentview', compact(
        'students',
        'sections',
        'guardians',
        'academicYears',
        'semesters',
        'adviserSection'
    ));
}

public function exportSF1(Request $request)
{
    $academicYearId = $request->academic_year;
    $semesterId     = $request->semester;

    if (!$academicYearId || !$semesterId) {
        return redirect()->back()->with('error', 'Please select academic year and semester.');
    }

    $semester = Semester::find($semesterId);
    if (!$semester) {
        return redirect()->back()->with('error', 'Semester not found.');
    }

    // Determine start and end of semester
    $startOfSemester = Carbon::parse($semester->start_date)->startOfDay();
    $endOfSemester   = Carbon::parse($semester->end_date)->endOfDay();

    // Get any enrollment in this semester, even if archived
    $enrollment = StudentEnrollment::where('semester_id', $semester->semester_id)
        ->where('date_created', '<=', $endOfSemester)
        ->where(function($q) use ($endOfSemester) {
            $q->whereNull('date_archived')
              ->orWhere('date_archived', '>=', $endOfSemester);
        })
        ->first();

    if (!$enrollment) {
        return redirect()->back()->with('error', 'No enrollments found for this semester.');
    }

    $sectionId = $enrollment->section_id;

    $section = Section::find($sectionId);
    $fileName = 'SF1_' . ($section->section_name ?? 'Section') . '_' . now()->format('Y-m-d') . '.xlsx';

    return Excel::download(new SF1Export($academicYearId, $semesterId, $sectionId), $fileName);
}


public function exportSF1PDF(Request $request)
{
    $academicYearId = $request->academic_year;
    $semesterId     = $request->semester;

    if (!$academicYearId || !$semesterId) {
        return redirect()->back()->with('error', 'Please select academic year and semester.');
    }

    $semester = Semester::find($semesterId);
    if (!$semester) {
        return redirect()->back()->with('error', 'Semester not found.');
    }

    // Determine start and end of semester
    $startOfSemester = Carbon::parse($semester->start_date)->startOfDay();
    $endOfSemester   = Carbon::parse($semester->end_date)->endOfDay();

    // Get any enrollment in this semester, even if archived
    $enrollment = StudentEnrollment::where('semester_id', $semester->semester_id)
        ->where('date_created', '<=', $endOfSemester)
        ->where(function($q) use ($endOfSemester) {
            $q->whereNull('date_archived')
              ->orWhere('date_archived', '>=', $endOfSemester);
        })
        ->first();

    if (!$enrollment) {
        return redirect()->back()->with('error', 'No enrollments found for this semester.');
    }

    $sectionId = $enrollment->section_id;

    $export = new \App\Exports\SF1PDFExport($academicYearId, $semesterId, $sectionId);
    return $export->download();
}




public function searchStudent(Request $request)
{
    $search = $request->input('search');

    $students = Student::with(['guardians' => function($query) {
            $query->whereNull('date_archived'); // keep all guardians
        }])
        ->whereNull('date_archived')
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('lrn', 'like', "%{$search}%")
                  ->orWhereHas('guardians', function ($q2) use ($search) {
                      $q2->where('first_name', 'like', "%{$search}%")
                         ->orWhere('middle_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('relation', 'like', "%{$search}%");
                  });
            });
        })
        ->orderBy('last_name')
        ->orderBy('first_name')
        ->paginate(7)
        ->appends(['search' => $search]);

    return view('dashboard.student', compact('students', 'search'));
}


public function archivedSearch(Request $request)
{
    $search = $request->input('search');

    $students = Student::with('guardian')
        ->whereNotNull('date_archived')
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('lrn', 'like', "%{$search}%")

                  ->orWhereHas('guardian', function ($q2) use ($search) {
                      $q2->where('father_name', 'like', "%{$search}%")
                         ->orWhere('mother_name', 'like', "%{$search}%")
                         ->orWhere('guardian_name', 'like', "%{$search}%");
                  })
                  ->orWhereRaw("CONCAT_WS(' ', first_name, middle_name, last_name) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
            });
        })
        ->orderBy('last_name')
        ->orderBy('first_name')
        ->paginate(7)
        ->appends(['search' => $search]);

    $guardians = Guardian::whereNull('date_archived')
        ->orderBy('guardian_name')
        ->get();

    return view('dashboard.student', compact('students', 'guardians', 'search'));
}


public function archived()
{
    $currentAcadYear = AcademicYear::where('status', 'current')->first();
    $currentSemester = Semester::where('status', 'current')
                        ->where('academic_year_id', $currentAcadYear?->academic_year_id)
                        ->first();

    $students = Student::with(['guardian', 'enrollments'])
        ->whereNotNull('date_archived')
        ->orderBy('last_name', 'asc')
        ->orderBy('first_name', 'asc')
        ->paginate(20);

    $students->getCollection()->transform(function($student) use ($currentSemester) {
        $enrollment = $student->enrollments->firstWhere('semester_id', $currentSemester?->semester_id);
        $student->current_status = $enrollment?->status ?? 'No Enrollment';
        return $student;
    });

    $guardians = Guardian::whereNull('date_archived')
        ->orderBy('father_name')
        ->get();


        return view('dashboard.student', compact('students', 'guardians'));
}

  public function restore($id)
    {
        $student = Student::findOrFail($id);

        $existing = Student::where('lrn', $student->lrn)
                        ->whereNull('date_archived')
                        ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Cannot restore student. Another active student has the same LRN.');
        }

        $student->update(['date_archived' => null]);

        return redirect()->route('students.archived')->with('success', 'Student restored successfully.');

    }



public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xls,xlsx'
    ]);

    $import = new StudentsImport();
    Excel::import($import, $request->file('file'));

    $imported = $import->importedCount;
    $skipped = $import->skippedCount;

    return redirect()->back()->with('success', "Students imported successfully! Imported: $imported");
}


}
