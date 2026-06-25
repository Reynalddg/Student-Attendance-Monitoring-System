<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Mail\SendPasswordMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Models\User;

class UserController extends Controller
{
   public function index()
{
    $users = User::whereNull('date_archived')
                ->where('role', 'admin')
                ->paginate(7);

    return view('dashboard.users', compact('users'));
}

    public function store(Request $request){
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
            'role' => 'admin',
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

        return redirect()->route('users')->with('success', 'User created successfully!');

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to create user. Please try again.');
    }
    }

    
    public function logout(Request $request){
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/homepage');
    }
    
public function process(Request $request)
{
    $validated = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $email = $validated['email'];
    $cacheKey = 'login_attempts_' . Str::lower($email);
    $lockoutKey = 'login_lockout_' . Str::lower($email);
    $maxAttempts = 5;

    // Check if account is locked
    if (Cache::has($lockoutKey)) {
        $seconds = Cache::get($lockoutKey) - time();
        return redirect()->back()
            ->withInput()
            ->with('error', "Too many failed attempts. Please try again in {$seconds} seconds.");
    }

    $user = \App\Models\User::where('email', $email)
                            ->whereNull('date_archived')
                            ->first();

    if (!$user || !auth()->attempt([
        'email' => $email,
        'password' => $validated['password'],
    ])) {

        // Increment failed attempts
        $attempts = Cache::get($cacheKey, 0) + 1;
        Cache::put($cacheKey, $attempts, now()->addMinutes(15));

        if ($attempts >= 5) {
            Cache::put($lockoutKey, time() + 900, now()->addMinutes(15));
            return redirect()->back()
                ->withInput()
                ->with('error', 'Too many login attempts. Account locked for 15 minutes.');
        }

          $remainingAttempts = $maxAttempts - $attempts;

        return redirect()->back()
            ->withInput()
            ->with('error', "The email or password you entered is incorrect. You have {$remainingAttempts} attempt(s) left.");
    }

    // Reset attempts on successful login
    Cache::forget($cacheKey);
    Cache::forget($lockoutKey);

    $request->session()->regenerate();

    if ($user->role === 'admin') {
        return redirect('/dashboard')->withInput()->with('success', 'Admin login successful!');
    }

    if ($user->role === 'adviser') {
        $section = \App\Models\Section::where('user_id', $user->user_id)->first();
        if (!$section) {
            auth()->logout();
            return redirect()->back()->with('error', 'No section assigned to this adviser.');
        }

        session([
            'adviser_section_id' => $section->section_id,
            'adviser_user_id' => $user->user_id
        ]);

        return redirect()->route('teacher.teacherDashboard')
                         ->with('success', 'Adviser login successful!');
    }

    auth()->logout();
    return redirect('/homepage')->with('error', 'Unauthorized role.');
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
        
 return redirect()->route('users')->with( 'success', $emailChanged ? 'Admin updated successfully! New password sent to new email.' : 'Admin updated successfully!'
        );        }
        catch(\Exception $e){
            return redirect()->back()->with('error', 'Failed to update admin. Please try again');
            
        }

    }


        public function destroy($id)
{
    try {
        $user = User::findOrFail($id);

        if ($user->user_id == 1) {
            return redirect()->back()->with('error', 'Cannot archive the super admin.');
        }

        $user->date_archived = Carbon::now('Asia/Manila');
        $user->save();

        return redirect()->route('users')->with('success', 'Admin archived successfully!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Failed to archive admin. Please try again later.');
    }
}

    
        public function changePassword(Request $request)
        {
            $request->validate([
                'new_password' => 'required|string|min:6|confirmed',
            ]);

            $user = auth()->user();
            $user->password = Hash::make($request->new_password);
            $user->save();

            return redirect()->back()->with('success', 'Password updated successfully!');
        }

       public function search(Request $request)
{
    $search = $request->input('search');

    $users = User::whereNull('date_archived')
                ->where('role', 'admin')
                ->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%$search%")
                      ->orWhere('middle_name', 'like', "%$search%")
                      ->orWhere('last_name', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%")
                      ->orWhereRaw("CONCAT(first_name, ' ', IFNULL(middle_name,''), ' ', last_name) LIKE ?", ["%{$search}%"]);
                })
                ->paginate(7)
                ->withQueryString();

    return view('dashboard.users', compact('users', 'search'));
}

 public function archivedSearch(Request $request)
{
     $search = $request->input('search');

    $users = User::whereNotNull('date_archived')
                ->where('role', 'admin')
                ->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%$search%")
                      ->orWhere('middle_name', 'like', "%$search%")
                      ->orWhere('last_name', 'like', "%$search%")
                      ->orWhere('email', 'like', "%$search%")
                      ->orWhereRaw("CONCAT(first_name, ' ', IFNULL(middle_name,''), ' ', last_name) LIKE ?", ["%{$search}%"]);
                })
                ->paginate(7)
                ->withQueryString();

    return view('dashboard.users', compact('users', 'search'));
}


public function archived()
{
      $users = User::whereNotNull('date_archived')
                ->where('role', 'admin')
                ->paginate(7);

    return view('dashboard.users', compact('users'));
}

public function restore($id)
{
    $user = User::findOrFail($id);

    $existing = User::where('email', $user->email)
                        ->whereNull('date_archived')
                        ->where('user_id', '!=', $id)
                        ->exists();

    if ($existing) {
        return redirect()->back()->with('error', 'Cannot restore admin. The email is already used by another active admin.');
    }

    $user->update(['date_archived' => null]);

    return redirect()->route('users.archived')->with('success', 'Admin restored successfully.');
}


}
