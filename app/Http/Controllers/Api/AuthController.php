<?php

namespace App\Http\Controllers\Api;

use App\Actions\RegisterDeviceAction;
use App\Enums\MessageChannel;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\SendCodeRequest;
use App\Http\Requests\Api\VerifyCodeRequest;
use App\Models\UserDevice;
use App\Models\UserLoginInfo;
use App\Services\Auth\LoginChallengeService;
use App\Services\Messaging\MessageService;
use App\Support\Mask;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Pail\ValueObjects\Origin\Console;
use Throwable;

/**
 * Login has 3 steps:
 *   1. login      -> password + device limit check, returns masked email/phone options
 *   2. sendCode   -> the user picks email or sms, a 6 digit code is sent
 *   3. verify     -> code is checked, the device is saved and the token is returned
 */
class AuthController extends Controller
{
    // ---------------------------------------------------------------- step 1
    public function login(
        LoginRequest $request,
        string $type,
        RegisterDeviceAction $devices,
        LoginChallengeService $challenges
    ): JsonResponse {
        $model = Relation::getMorphedModel($type);
        $field = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        /** @var \App\Models\Admin|\App\Models\Student|\App\Models\Instructor|null $user */
        $user = $model::where($field, $request->login)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            $this->log($request, $type, $user, 'failed', 'wrong_password');

            return response()->json(['message' => 'Invalid credentials.'], 422);
        }

        if (!$user->isActive()) {
            $this->log($request, $type, $user, 'blocked', 'suspended');

            return response()->json(['message' => 'Your account is not active.'], 403);
        }

        // After a 409 the user can send revoke_device_id to replace an old device.
        // It is only removed after the code is verified.
        $revokeId = $request->filled('revoke_device_id')
            ? $user->devices()->active()->whereKey($request->revoke_device_id)->value('id')
            : null;

        try {
            $devices->ensureSlot($user, $request->device_id, $revokeId);
        } catch (HttpResponseException $e) {
            $this->log($request, $type, $user, 'blocked', 'device_limit');
            throw $e;
        }

        $challengeId = $challenges->start(
            $type,
            $user->id,
            $request->only(['device_id', 'device_name', 'platform', 'browser']),
            $revokeId
        );

