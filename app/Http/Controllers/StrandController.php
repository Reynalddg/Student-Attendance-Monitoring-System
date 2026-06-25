<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Strand;
use App\Models\Track;
use Carbon\Carbon;

class StrandController extends Controller
{
    public function index(){
        $strands = Strand::whereNull('date_archived')
                    ->orderBy('name', 'asc')
                    ->paginate(10);

        $tracks = Track::orderBy('name')->get();
  
                    
        return view('dashboard.strand', compact('strands', 'tracks'));
    }

 public function store(Request $request){
    $validated = $request->validate([
        'name' => ['required', 'string', 'min:8', 'max:255'],
        'track_id' => ['required']
    ]);

    // ✅ Check if strand with same name and track already exists (not archived)
    $existing = Strand::where('name', $validated['name'])
                      ->where('track_id', $validated['track_id'])
                      ->whereNull('date_archived')
                      ->first();

    if ($existing) {
        return redirect()->back()->with('error', 'This strand already exists for the selected track.');
    }

    try {
        Strand::create([
            'name' => $validated['name'],
            'track_id' => $validated['track_id']
        ]);

        return redirect()->route('strands')->with('success', 'Strand created Successfully!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to create strand. Please try again later.');
    }
}

public function update(Request $request, $id){
    $validated = $request->validate([
        'name' => ['required', 'string', 'min:8', 'max:255'],
        'track_id' => ['required']
    ]);

    // ✅ Check for duplicate strand (exclude current) with same track
    $existing = Strand::where('name', $validated['name'])
                      ->where('track_id', $validated['track_id'])
                      ->where('strand_id', '!=', $id)
                      ->whereNull('date_archived')
                      ->first();

    if ($existing) {
        return redirect()->back()->with('error', 'Another strand with this name already exists for the selected track.');
    }

    try {
        $strand = Strand::findOrFail($id);
        $strand->update($validated);

        return redirect()->route('strands')->with('success', 'Strand updated Successfully!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to update strand. Please try again later.');
    }
}


    public function destroy($id)
    {
        try {
            $strand = Strand::findOrFail($id);
            $strand->date_archived = Carbon::now('Asia/Manila');
            $strand->save();

            return redirect()->route('strands')->with('success', 'Strand archived Successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to archived strand. Please try again');
        }
    }

   public function search(Request $request)
    {
        $search = $request->input('search');

        $strands = Strand::with('track') // kung may relation sa track
                    ->whereNull('date_archived')
                    ->when($search, function($query, $search){
                        $query->where('name', 'like', "%{$search}%");
                    })
                    ->orderBy('name', 'asc')
                    ->paginate(10)
                    ->appends(['search' => $search]);

        $tracks = Track::orderBy('name')->get();

        return view('dashboard.strand', compact('strands', 'tracks'));
    }
}
