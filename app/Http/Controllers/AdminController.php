<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Http\Resources\AdminResource;
use App\Http\Resources\AssistantTeacherResource;
use App\Http\Resources\TeacherAdminResource;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Teacher;
use App\Models\AssistantTeacher;
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

        $email = $request->input('email');
        $password = $request->input('password');

        // 🔹 أولاً: Admin
        $admin = Admin::where('email', $email)->first();

        if ($admin && Hash::check($password, $admin->password)) {
            $token = $admin->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login success',
                'role'    => 'admin',
                'token'   => $token,
                'data'    => $admin,
            ]);
        }

        // 🔹 ثانياً: Teacher
        $teacher = Teacher::where('email', $email)->first();

        if ($teacher && Hash::check($password, $teacher->password)) {
            $token = $teacher->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login success',
                'role'    => 'teacher',
                'token'   => $token,
                'data'    => $teacher,
            ]);
        }

        // 🔹 ثالثاً: AssistantTeacher
        $assistant = AssistantTeacher::where('email', $email)->first();

        if ($assistant && Hash::check($password, $assistant->password)) {

            $token = $assistant->createToken('auth_token')->plainTextToken;

            $assistantData = $assistant->toArray();
            $assistantData['id'] = $assistant->teacher_id;

            return response()->json([
                'message' => 'Login success',
                'role'    => 'assistant_teacher',
                'token'   => $token,
                'data'    => $assistantData,
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
        $user = $request->user();

        if (!$user) {
            return JsonResponse::respondError('Unauthenticated', 401);
        }

        if ($user instanceof Admin) {
            return JsonResponse::respondSuccess(
                'Admin Fetched Successfully',
                new AdminResource($user)
            );
        }

        if ($user instanceof Teacher) {
            return JsonResponse::respondSuccess(
                'Teacher Fetched Successfully',
                new TeacherAdminResource($user)
            );
        }

        if ($user instanceof AssistantTeacher) {

            $teacher = Teacher::find($user->teacher_id);

            if (!$teacher) {
                return JsonResponse::respondError('Teacher Not Found', 404);
            }

            return JsonResponse::respondSuccess(
                'Teacher Fetched Successfully',
                new TeacherAdminResource($teacher)
            );
        }

        return JsonResponse::respondError('Unknown User Type', 400);

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
