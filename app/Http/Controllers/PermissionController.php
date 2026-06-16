<?php

namespace App\Http\Controllers;

use App\Models\AssistantTeacherPermission;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    public function allPermission()
    {
        $permissions = DB::table('permissions')->get();
        return response()->json($permissions);
    }
    public function assignPermission(Request $request)
    {
        $request->validate([
            'assistant_teacher_id' => 'required|exists:assistant_teachers,id',
            'permission_id' => 'required|exists:permissions,id',
            'view' => 'nullable|boolean',
            'create' => 'nullable|boolean',
            'update' => 'nullable|boolean',
            'delete' => 'nullable|boolean',
        ]);

        AssistantTeacherPermission::updateOrCreate(
            [
                'assistant_teacher_id' => $request->assistant_teacher_id,
                'permission_id' => $request->permission_id,
            ],
            [
                'view' => $request->view ?? false,
                'create' => $request->create ?? false,
                'update' => $request->update ?? false,
                'delete' => $request->delete ?? false,
            ]
        );

        return response()->json([
            'message' => 'Permission assigned successfully'
        ]);
    }
}
