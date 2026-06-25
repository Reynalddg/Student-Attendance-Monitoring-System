<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\Semester;
use Carbon\Carbon;

class GuardianController extends Controller
{
   public function index()
{
    $guardians = Guardian::with('student') 
        ->whereNull('date_archived')
        ->orderBy('first_name')
        ->paginate(20);

$students = Student::with(['guardians' => function($query) {
        $query->whereNull('date_archived')
              ->orderBy('first_name'); // optional: alphabetical
    }])
    ->whereNull('date_archived')
    ->orderBy('last_name')
    ->orderBy('first_name')
    ->get();



    return view('dashboard.guardian', compact('guardians', 'students'));
}


public function store(Request $request)
{
    $validated = $request->validate([
        'guardian_type' => ['required', 'string', 'max:255'],
        'first_name'    => ['required', 'string', 'max:255'],
        'middle_name'   => ['nullable', 'string', 'max:255'],
        'last_name'     => ['required', 'string', 'max:255'],
        'suffix'        => ['nullable', 'string', 'max:255'],
        'relation'      => ['nullable', 'string', 'max:255'],
       'phone_number' => [
    'nullable',
    'regex:/^(09|\+639)\d{9}$/',
    function ($attribute, $value, $fail) use ($request) {
        if ($value) {
            $exists = Guardian::where('student_id', $request->student_id)
                ->where('phone_number', $value)
                ->whereNull('date_archived')
                ->exists();
            if ($exists) {
                $fail('This phone number is already used by another guardian.');
            }
        }
    },
],


        'student_id' => ['required', 'exists:students,student_id'],
    ]);

    // Check if guardian_type already exists for this student
    $typeExists = Guardian::where('student_id', $validated['student_id'])
        ->where('guardian_type', $validated['guardian_type'])
        ->whereNull('date_archived')
        ->exists();

    if ($typeExists) {
        return redirect()->back()->with('error', "A {$validated['guardian_type']} already exists for this student.");
    }

    try {
        Guardian::create($validated);
        return redirect()->route('dashboard.guardians')->with('success', 'Guardian created successfully!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to create guardian: ' . $e->getMessage());
    }
}


 public function update(Request $request, $id)
{
    $validated = $request->validate([
        'guardian_type' => ['required', 'string', 'max:255'],
        'first_name'    => ['required', 'string', 'max:255'],
        'middle_name'   => ['nullable', 'string', 'max:255'],
        'last_name'     => ['required', 'string', 'max:255'],
        'suffix'        => ['nullable', 'string', 'max:255'],
        'relation'      => ['nullable', 'in:Relative,Guardian'],
       'phone_number' => [
    'nullable',
    'regex:/^(09|\+639)\d{9}$/',
    function ($attribute, $value, $fail) use ($request) {
        if ($value) {
            $exists = Guardian::where('student_id', $request->student_id)
                ->where('phone_number', $value)
                ->whereNull('date_archived')
                ->exists();
            if ($exists) {
                $fail('This phone number is already used by another guardian.');
            }
        }
    },
],

    ]);

    $guardian = Guardian::findOrFail($id);

    // Check if guardian_type already exists for this student excluding current guardian
    $typeExists = Guardian::where('student_id', $guardian->student_id)
        ->where('guardian_type', $validated['guardian_type'])
        ->whereNull('date_archived')
        ->where('guardian_id', '!=', $id)
        ->exists();

    if ($typeExists) {
        return redirect()->back()->with('error', "A {$validated['guardian_type']} already exists for this student.");
    }

    try {
        $guardian->update($validated);
        return redirect()->route('dashboard.guardians')->with('success', 'Guardian updated successfully!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to update guardian. Please try again.');
    }
}



    public function destroy($id)
    {
        try {
            $guardian = Guardian::findOrFail($id);
            $guardian->date_archived = Carbon::now('Asia/Manila');
            $guardian->save();

            return redirect()->route('dashboard.guardians')->with('success', 'Guardian archived successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to archive guardian. Please try again.');
        }
    }

