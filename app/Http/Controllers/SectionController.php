<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\User;
use App\Models\Track;
use Carbon\Carbon;
use App\Models\Adviser;

class SectionController extends Controller
{
    public function index()
    {
        $sections = Section::with(['adviser', 'track_strand'])
                    ->whereNull('date_Archived')
                    ->orderBy('grade_level', 'asc')
                    ->orderBy('section_name', 'asc')
                    ->paginate(10);

        $advisers = User::selectRaw("user_id, CONCAT_WS(' ', first_name, middle_name, last_name) as full_name")
            ->whereNull('date_Archived')
             ->where('role', 'adviser') 
            ->orderBy('full_name')
            ->get();
        $track_strands = Track::orderBy('track')
                    ->whereNull('date_Archived')
                    ->get();
        return view('dashboard.section', compact('sections', 'advisers', 'track_strands'));    
    }

   public function store(Request $request)
    {
        $validated = $request->validate([
            'grade_level' => ['required', 'string', 'max:255'],
            'section_name' => ['required', 'string', 'max:255'],
            'adviser_id' => ['required'],
            'track_strand_id' => ['required']
        ]);

        $existingSection = Section::where('user_id', $validated['adviser_id'])
            ->whereNull('date_archived')
            ->first();

        if ($existingSection) {
            return redirect()->back()->with('error', 'This adviser is already assigned to another section.');
        }

        try {
            Section::create([
                'grade_level' => $validated['grade_level'],
                'section_name' => $validated['section_name'],
                'user_id' => $validated['adviser_id'],
                'track_strand_id' => $validated['track_strand_id']
            ]);

            return redirect()->route('sections')->with('success', 'Section created successfully!');
        } catch (\Exception $e) {
 return redirect()->back()->with('error', $e->getMessage());        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'grade_level' => ['required', 'string', 'max:255'],
            'section_name' => ['required', 'string', 'max:255'],
            'adviser_id' => ['required'],
            'track_strand_id' => ['required']
        ]);

        $existingSection = Section::where('user_id', $validated['adviser_id'])
            ->where('section_id', '!=', $id)
            ->whereNull('date_archived')
            ->first();

        if ($existingSection) {
            return redirect()->back()->with('error', 'This adviser is already assigned to another section.');
        }

        try {
            $section = Section::findOrFail($id);
            $section->update([
                'grade_level' => $validated['grade_level'],
                'section_name' => $validated['section_name'],
                'user_id' => $validated['adviser_id'],
                'track_strand_id' => $validated['track_strand_id']
            ]);

            return redirect()->route('sections')->with('success', 'Section updated successfully!');
        } catch (\Exception $e) {
 return redirect()->back()->with('error', $e->getMessage());        
        }
    }

    public function destroy($id)
    {
        try {
            $section = Section::findOrFail($id);
            $section->date_archived = Carbon::now('Asia/Manila');
            $section->save();

            return redirect()->route('sections')->with('success', 'Section archived successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to archived section. Please try again');
        }
    }

   public function search(Request $request)
{
    $search = $request->input('search');

    $sections = Section::with(['adviser', 'track_strand'])
        ->whereNull('date_archived')
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('section_name', 'like', "%{$search}%")
                  ->orWhere('grade_level', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(grade_level, ' ', section_name) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("CONCAT(section_name, ' ', grade_level) LIKE ?", ["%{$search}%"])
                  // Search adviser full name
                  ->orWhereHas('adviser', function ($a) use ($search) {
                      $a->whereRaw("CONCAT_WS(' ', first_name, middle_name, last_name) LIKE ?", ["%{$search}%"]);
                  })
                  // Search track + strand
                  ->orWhereHas('track_strand', function ($b) use ($search) {
                      $b->whereRaw("CONCAT_WS(' ', track, strand) LIKE ?", ["%{$search}%"]);
                  });
            });
        })
        ->orderBy('grade_level')
        ->orderBy('section_name')
        ->paginate(10)
        ->appends(['search' => $search]);

    $advisers = Adviser::selectRaw("*, CONCAT_WS(' ', first_name, middle_name, last_name) as full_name")
        ->orderBy('full_name')
        ->get();

    $track_strands = Track::orderBy('track')->get();

    return view('dashboard.section', compact('sections', 'advisers', 'track_strands'));
}

public function archivedSearch(Request $request)
{
     $search = $request->input('search');

    $sections = Section::with(['adviser', 'track_strand'])
        ->whereNotNull('date_archived')
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('section_name', 'like', "%{$search}%")
                  ->orWhere('grade_level', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(grade_level, ' ', section_name) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("CONCAT(section_name, ' ', grade_level) LIKE ?", ["%{$search}%"])
                  // Search adviser full name
                  ->orWhereHas('adviser', function ($a) use ($search) {
                      $a->whereRaw("CONCAT_WS(' ', first_name, middle_name, last_name) LIKE ?", ["%{$search}%"]);
                  })
                  // Search track + strand
                  ->orWhereHas('track_strand', function ($b) use ($search) {
                      $b->whereRaw("CONCAT_WS(' ', track, strand) LIKE ?", ["%{$search}%"]);
                  });
            });
        })
        ->orderBy('grade_level')
        ->orderBy('section_name')
        ->paginate(10)
        ->appends(['search' => $search]);

    $advisers = Adviser::selectRaw("*, CONCAT_WS(' ', first_name, middle_name, last_name) as full_name")
        ->orderBy('full_name')
        ->get();

    $track_strands = Track::orderBy('track')->get();

    return view('dashboard.section', compact('sections', 'advisers', 'track_strands'));
}


public function archived()
{
     $sections = Section::with(['adviser', 'track_strand'])
                    ->whereNotNull('date_Archived')
                    ->orderBy('grade_level', 'asc')
                    ->orderBy('section_name', 'asc')
                    ->paginate(10);

        $advisers = User::selectRaw("user_id, CONCAT_WS(' ', first_name, middle_name, last_name) as full_name")
             ->where('role', 'adviser') 
            ->orderBy('full_name')
            ->get();
        $track_strands = Track::orderBy('track')->get();
        return view('dashboard.section', compact('sections', 'advisers', 'track_strands')); 
}

public function restore($id)
{
    $section = Section::findOrFail($id);

    // Check if adviser already has an active section
    $existingSection = Section::where('user_id', $section->user_id)
                              ->where('section_id', '!=', $id)
                              ->whereNull('date_archived')
                              ->first();

    if ($existingSection) {
        return redirect()->back()->with('error', 'Cannot restore this section because the adviser is already assigned to another active section.');
    }

    $section->update(['date_archived' => null]);

    return redirect()->route('sections.archived')->with('success', 'Section restored successfully.');
}

}
