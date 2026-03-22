<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SmsController extends Controller
{
    /**
     * Send SMS via external API after credit check
     */
    public function sendByDlt(Request $request)
    {
        $user = $request->user;
        // Step 1: Check if user has enough credits
        if ($user->credits < 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Insufficient balance. Minimum 1 credit required.'
            ], 403);
        }

       $validator = Validator::make($request->all(), [
            'mobile' => 'required|numeric',
            'message' => 'required|string',
            'tempid' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $smsUrl = 'http://sms.sbgenus.com/submitsms.jsp';
        $params = [
            'user' => 'SBGENUS',
            'key' => '91ccebbe47XX',
            'mobile' => $request->mobile,
            'message' => $request->message,
            'senderid' => 'SBGENS',
            'accusage' => '1',
            'entityid' => '1501834600000058419',
            'tempid' => $request->tempid,
        ];

        try {
            $response = Http::timeout(10)->get($smsUrl, $params);

            if ($response->successful()) {
                $user->decrement('credits'); // safer way to reduce credit

                return response()->json([
                    'status' => 'success',
                    'message' => 'SMS sent successfully',
                    'sms_response' => $response->body()
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to send SMS',
                    'sms_response' => $response->body()
                ], 500);
            }
        } catch (\Illuminate\Http\Client\RequestException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request to SMS server failed',
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
