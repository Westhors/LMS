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

    public function showPermissions(Request $request)
    {
        $request->validate([
            'assistant_teacher_id' => 'required|exists:assistant_teachers,id',
        ]);

        $permissions = AssistantTeacherPermission::with('permission')
            ->where('assistant_teacher_id', $request->assistant_teacher_id)
            ->get()
            ->map(function ($item) {
                return [
                    'permission_id' => $item->permission_id,
                    'permission_name' => $item->permission->name,
                    'view' => (bool) $item->view,
                    'create' => (bool) $item->create,
                    'update' => (bool) $item->update,
                    'delete' => (bool) $item->delete,
                ];
            });

        return response()->json([
            'assistant_teacher_id' => $request->assistant_teacher_id,
            'permissions' => $permissions
        ]);
    }
    public function assignPermission(Request $request)
    {
        $request->validate([
            'assistant_teacher_id' => 'required|exists:assistant_teachers,id',
            'permissions' => 'required|array',
            'permissions.*.permission_id' => 'required|exists:permissions,id',
            'permissions.*.view' => 'nullable|boolean',
            'permissions.*.create' => 'nullable|boolean',
            'permissions.*.update' => 'nullable|boolean',
            'permissions.*.delete' => 'nullable|boolean',
        ]);

        foreach ($request->permissions as $permission) {

            AssistantTeacherPermission::updateOrCreate(
                [
                    'assistant_teacher_id' => $request->assistant_teacher_id,
                    'permission_id' => $permission['permission_id'],
                ],
                [
                    'view' => $permission['view'] ?? false,
                    'create' => $permission['create'] ?? false,
                    'update' => $permission['update'] ?? false,
                    'delete' => $permission['delete'] ?? false,
                ]
            );
        }

        return response()->json([
            'message' => 'Permissions assigned successfully'
        ]);
    }
}
