<?php

namespace App\Http\Controllers;

use App\Models\WhatsappSetting;
use Illuminate\Http\Request;

class WhatsappSettingController extends Controller
{
    public function settingWhatsapp(Request $request)
    {
        $request->validate([
            'teacherId' => ['required', 'integer', 'exists:users,id'],

            'recipientType' => [
                'required',
                'in:student,parent,both'
            ],

            'type' => [
                'nullable',
                'string',
            ],
        ]);

        $setting = WhatsappSetting::where('teacher_id', $request->teacherId)
            ->where('type', $request->type)
            ->firstOrFail();

        $setting->update([
            'recipient_type' => $request->recipientType,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'WhatsApp setting updated successfully.',
            'data' => [
                'id' => $setting->id,
                'teacherId' => $setting->teacher_id,
                'recipientType' => $setting->recipient_type,
                'type' => $setting->type,
            ],
        ]);
    }
}
