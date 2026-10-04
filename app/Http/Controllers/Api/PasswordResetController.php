<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\{PasswordResetCompleteRequest, PasswordResetSendCodeRequest, PasswordResetStartRequest, PasswordResetVerifyRequest};
use App\Models\PasswordReset;
use App\Services\Auth\PasswordResetService;
use App\Services\Messaging\MessageService;
use Illuminate\Http\JsonResponse;
use Throwable;

class PasswordResetController extends Controller
{
    public function start(PasswordResetStartRequest $request, string $type, PasswordResetService $resets): JsonResponse
    {
        $reset = $resets->start($type, $request);

        return response()->json([
            'message' => 'If the account exists, select a channel to receive a verification code.',
            'challenge_id' => $reset->challenge_id,
            'expires_in' => $resets->setting('challenge_ttl'),
            'options' => $resets->options($reset),
        ]);
    }

    public function sendCode(
        PasswordResetSendCodeRequest $request,
        string $type,
        PasswordResetService $resets,
        MessageService $messages,
    ): JsonResponse {
        $reset = PasswordReset::where('challenge_id', $request->challenge_id)->where('user_type', $type)->first();
        if (!$reset) {
            return response()->json(['message' => 'Password reset session expired. Start again.'], 422);
        }

        try {
            $result = $resets->sendCode($reset, $request->channel, $messages);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json(['message' => 'Could not send the verification code. Try again.'], 502);
        }

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error'], 'retry_after' => $result['retry_after'] ?? null], $result['status']);
        }
        return response()->json($result);
    }

    public function verifyCode(
        PasswordResetVerifyRequest $request,
        string $type,
        PasswordResetService $resets,
    ): JsonResponse {
        $reset = PasswordReset::where('challenge_id', $request->challenge_id)->where('user_type', $type)->first();
        if (!$reset) {
            return response()->json(['message' => 'Password reset session expired. Start again.'], 422);
        }

        $result = $resets->verifyCode($reset, $request->code);
        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        return response()->json($result);
    }

    public function complete(
        PasswordResetCompleteRequest $request,
        string $type,
        PasswordResetService $resets,
    ): JsonResponse {
        $reset = PasswordReset::where('challenge_id', $request->challenge_id)->where('user_type', $type)->first();
        if (!$reset) {
            return response()->json(['message' => 'Password reset session expired. Start again.'], 422);
        }

        $result = $resets->complete($reset, $request->reset_token, $request->password);
        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        return response()->json(['message' => 'Password changed. All previous devices have been logged out.']);
    }
}
