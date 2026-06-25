<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Semester;
use App\Models\AcademicYear;

class SemesterController extends Controller
{
    public function index(){
        $semesters = Semester::whereNull('date_archived')
                    ->orderByRaw("CASE WHEN status = 'active' THEN 1 ELSE 2 END ASC")
                     ->orderBy('status', 'asc')
                    ->paginate(10);

        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('dashboard.semester', compact('semesters', 'academicYears'));
    }

     public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'min:8', 'max:255'],
            'start_date'       => ['required', 'date', 'before:end_date'],
            'end_date'         => ['required', 'date', 'after:start_date'],
            'academic_year_id' => ['required'],
            'status' => ['required', 'in:current,previous,next'],
        ]);

        $overlap = Semester::where('academic_year_id', $validated['academic_year_id'])
            ->whereNull('date_archived')
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
            return redirect()->back()->with('error', 'This semester overlaps with another semester in the same academic year.');
        }

        try {
          if ($validated['status'] === 'current') {
                Semester::where('academic_year_id', $validated['academic_year_id'])
                        ->where('status', 'current')
                        ->update(['status' => 'previous']);
                }

            Semester::create([
                'name'             => $validated['name'],
                'academic_year_id' => $validated['academic_year_id'],
                'start_date'       => $validated['start_date'],
                'end_date'         => $validated['end_date'],
                'status'           => $validated['status'],
                'date_created'     => now(),
            ]);

            return redirect()->route('semesters')->with('success', 'Semester created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create semester. Please try again later.');
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'             => ['required', 'string', 'min:8', 'max:255'],
            'start_date'       => ['required', 'date', 'before:end_date'],
            'end_date'         => ['required', 'date', 'after:start_date'],
            'academic_year_id' => ['required'],
            'status' => ['required', 'in:current,previous,next'],
        ]);

        $overlap = Semester::where('academic_year_id', $validated['academic_year_id'])
            ->where('semester_id', '!=', $id)
            ->whereNull('date_archived')
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
            return redirect()->back()->with('error', 'This semester overlaps with another semester in the same academic year.');
        }

        try {
            $semester = Semester::findOrFail($id);

         if ($validated['status'] === 'current') {
                Semester::where('status', 'current')
                        ->where('semester_id', '!=', $id)
                        ->update(['status' => 'previous']);
            }



            $semester->update($validated);

            return redirect()->route('semesters')->with('success', 'Semester updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update semester. Please try again later.');
        }
    }

public function destroy($id)
{
    try {
        $semester = Semester::findOrFail($id);

      if (strtolower($semester->status) === 'current') {
            return redirect()->back()->with('error', 'You cannot archive the current semester.');
        }


        $semester->date_archived = Carbon::now('Asia/Manila');
        $semester->save();

        return redirect()->route('semesters')->with('success', 'Semester archived successfully!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to archive semester. Please try again.');
    }
}


    public function search(Request $request)
{
    $search = $request->input('search');

    $semesters = Semester::with('academicYear') 
        ->whereNull('date_archived')
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('start_date', 'like', "%{$search}%")
                  ->orWhere('end_date', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(start_date, ' - ', end_date) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("CONCAT(name, ' ', status) LIKE ?", ["%{$search}%"])

                  ->orWhereHas('academicYear', function ($ay) use ($search) {
                      $ay->where('name', 'like', "%{$search}%");
                  });
            });
        })
        ->orderByRaw("CASE WHEN status = 'current' THEN 1 ELSE 2 END ASC")
        ->paginate(10)
        ->appends(['search' => $search]);

    $academicYears = AcademicYear::whereNull('date_archived')
        ->orderBy('name')
        ->get();

    return view('dashboard.semester', compact('semesters', 'academicYears', 'search'));
}

 public function archivedSearch(Request $request)
{
     $search = $request->input('search');

    $semesters = Semester::with('academicYear') 
        ->whereNotNull('date_archived')
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('start_date', 'like', "%{$search}%")
                  ->orWhere('end_date', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(start_date, ' - ', end_date) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("CONCAT(name, ' ', status) LIKE ?", ["%{$search}%"])

                  ->orWhereHas('academicYear', function ($ay) use ($search) {
                      $ay->where('name', 'like', "%{$search}%");
                  });
            });
        })
        ->orderByRaw("CASE WHEN status = 'current' THEN 1 ELSE 2 END ASC")
        ->paginate(10)
        ->appends(['search' => $search]);

    $academicYears = AcademicYear::whereNull('date_archived')
        ->orderBy('name')
        ->get();

    return view('dashboard.semester', compact('semesters', 'academicYears', 'search'));
}


public function archived()
{
     $semesters = Semester::whereNotNull('date_archived')
                    ->orderByRaw("CASE WHEN status = 'current' THEN 1 ELSE 2 END ASC")
                    ->orderBy('name', 'desc')
                    ->paginate(10);

        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('dashboard.semester', compact('semesters', 'academicYears'));
}

  public function restore($id)
    {
         $semesters = Semester::findOrFail($id);
         $semesters->update(['date_archived' => null]);
         return redirect()->route('semesters.archived')->with('success', 'Semester restored successfully.');
    }
}
