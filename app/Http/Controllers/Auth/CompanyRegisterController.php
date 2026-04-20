<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\SystemSetting;
use App\Models\User;
use App\Notifications\CompanyRegistrationSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CompanyRegisterController extends Controller
{
    public function show()
    {
        return view('auth.register-company');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_name' => ['required','string','max:255'],
            'contact_person_name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:6'],
            'industry' => ['nullable','string','max:255'],
            'location' => ['nullable','string','max:255'],
            'website' => ['nullable','string','max:255'],
            'description' => ['nullable','string','max:5000'],
        ]);

        $user = null;

        DB::transaction(function () use ($data, &$user) {

            $user = User::create([
                'name' => $data['company_name'],
                'email' => $data['email'],
                'role' => 'company',
                'academic_id' => null,
                'password' => Hash::make($data['password']),
            ]);

            CompanyProfile::create([
                'user_id' => $user->id,
                'company_name' => $data['company_name'],
                'contact_person_name' => $data['contact_person_name'],
                'industry' => $data['industry'] ?? null,
                'location' => $data['location'] ?? null,
                'website' => $data['website'] ?? null,
                'description' => $data['description'] ?? null,
                'status' => 'pending',
                'approved_at' => null,
                'rejected_at' => null,
                'approved_by' => null,
                'admin_note' => null,
            ]);


            if (SystemSetting::enabled('email_new_user_notifications')) {
                $admins = User::query()
                    ->whereIn('role', ['admin', 'super_admin'])
                    ->get();

                foreach ($admins as $admin) {
                    $admin->notify(new CompanyRegistrationSubmitted(
                        $data['company_name'],
                        $data['email']
                    ));
                }
            }
        });


        Auth::login($user);

        return redirect()->route('company.dashboard');
    }
}
