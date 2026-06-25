<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Mail\SendPasswordMail;
use App\Models\User;
use Carbon\Carbon;
class AdviserController extends Controller
{
    
    public function index()
    {
        $advisers = User::whereNull('date_archived')
                            ->where('role', 'adviser')
                            ->orderBy('first_name', 'asc')
                            ->paginate(10);
        
        return view('dashboard.adviser', compact('advisers'));
    }

    public function store(Request $request)
    {
       $validated = $request->validate([
            'first_name'=>['required','string','max:255'],
            'middle_name'=> ['nullable', 'string', 'max:255'],
            'last_name'=> ['string', 'max:255', 'required'],
              "suffix"   => ['nullable', 'string', 'min:2'],
            'phone_number' => ['required', 'regex:/^(09|\+639)\d{9}$/'],
            'email' => ['required', 'email', 'unique:users,email'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048']
        ]);

        $imagePath = null;

         if ($request->hasFile('image')) {
             $imagePath = $request->file('image')->store('AdminImage', 'public');
        }

        $validated['image'] = $imagePath;
        $randomPart= Str::lower(Str::random(6));
        $generatedPassword = "TSHS-" . $randomPart;

        
        try {
        $user = User::create([
           'first_name'=> $validated['first_name'],
            'middle_name'=> $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
             'suffix' => $validated['suffix'] ?? null,
            'phone_number'=> $validated['phone_number'],
            'role' => 'adviser',
            'email' => $validated['email'],
            'image' => $validated['image'],
            'password' => bcrypt($generatedPassword)
        ]);

        
         $fullName = $user->first_name
                    . ($user->middle_name ? ' ' . $user->middle_name : '')
                    . ' ' . $user->last_name;

        Mail::to($user->email)->send(
            new SendPasswordMail($fullName, $user->email, $generatedPassword)
        );

            return redirect()->route('advisers')->with('success', 'Adviser created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Faile to create adviser. Please try again.');
        }
    }

    public function update(Request $request, $id)
    {
        try{
        $validated = $request->validate([
            'first_name'=>['required','string','max:255'],
            'middle_name'=> ['nullable', 'string', 'max:255'],
            'last_name'=> ['string', 'max:255', 'required'],
             "suffix"   => ['nullable', 'string', 'min:2'],
            'phone_number' => ['required', 'regex:/^(09|\+639)\d{9}$/'],
           'email' => ['required', 'email', 'unique:users,email,' . $id . ',user_id'],
            'password' => ['nullable', 'string', 'min:8'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048']
        ]);

        $user = User::findOrFail($id);

          if ($request->hasFile('image')) {
            // Burahin ang dating image kung meron
            if ($user->image && \Storage::disk('public')->exists($user->image)) {
                \Storage::disk('public')->delete($user->image);
            }

            // I-save ang bagong image sa 'public/StudentImage'
            $validated['image'] = $request->file('image')->store('AdminImage', 'public');
            } else {
                // Huwag palitan ang image kung walang bagong upload
                $validated['image'] = $user->image;
            }

            if (!empty($validated['password'])) {
                $validated['password'] = bcrypt($validated['password']);
            } else {
                unset($validated['password']);
            }

            $emailChanged = $validated['email'] !== $user->email;
            
            $user->update($validated);

            if ($emailChanged) {
                $randomPart = Str::lower(Str::random(6));
                $newPassword = "TSHS-" . $randomPart;

                $user->update([
                    'password' => bcrypt($newPassword)
                ]);

                $fullName = $user->first_name . ($user->middle_name ? ' ' . $user->middle_name : '') . ' ' . $user->last_name;

                Mail::to($user->email)->send(
                    new SendPasswordMail($fullName, $user->email, $newPassword)
                );
            }

            return redirect()->route('advisers')->with( 'success', $emailChanged ? 'Adviser updated successfully! New password sent to new email.' : 'Adviser updated successfully!'
            );


                } catch (\Exception $e) {
                    return redirect()->back()->with('error', 'Error: ' . $e->getMessage());        }
                
            }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->date_archived = Carbon::now('Asia/Manila');
            $user->save();

            return redirect()->route('advisers')->with('success', 'Adviser archived successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to archived adviser');
        }
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        $advisers = User::where('role', 'adviser')
            ->whereNull('date_archived')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', middle_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]); // kapag walang middle name
                });
            })
            ->orderBy('first_name', 'asc')
            ->paginate(10)
            ->appends(['search' => $search]);

        return view('dashboard.adviser', compact('advisers'));
    }


    public function archivedSearch(Request $request)
    {
        $search = $request->input('search');

        $advisers = User::where('role', 'adviser')
            ->whereNotNull('date_archived')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    // ito yung full name check
                    ->orWhereRaw("CONCAT(first_name, ' ', middle_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]); // kapag walang middle name
                });
            })
            ->orderBy('first_name', 'asc')
            ->paginate(10)
            ->appends(['search' => $search]);

        return view('dashboard.adviser', compact('advisers'));
    }


  public function archived()
{
    $advisers = User::whereNotNull('date_archived')
                ->where('role', 'adviser')
                ->orderBy('first_name', 'asc')
                ->paginate(10);

    return view('dashboard.adviser', compact('advisers'));
}

public function restore($id)
{
    $adviser = User::findOrFail($id);

    $existing = User::where('role', 'adviser')
                    ->where('email', $adviser->email)
                    ->whereNull('date_archived')
                    ->first();

    if ($existing) {
        return redirect()->back()->with('error', 'Cannot restore: an active adviser with this email already exists.');
    }

    $adviser->update(['date_archived' => null]);

    return redirect()->route('advisers.archived')->with('success', 'Adviser restored successfully.');
}


 
    


}
