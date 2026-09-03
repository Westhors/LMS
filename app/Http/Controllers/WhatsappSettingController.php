<?php

namespace App\Http\Controllers;

use App\Models\WhatsappInstance;
use App\Models\WhatsappSetting;
use Illuminate\Http\Request;

class WhatsappSettingController extends Controller
{
    public function settingWhatsapp(Request $request)
    {
        try {

            $request->validate([
                'recipient_type' => [
                    'required',
                    'in:student,parent,both',
                ],
            ]);

            // المدرس اللي عامل Login
            $teacherId = auth()->id();

            // ندور على Setting الامتحان الخاصة بالمدرس
            $setting = WhatsappSetting::where('teacher_id', $teacherId)
                ->where('type', 'exam')
                ->first();

            if ($setting) {

                // موجودة → Update
                $setting->update([
                    'recipient_type' => $request->recipient_type,
                ]);

                $message = 'WhatsApp setting updated successfully.';
            } else {

                // مش موجودة → Create
                $setting = WhatsappSetting::create([
                    'teacher_id' => $teacherId,
                    'recipient_type' => $request->recipient_type,
                    'type' => 'exam',
                ]);

                $message = 'WhatsApp setting created successfully.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'id' => $setting->id,
                    'teacher_id' => $setting->teacher_id,
                    'recipient_type' => $setting->recipient_type,
                    'type' => $setting->type,
                ],
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {

            throw $e;
        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteWhatsappInstance($teacherId)
    {
        $instance = WhatsappInstance::where('teacher_id', $teacherId)->first();

        if (!$instance) {
            return response()->json([
                'status' => false,
                'message' => 'WhatsApp instance not found',
            ], 404);
        }

        $instance->delete();

        return response()->json([
            'status' => true,
            'message' => 'WhatsApp instance deleted successfully',
        ]);
    }
}