  public function search(Request $request)
{
    $search = $request->input('search');

    $guardians = Guardian::with('student')
        ->whereNull('date_archived')
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($q2) use ($search) {
                      $q2->where('first_name', 'like', "%{$search}%")
                         ->orWhere('middle_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhereRaw("CONCAT(first_name, ' ', COALESCE(middle_name,''), ' ', last_name) LIKE ?", ["%{$search}%"])
                         ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                  });
            });
        })
        ->orderBy('first_name')
        ->paginate(10)
        ->appends(['search' => $search]);

    $students = Student::whereNull('date_archived')
        ->orderBy('last_name')
        ->orderBy('first_name')
        ->get();

    return view('dashboard.guardian', compact('guardians', 'students', 'search'));
}




 public function guardianView()
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

    $guardians = Guardian::with(['students.enrollments' => function ($q) use ($sectionId, $currentSemester) {
            $q->where('section_id', $sectionId)
              ->where('semester_id', $currentSemester?->semester_id); // ✅ filter via semester
        }])
        ->whereNull('date_archived')
        ->whereHas('students.enrollments', function ($q) use ($sectionId, $currentSemester) {
            $q->where('section_id', $sectionId)
              ->where('semester_id', $currentSemester?->semester_id); // ✅ filter via semester
        })
        ->orderBy('father_name')
        ->paginate(5);

    return view('teacher.guardianview', compact('guardians'));
}

public function searchGuardian(Request $request)
{
    $search = $request->input('search');
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

    $guardians = Guardian::with(['students.enrollments' => function ($q) use ($sectionId, $currentSemester) {
            $q->where('section_id', $sectionId)
              ->where('semester_id', $currentSemester?->semester_id);
        }])
        ->whereNull('date_archived')
        ->whereHas('students.enrollments', function ($q) use ($sectionId, $currentSemester) {
            $q->where('section_id', $sectionId)
              ->where('semester_id', $currentSemester?->semester_id);
        })
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                  ->orWhereHas('students', function ($q2) use ($search) {
                      $q2->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                  });
            });
        })
        ->orderBy('first_name')
        ->paginate(10);

    return view('teacher.guardianview', compact('guardians'));
}

   public function archivedSearch(Request $request)
{
    $search = $request->input('search');

    $guardians = Guardian::with('student')
        ->whereNotNull('date_archived')
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($q2) use ($search) {
                      $q2->where('first_name', 'like', "%{$search}%")
                         ->orWhere('middle_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhereRaw("CONCAT(first_name, ' ', COALESCE(middle_name,''), ' ', last_name) LIKE ?", ["%{$search}%"])
                         ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                  });
            });
        })
        ->orderBy('first_name')
        ->paginate(10)
        ->appends(['search' => $search]);

    $students = Student::whereNull('date_archived')
        ->orderBy('last_name')
        ->orderBy('first_name')
        ->get();

    return view('dashboard.guardian', compact('guardians', 'students', 'search'));
}

    public function archived()
    {
          $guardians = Guardian::with('student') 
                ->whereNotNull('date_archived')
                ->orderBy('first_name')
                ->paginate(20);

            $students = Student::whereNull('date_archived') 
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get();

        return view('dashboard.guardian', compact('guardians', 'students'));
    
    }

public function restore($id)
{
    $guardian = Guardian::findOrFail($id);

    $existing = Guardian::whereRaw('LOWER(TRIM(first_name)) = ?', [strtolower(trim($guardian->first_name))])
        ->whereRaw('LOWER(TRIM(middle_name)) = ?', [strtolower(trim($guardian->middle_name))])
        ->whereRaw('LOWER(TRIM(last_name)) = ?', [strtolower(trim($guardian->last_name ?? ''))])
        ->where('phone_number', trim($guardian->phone_number))
        ->whereNull('date_archived')
        ->where('guardian_id', '!=', $id)
        ->first();

    if ($existing) {
        return redirect()->route('guardians.archived')
                         ->with('error', 'Cannot restore this guardian because another active guardian with the same details already exists.');
    }

    $guardian->update(['date_archived' => null]);

    return redirect()->route('guardians.archived')->with('success', 'Guardian restored successfully.');
}


}
