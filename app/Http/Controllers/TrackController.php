<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Track;
use Carbon\Carbon;

class TrackController extends Controller
{
    /**
     * Display the list of active tracks and strands
     */
    public function index()
    {
        $tracks = Track::whereNull('date_archived')
                    ->orderBy('track', 'asc')
                    ->orderBy('strand', 'asc')
                    ->paginate(10);

        return view('dashboard.track', compact('tracks'));
    }

    /**
     * Store a new track-strand record
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'track'  => ['required', 'string', 'max:255'],
            'strand' => ['required', 'string', 'max:255']
        ]);

        // Prevent duplicates
        $existing = Track::where('track', $validated['track'])
                        ->where('strand', $validated['strand'])
                        ->whereNull('date_archived')
                        ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'This track and strand combination already exists.');
        }

        try {
            Track::create([
                'track'  => $validated['track'],
                'strand' => $validated['strand'],
                'date_created' => Carbon::now('Asia/Manila'),
            ]);

            return redirect()->route('tracks')->with('success', 'Track and strand added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add record. Please try again.');
        }
    }

    /**
     * Update an existing track-strand record
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'track'  => ['required', 'string', 'max:255'],
            'strand' => ['required', 'string', 'max:255']
        ]);

        $existing = Track::where('track', $validated['track'])
                        ->where('strand', $validated['strand'])
                        ->where('track_strand_id', '!=', $id)
                        ->whereNull('date_archived')
                        ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'This track and strand combination already exists.');
        }

        try {
            $track = Track::findOrFail($id);
            $track->update($validated);

            return redirect()->route('tracks')->with('success', 'Track and strand updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update record. Please try again.');
        }
    }

   
    public function destroy($id)
    {
        try {
            $track = Track::findOrFail($id);
            $track->date_archived = Carbon::now('Asia/Manila');
            $track->save();

            return redirect()->route('tracks')->with('success', 'Track and strand archived successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to archive record. Please try again.');
        }
    }


    public function search(Request $request)
{
    $search = $request->input('search');

    $tracks = Track::whereNull('date_archived')
        ->when($search, function($query, $search) {
            $query->where(function($q) use ($search) {
                $q->where('track', 'like', "%{$search}%")
                  ->orWhere('strand', 'like', "%{$search}%");
            });
        })
        ->orderBy('track', 'asc')
        ->orderBy('strand', 'asc')
        ->paginate(10)
        ->appends(['search' => $search]);

    return view('dashboard.track', compact('tracks'));
}


    public function archivedSearch(Request $request)
{
    $search = $request->input('search');

    $tracks = Track::whereNotNull('date_archived')
        ->when($search, function($query, $search) {
            $query->where(function($q) use ($search) {
                $q->where('track', 'like', "%{$search}%")
                  ->orWhere('strand', 'like', "%{$search}%");
            });
        })
        ->orderBy('track', 'asc')
        ->orderBy('strand', 'asc')
        ->paginate(10)
        ->appends(['search' => $search]);

    return view('dashboard.track', compact('tracks'));
}


    public function archived()
    {
         $tracks = Track::whereNotNull('date_archived')
                    ->orderBy('track', 'asc')
                    ->orderBy('strand', 'asc')
                    ->paginate(10);

        return view('dashboard.track', compact('tracks'));
    }

  public function restore($id)
{
    $track = Track::findOrFail($id);

    // Check if same combination already exists as active
    $existing = Track::where('track', $track->track)
                    ->where('strand', $track->strand)
                    ->whereNull('date_archived')
                    ->first();

    if ($existing) {
        return redirect()->back()->with('error', 'Cannot restore: active record with the same track and strand already exists.');
    }

    $track->update(['date_archived' => null]);
    return redirect()->route('tracks.archived')->with('success', 'Track & Strand restored successfully.');
}

}
