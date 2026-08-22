<?php

namespace App\Http\Controllers;

use App\Helpers\JsonResponse;
use App\Models\WhatsappInstance;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class WhatsappController extends BaseController
{
    private function createWhatsappInstance($teacherId, $gatewayToken)
    {
        $response = Http::timeout(30)->get(
            'https://wzila.com/whatsapp/api/get_instance_id_key.php',
            [
                'access_token' => $gatewayToken,
            ]
        );


        if (!$response->successful()) {

            throw new \Exception(
                'Failed to create WhatsApp instance: ' .
                    $response->body()
            );
        }


        $data = $response->json();


        if (($data['status'] ?? null) !== 'success') {

            throw new \Exception(
                $data['message'] ??
                    'Failed to create WhatsApp instance'
            );
        }


        $instanceId = $data['instance_id'] ?? null;


        if (!$instanceId) {

            throw new \Exception(
                'Instance ID was not returned'
            );
        }


        return WhatsappInstance::create([
            'teacher_id' => $teacherId,

            'instance_id' => $instanceId,

            /*
        |--------------------------------------------------------------------------
        | WZILA create endpoint returns no instance token
        |--------------------------------------------------------------------------
        */

            'access_token' => $gatewayToken,

            'status' => 'pending',

            'phone' => null,
        ]);
    }
    public function connect()
    {
        try {

            $teacher = auth()->user();

            $gatewayToken = config('services.wzila.gateway_token');

            /*
        |--------------------------------------------------------------------------
        | 1. Get existing instance
        |--------------------------------------------------------------------------
        */

            $instance = WhatsappInstance::where(
                'teacher_id',
                $teacher->id
            )->first();


            /*
        |--------------------------------------------------------------------------
        | 2. Create new instance
        |--------------------------------------------------------------------------
        */

            if (!$instance) {

                $instance = $this->createWhatsappInstance(
                    $teacher->id,
                    $gatewayToken
                );
            }


            /*
        |--------------------------------------------------------------------------
        | 3. Get QR Code
        |--------------------------------------------------------------------------
        */

            $qrResponse = Http::timeout(30)->get(
                'https://apis.wzila.com/get_qrcode',
                [
                    'access_token' => $instance->access_token,
                    'instance_id' => $instance->instance_id,
                ]
            );


            $qrData = $qrResponse->json();


            /*
        |--------------------------------------------------------------------------
        | 4. Instance invalidated
        |--------------------------------------------------------------------------
        */

            if (
                ($qrData['status'] ?? null) === 'error' &&
                str_contains(
                    strtolower($qrData['message'] ?? ''),
                    'invalidated'
                )
            ) {

                /*
            Delete old instance
            */

                $instance->delete();


                /*
            Create new instance
            */

                $instance = $this->createWhatsappInstance(
                    $teacher->id,
                    $gatewayToken
                );


                /*
            Get QR again
            */

                $qrResponse = Http::timeout(30)->get(
                    'https://apis.wzila.com/get_qrcode',
                    [
                        'access_token' => $instance->access_token,
                        'instance_id' => $instance->instance_id,
                    ]
                );


                $qrData = $qrResponse->json();
            }


            /*
        |--------------------------------------------------------------------------
        | 5. Check QR response
        |--------------------------------------------------------------------------
        */

            if (!$qrResponse->successful()) {

                return response()->json([
                    'status' => false,
                    'message' => 'Failed to get QR code',
                    'response' => $qrResponse->body(),
                ], 500);
            }


            if (($qrData['status'] ?? null) !== 'success') {

                return response()->json([
                    'status' => false,
                    'message' =>
                    $qrData['message'] ??
                        'Failed to get QR code',

                    'response' => $qrData,
                ], 500);
            }


            /*
        |--------------------------------------------------------------------------
        | 6. Return QR
        |--------------------------------------------------------------------------
        */

            return response()->json([
                'status' => true,

                'message' =>
                'WhatsApp QR generated successfully',

                'instance_id' =>
                $instance->instance_id,

                'qr_code' =>
                $qrData['base64'] ?? null,

                'expires_in' =>
                $qrData['expires_in'] ?? 300,

                'status' =>
                $instance->status,
            ]);
        } catch (\Throwable $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function sendMessage(Request $request)
    {
        try {
            $teacher = auth()->user();

            $request->validate([
                'phone' => ['required', 'string'],
                'message' => ['required', 'string'],
            ]);

            /*
        |--------------------------------------------------------------------------
        | Get Teacher WhatsApp Instance
        |--------------------------------------------------------------------------
        */

            $instance = WhatsappInstance::where(
                'teacher_id',
                $teacher->id
            )->first();

            if (!$instance) {
                return response()->json([
                    'status' => false,
                    'message' => 'WhatsApp instance not found',
                ], 404);
            }

            /*
        |--------------------------------------------------------------------------
        | Format Phone
        |--------------------------------------------------------------------------
        */

            $phone = preg_replace('/\D/', '', $request->phone);

            $chatId = $phone . '@s.whatsapp.net';

            /*
        |--------------------------------------------------------------------------
        | Instance Data
        |--------------------------------------------------------------------------
        */

            $accessToken = $instance->access_token;
            $instanceId  = $instance->instance_id;

            /*
        |--------------------------------------------------------------------------
        | Build URL
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | Wzila expects all parameters in the URL.
        | POST body must remain empty.
        |
        */

            $query = http_build_query([
                'access_token' => $accessToken,
                'instance_id'  => $instanceId,
                'chat_id'      => $chatId,
                'text'         => $request->message,
            ]);

            $url = 'https://apis.wzila.com/send-link?' . $query;

            /*
        |--------------------------------------------------------------------------
        | Send exactly like Wzila Tester
        |--------------------------------------------------------------------------
        */

            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,

                CURLOPT_RETURNTRANSFER => true,

                CURLOPT_POST => true,

                // Empty body exactly like Wzila Tester
                CURLOPT_POSTFIELDS => '',

                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/x-www-form-urlencoded',
                    'Accept: application/json',
                ],

                CURLOPT_TIMEOUT => 30,
            ]);

            $result = curl_exec($ch);

            $curlError = curl_error($ch);

            $httpCode = curl_getinfo(
                $ch,
                CURLINFO_HTTP_CODE
            );

            curl_close($ch);

            /*
        |--------------------------------------------------------------------------
        | cURL Error
        |--------------------------------------------------------------------------
        */

            if ($curlError) {
                return response()->json([
                    'status' => false,
                    'message' => $curlError,
                ], 500);
            }

            /*
        |--------------------------------------------------------------------------
        | Decode Wzila Response
        |--------------------------------------------------------------------------
        */

            $data = json_decode($result, true);

            /*
        |--------------------------------------------------------------------------
        | Wzila Error
        |--------------------------------------------------------------------------
        */

            if (
                $httpCode !== 200 ||
                ($data['status'] ?? null) !== 'success'
            ) {
                return response()->json([
                    'status' => false,

                    'message' =>
                    $data['message'] ??
                        'Wzila error',

                    'response' => $data,

                    'debug' => [
                        'instance_id' => $instanceId,
                        'chat_id' => $chatId,
                        'phone' => $phone,
                        'http_status' => $httpCode,
                    ],
                ], 422);
            }

            /*
        |--------------------------------------------------------------------------
        | Success
        |--------------------------------------------------------------------------
        */

            return response()->json([
                'status' => true,

                'message' =>
                'WhatsApp message sent successfully',

                'response' => $data,

                'data' => [
                    'instance_id' => $instanceId,
                    'chat_id' => $chatId,
                    'phone' => $phone,
                ],
            ]);
        } catch (\Throwable $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
