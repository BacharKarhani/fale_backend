<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\SponsorshipRegistered;
use App\Mail\CompanyRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Role;
use Illuminate\Validation\ValidationException;
use Exception;

class AuthController extends Controller
{
    public function getRoles()
    {
        try {
            $roles = Role::whereIn('name', ['sponsorship', 'registered_company', 'Company', 'Sponsorship'])
                ->get()
                ->map(function ($role) {
                    $role->display_name = $role->name === 'company' ? 'Company' : ucfirst($role->name);
                    return $role;
                });

            return response()->json(['roles' => $roles], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Could not fetch roles', 'error' => $e->getMessage()], 500);
        }
    }

    public function registerCompany(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'nullable|string|max:20',
                'company_name' => 'required|string|max:255',
                'specialization' => 'nullable|array',
                'specialization.*' => 'string|max:255',
                'code' => 'nullable|string|max:50',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('users', 'public');
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'company_name' => $request->company_name,
                'specialization' => json_encode($request->specialization),
                'code' => $request->code,
                'role_id' => 4, // company
                'status' => false,
                'photo' => $photoPath,
            ]);

            // ✅ أرسل إيميل إشعار لتسجيل شركة جديدة إلى الإيميل المحدّد في .env
            try {
                $toEmail = config('mail.contact_to', env('CONTACT_TO_EMAIL', 'info@lafeleb.com'));
                Mail::to($toEmail)->send(new CompanyRegistered($user));
            } catch (Exception $mailEx) {
                // ما نوقف التسجيل إذا الإيميل فشل، بس منسجّل اللوج
                logger()->error('Company registration mail failed: '.$mailEx->getMessage());
            }

            return response()->json(['message' => 'Company registered successfully. Pending approval.'], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }

    public function registerSponsorship(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'nullable|string|max:20',
                'company_name' => 'required|string|max:255',
                'specialization' => 'nullable|array',
                'specialization.*' => 'string|max:255',
                'code' => 'nullable|string|max:50',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('users', 'public');
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'company_name' => $request->company_name,
                'specialization' => json_encode($request->specialization),
                'code' => $request->code,
                'role_id' => 3, // sponsorship
                'status' => false,
                'photo' => $photoPath,
            ]);

            // ✅ إشعار تسجيل رعايات (موجود أصلاً)
            try {
                $toEmail = config('mail.contact_to', env('CONTACT_TO_EMAIL', 'info@lafeleb.com'));
                Mail::to($toEmail)->send(new SponsorshipRegistered($user));
            } catch (Exception $mailEx) {
                logger()->error('Sponsorship registration mail failed: '.$mailEx->getMessage());
            }

            return response()->json(['message' => 'Sponsorship registered successfully. Pending approval.'], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            $user = $request->user();

            if (!$user->status) {
                Auth::logout();
                return response()->json(['message' => 'Your account is pending approval. Please contact admin.'], 403);
            }

            $user->load('role');
            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user
            ], 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation error', 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['message' => 'Server error', 'error' => $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            return response()->json(['message' => 'Logged out successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Server error during logout', 'error' => $e->getMessage()], 500);
        }
    }

    public function user(Request $request)
    {
        try {
            $user = $request->user()->load('role');

            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->name ?? null,
            ], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Could not fetch user details', 'error' => $e->getMessage()], 500);
        }
    }

    public function getCompanyUsers(Request $request)
    {
        try {
            if (!$request->user()->isAdmin()) {
                return response()->json(['message' => 'Unauthorized. Admin access required.'], 403);
            }

            $users = User::with('role')->where('role_id', 4)->get();

            return response()->json([
                'message' => 'Company users retrieved successfully',
                'users' => $users,
                'total' => $users->count()
            ], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Could not fetch company users', 'error' => $e->getMessage()], 500);
        }
    }

    public function getSponsorshipUsers(Request $request)
    {
        try {
            if (!$request->user()->isAdmin()) {
                return response()->json(['message' => 'Unauthorized. Admin access required.'], 403);
            }

            $users = User::with('role')->where('role_id', 3)->get();

            return response()->json([
                'message' => 'Sponsorship users retrieved successfully',
                'users' => $users,
                'total' => $users->count()
            ], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Could not fetch sponsorship users', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateUserStatus(Request $request, $userId)
    {
        try {
            if (!$request->user()->isAdmin()) {
                return response()->json(['message' => 'Unauthorized. Admin access required.'], 403);
            }

            $request->validate([
                'status' => 'required|boolean'
            ]);

            $user = User::with('role')->findOrFail($userId);

            if (!in_array($user->role_id, [3, 4])) {
                return response()->json(['message' => 'Can only update status for Company or Sponsorship users'], 400);
            }

            $user->status = $request->status;
            $user->save();

            return response()->json([
                'message' => 'User status updated successfully',
                'user' => $user
            ], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Could not update user status', 'error' => $e->getMessage()], 500);
        }
    }
}
