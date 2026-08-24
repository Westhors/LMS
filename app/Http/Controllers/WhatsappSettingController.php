<?php

namespace App\Http\Controllers;

use App\Models\WhatsappSetting;
use Illuminate\Http\Request;

class WhatsappSettingController extends Controller
{
    public function settingWhatsapp(Request $request)
    {
        try {

            $request->validate([
                'recipientType' => [
                    'required',
                    'in:student,parent,both',
                ],
            ]);

            // المدرس اللي عامل Login
            $teacherId = auth()->id();

            // نوع الـ Setting ثابت: exam
            $setting = WhatsappSetting::where('teacher_id', $teacherId)
                ->where('type', 'exam')
                ->first();

            if ($setting) {

                // موجودة → Update
                $setting->update([
                    'recipient_type' => $request->recipientType,
                ]);

                $message = 'WhatsApp setting updated successfully.';
            } else {

                // مش موجودة → Create
                $setting = WhatsappSetting::create([
                    'teacher_id' => $teacherId,
                    'recipient_type' => $request->recipientType,
                    'type' => 'exam',
                ]);

                $message = 'WhatsApp setting created successfully.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'id' => $setting->id,
                    'teacherId' => $setting->teacher_id,
                    'recipientType' => $setting->recipient_type,
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
}
