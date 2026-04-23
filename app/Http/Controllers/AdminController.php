<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Teacher;
use Exception;

class AdminController extends Controller
{

    public function index()
    {

    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 🔹 أولاً: Admin
        $admin = Admin::where('email', $request->email)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {
            $token = $admin->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login success',
                'role'    => 'admin',
                'token'   => $token,
                'data'    => $admin,
            ]);
        }

        // 🔹 ثانياً: Teacher
        $teacher = Teacher::where('email', $request->email)->first();

        if ($teacher && Hash::check($request->password, $teacher->password)) {
            $token = $teacher->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login success',
                'role'    => 'teacher',
                'token'   => $token,
                'data'    => $teacher,
            ]);
        }

        // ❌ لو الاتنين غلط
        return response()->json([
            'message' => 'الايميل او كلمة المرور غير صحيحة'
        ], 401);
    }

    public function checkAuth(Request $request)
    {
        try {
            $admin = $request->user(); // Sanctum يرجّع الـ authenticated model

            if (!$admin) {
                return JsonResponse::respondError('Unauthenticated', 401);
            }

            return JsonResponse::respondSuccess(
                'Admin Fetched Successfully',
                new AdminResource($admin)
            );

        } catch (Exception $e) {
            return JsonResponse::respondError($e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح'
        ]);
    }
}
