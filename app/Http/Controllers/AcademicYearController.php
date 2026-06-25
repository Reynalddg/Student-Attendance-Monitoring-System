<?php

namespace App\Http\Controllers;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Carbon\Carbon;


class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::whereNull('date_archived')
                ->orderByRaw("CASE WHEN status = 'active' THEN 1 ELSE 2 END ASC")
                ->orderBy('status', 'asc')
                ->paginate(10);

                return view('dashboard.academicYear', compact('academicYears'));
    }

   public function store(Request $request)
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date', 'before:end_date'],
            'end_date'   => ['required', 'date', 'after:start_date'],
            'status'     => ['required', 'in:current,previous,next'],
        ]);

        // Auto-generate name
        $startYear = Carbon::parse($validated['start_date'])->format('Y');
        $endYear = Carbon::parse($validated['end_date'])->format('Y');
        $validated['name'] = "{$startYear} - {$endYear}";

        $overlap = AcademicYear::whereNull('date_archived')
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('start_date', '<=', $validated['start_date'])
                          ->where('end_date', '>=', $validated['end_date']);
                    });
            })
            ->exists();

        if ($overlap) {
            return redirect()->back()->with('error', 'The academic year overlaps with an existing one.');
        }

        try {
            if ($validated['status'] === 'current') {
                AcademicYear::where('status', 'current')->update(['status' => 'previous']);
            }

            AcademicYear::create($validated);

            return redirect()->route('academicYears')->with('success', 'Academic year added successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create academic year. Please try again.');
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date', 'before:end_date'],
            'end_date'   => ['required', 'date', 'after:start_date'],
            'status'     => ['required', 'in:current,previous,next'],
        ]);

        // Auto-generate name
        $startYear = Carbon::parse($validated['start_date'])->format('Y');
        $endYear = Carbon::parse($validated['end_date'])->format('Y');
        $validated['name'] = "{$startYear} - {$endYear}";

        $overlap = AcademicYear::whereNull('date_archived')
            ->where('academic_year_id', '!=', $id)
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                      ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                      ->orWhere(function ($q) use ($validated) {
                          $q->where('start_date', '<=', $validated['start_date'])
                            ->where('end_date', '>=', $validated['end_date']);
                      });
            })
            ->exists();

        if ($overlap) {
            return redirect()->back()->with('error', 'The academic year overlaps with an existing one.');
        }

        try {
            $academicYear = AcademicYear::findOrFail($id);

            // Update current status logic
            if ($validated['status'] === 'current') {
                $previousCurrent = AcademicYear::where('status', 'current')
                    ->where('academic_year_id', '!=', $id)
                    ->first();

                if ($previousCurrent) {
                    $previousCurrent->status = 'previous';
                    if ((int)$previousCurrent->students_promoted === 0) {
                        $this->promoteStudents($previousCurrent);
                        $previousCurrent->students_promoted = 1;
                    }
                    $previousCurrent->save();
                }
            }

            $academicYear->update($validated);

            if ($validated['status'] === 'previous' && (int)$academicYear->students_promoted === 0) {
                $this->promoteStudents($academicYear);
                $academicYear->students_promoted = 1;
                $academicYear->save();
            }

            return redirect()->route('academicYears')->with('success', 'Academic year updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update academic year. Please try again. Error: ' . $e->getMessage());
        }
    }


public function destroy($id)
{
    try {
        $academicYear = AcademicYear::findOrFail($id);

        if ($academicYear->status === 'current') {
            return redirect()->back()->with('error', 'You cannot archive an active academic year.');
        }

        $academicYear->date_archived = Carbon::now('Asia/Manila');

        // Promote students and handle grade 12 archiving
        if ((int)$academicYear->students_promoted === 0) {
            $this->promoteStudents($academicYear);
            $academicYear->students_promoted = 1;
        }

        $academicYear->save();

        return redirect()->route('academicYears')->with('success', 'Academic year archived successfully.');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to archive academic year. Please try again. Error: ' . $e->getMessage());
    }
}




    private function promoteStudents($acadYear)
    {
        // Get the second semester of the academic year
        $secondSemester = \App\Models\Semester::where('academic_year_id', $acadYear->academic_year_id)
            ->where('name', 'Second Semester')
            ->first();

        if (!$secondSemester) return;

        // Get all students enrolled in second semester with Active status
        $students = \App\Models\Student::whereHas('enrollments', function($q) use ($secondSemester) {
            $q->where('semester_id', $secondSemester->semester_id)
              ->where('status', 'Active');
        })->get();

        foreach ($students as $student) {
            $currentGrade = (int) filter_var($student->grade_level, FILTER_SANITIZE_NUMBER_INT);

            if ($currentGrade < 12) {
                $student->grade_level =  ($currentGrade + 1);
            } else {
                $student->date_archived = now('Asia/Manila'); // optional graduation
            }

            $student->save();
        }
    }



    public function search(Request $request){
        $search = $request->input('search');

         $academicYears = AcademicYear::whereNull('date_archived')
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('start_date', 'like', "%{$search}%")
                  ->orWhere('end_date', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(start_date, ' - ', end_date) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("CONCAT(name, ' ', status) LIKE ?", ["%{$search}%"]);
            });
        })
        ->orderByRaw("CASE WHEN status = 'current' THEN 1 ELSE 2 END ASC")
        ->paginate(10)
        ->appends(['search' => $search]);

        return view('dashboard.academicYear', compact('academicYears'));
    }

    public function archivedSearch(Request $request)
{
     $search = $request->input('search');

         $academicYears = AcademicYear::whereNotNull('date_archived')
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('start_date', 'like', "%{$search}%")
                  ->orWhere('end_date', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(start_date, ' - ', end_date) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("CONCAT(name, ' ', status) LIKE ?", ["%{$search}%"]);
            });
        })
        ->orderByRaw("CASE WHEN status = 'current' THEN 1 ELSE 2 END ASC")
        ->paginate(10)
        ->appends(['search' => $search]);

        return view('dashboard.academicYear', compact('academicYears'));
}


public function archived()
{
     $academicYears = AcademicYear::whereNotNull('date_archived')
                ->orderByRaw("CASE WHEN status = 'active' THEN 1 ELSE 2 END ASC")
                ->orderBy('name', 'desc')
                ->paginate(10);

                return view('dashboard.academicYear', compact('academicYears'));
}

public function restore($id)
{
    $academicYear = AcademicYear::findOrFail($id);
    $academicYear->update([
        'date_archived' => null,
        'students_promoted' => 0,
    ]);
    return redirect()->route('academicYears.archived')->with('success', 'Academic year restored successfully.');
}

}
