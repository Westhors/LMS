<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Resources\AdminResource;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\Course;
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

        $admin = Admin::where('email', $request->email)->first();

        if (! $admin || ! Hash::check($request->password, $admin->password)) {
            return response()->json([
                'message' => 'الايميل او كلمة المرور غير صحيحة'
            ], 401);
        }

        $token = $admin->createToken('admin-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'admin' => $admin
        ]);
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