        return response()->json([
            'challenge_id' => $challengeId,
            'expires_in' => LoginChallengeService::CHALLENGE_TTL,
            'options' => $challenges->options($user),
        ]);
    }

    // ---------------------------------------------------------------- step 2
    public function sendCode(
        SendCodeRequest $request,
        string $type,
        LoginChallengeService $challenges,
        MessageService $messages
    ): JsonResponse {
        $challenge = $challenges->find($request->challenge_id, $type);
        $user = $challenge ? $this->findUser($type, $challenge['user_id']) : null;

        if (!$user || !$user->isActive()) {
            return $this->sessionExpired();
        }

        $wait = $challenges->secondsUntilResend($challenge);

        if ($wait > 0) {
            return response()->json([
                'message' => "Please wait {$wait} seconds before requesting a new code.",
                'retry_after' => $wait,
            ], 429);
        }

        $channel = MessageChannel::from($request->channel);
        $to = match ($channel) {
            MessageChannel::Email => $user->email,
            MessageChannel::Sms => $user->phone,
        };

        if (!$to) {
            return response()->json(['message' => 'This account has no phone number.'], 422);
        }

        $code = $challenges->makeCode();

        try {
            $messages->send(
                $channel,
                $to,
                'login-code',
                ['code' => $code, 'name' => $user->name, 'minutes' => intdiv(LoginChallengeService::CODE_TTL, 60)],
                'Your ' . config('app.name') . ' login code'
            );
        } catch (Throwable $e) {
            report($e);

            return response()->json(['message' => 'Could not send the code. Please try again.'], 502);
        }

        $challenges->storeCode($request->challenge_id, $challenge, $code);

        $response = [
            'message' => 'Code sent.',
            'channel' => $channel->value,
            'sent_to' => $channel === MessageChannel::Email ? Mask::email($to) : Mask::phone($to),
            'expires_in' => LoginChallengeService::CODE_TTL,
            'resend_after' => LoginChallengeService::RESEND_AFTER,
        ];

        if (app()->environment('local') && config('messaging.show_login_code')) {
            $response['debug_code'] = $code;
        }

        return response()->json($response);
    }

    // ---------------------------------------------------------------- step 3
    public function verify(
        VerifyCodeRequest $request,
        string $type,
        LoginChallengeService $challenges,
        RegisterDeviceAction $devices
    ): JsonResponse {
        $challenge = $challenges->find($request->challenge_id, $type);
        $user = $challenge ? $this->findUser($type, $challenge['user_id']) : null;

        if (!$user || !$user->isActive()) {
            return $this->sessionExpired();
        }

        $result = $challenges->verify($request->challenge_id, $challenge, $request->code);

        if ($result !== 'ok') {
            $this->log($request, $type, $user, 'failed', "code_{$result}", $challenge['device']['device_id']);

            [$message, $status] = match ($result) {
                'expired' => ['The code has expired. Request a new one.', 422],
                'no_code' => ['Request a code first.', 422],
                'locked' => ['Too many wrong codes. Please log in again.', 429],
                default => ['The code is not correct.', 422],
            };

            return response()->json(['message' => $message], $status);
        }

        // Replace the old device the user chose after the 409
        if ($challenge['revoke_device_id']) {
            $user->devices()->active()->find($challenge['revoke_device_id'])?->revoke();
        }

        try {
            $device = $devices->register($user, $challenge['device'], $request);
        } catch (HttpResponseException $e) {
            $this->log($request, $type, $user, 'blocked', 'device_limit', $challenge['device']['device_id']);
            throw $e;
        }

        // One token per device
        if ($device->token_id) {
            $user->tokens()->whereKey($device->token_id)->delete();
        }

        $token = $user->createToken("{$type}:{$device->device_id}", [$type]);
        $device->update(['token_id' => $token->accessToken->id]);
        $user->update(['last_login_at' => now()]);

        $challenges->forget($request->challenge_id);
        $this->log($request, $type, $user, 'success', null, $device->device_id);

        return response()->json([
            'token' => $token->plainTextToken,
            'user_type' => $type,
            'user' => $user->only(['public_id', 'name', 'email', 'phone', 'avatar']),
            'device_id' => $device->id,
        ]);
    }

    // ---------------------------------------------------------------- after login
    public function me(Request $request): JsonResponse
    {
        /** @var \App\Models\Admin|\App\Models\Student|\App\Models\Instructor $user */
        $user = $request->user();

        return response()->json([
            'user_type' => $user->userType(),
            'user' => $user->only(['public_id', 'name', 'email', 'phone', 'avatar', 'status', 'last_login_at']),
        ]);
    }

    /**
     * POST logout
     *   no body             -> this device
     *   {"id": 2}           -> that device (id from GET devices)
     *   {"id": "all"}       -> every device
     */
    public function logout(Request $request): JsonResponse
    {
        $request->validate(['id' => ['nullable', 'regex:/^(all|\d+)$/']]);

        /** @var \App\Models\Admin|\App\Models\Student|\App\Models\Instructor $user */
        $user = $request->user();
        $id = $request->input('id');

        if ($id === 'all') {
            $user->devices()->active()->get()->each->revoke();
            $user->tokens()->delete();

            return response()->json(['message' => 'Logged out from all devices.']);
        }

        if ($id !== null) {
            $device = $user->devices()->active()->find($id);
            abort_if(!$device, 404, 'Device not found.');
            $device->revoke();

            return response()->json(['message' => 'Device logged out.']);
        }

        $token = $user->currentAccessToken();
        $device = UserDevice::where('token_id', $token->id)->first();
        $device ? $device->revoke() : $token->delete();

        return response()->json(['message' => 'Logged out.']);
    }

    // ---------------------------------------------------------------- helpers
    /** @return \App\Models\Admin|\App\Models\Student|\App\Models\Instructor|null */
    private function findUser(string $type, int $id)
    {
        $model = Relation::getMorphedModel($type);

        return $model::find($id);
    }

    private function sessionExpired(): JsonResponse
    {
        return response()->json(['message' => 'Login session expired. Please log in again.'], 422);
    }

    private function log(Request $request, string $type, $user, string $status, ?string $reason = null, ?string $deviceId = null): void
    {
        UserLoginInfo::create([
            'user_type' => $type,
            'user_id' => $user?->id,
            'login_identifier' => $request->input('login') ?? $user?->email,
            'status' => $status,
            'failure_reason' => $reason,
            'device_id' => $deviceId ?? $request->input('device_id'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
