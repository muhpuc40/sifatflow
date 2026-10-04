<?php

namespace App\Services\Auth;

use App\Enums\MessageChannel;
use App\Support\Mask;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

/**
 * Keeps a login or sign-up "in progress" between the first step and the code step.
 * Stored in the cache (no table needed), and it expires by itself.
 */
class LoginChallengeService
{
    public const CHALLENGE_TTL = 600;  // 10 minutes to finish
    public const CODE_TTL = 300;       // a code is valid for 5 minutes
    public const RESEND_AFTER = 60;    // seconds between two codes
    public const MAX_ATTEMPTS = 5;     // wrong codes before it must start again

    /**
     * $userId is null for a sign-up. $signup holds the new account data
     * (name, email, phone, hashed password) until the code is verified.
     */
    public function start(
        string $type,
        ?int $userId,
        array $device,
        ?int $revokeDeviceId = null,
        ?array $signup = null,
        ?string $passwordHash = null
    ): string {
        $id = Str::random(40);

        $this->save($id, [
            'user_type' => $type,
            'user_id' => $userId,
            'password_fingerprint' => $passwordHash ? $this->hash($passwordHash) : null,
            'signup' => $signup,
            'device' => $device,
            'revoke_device_id' => $revokeDeviceId,
            'expires_at' => time() + self::CHALLENGE_TTL,
            'channel' => null,
            'code_hash' => null,
            'code_expires_at' => null,
            'code_sent_at' => null,
            'attempts' => 0,
        ]);

        return $id;
    }

    public function find(string $id, string $type): ?array
    {
        $challenge = Cache::get($this->key($id));

        if (!$challenge || $challenge['user_type'] !== $type || $challenge['expires_at'] <= time()) {
            return null;
        }
        if (!isset($challenge['signup'])) {
            $model = Relation::getMorphedModel($type);
            $user = $model ? $model::find($challenge['user_id']) : null;
            if (!$user || !$user->isActive() || empty($challenge['password_fingerprint']) ||
                !hash_equals($challenge['password_fingerprint'], $this->hash($user->password))) {
                $this->forget($id);
                return null;
            }
        }
        return $challenge;
    }

    /** The masked email and phone the user can choose from. */
    public function options(string $email, ?string $phone): array
    {
        $options = [
            ['channel' => MessageChannel::Email->value, 'masked' => Mask::email($email)],
        ];

        if ($phone) {
            $options[] = ['channel' => MessageChannel::Sms->value, 'masked' => Mask::phone($phone)];
        }

        return $options;
    }

    public function makeCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function secondsUntilResend(array $challenge): int
    {
        return max(0, (int) $challenge['code_sent_at'] + self::RESEND_AFTER - time());
    }

    public function storeCode(string $id, array $challenge, string $channel, string $code): void
    {
        $challenge['channel'] = $channel;
        $challenge['code_hash'] = $this->hash($code);
        $challenge['code_sent_at'] = time();
        $challenge['code_expires_at'] = time() + self::CODE_TTL;
        $challenge['attempts'] = 0;

        $this->save($id, $challenge);
    }

    /** @return string ok | wrong | expired | locked | no_code */
    public function verify(string $id, array $challenge, string $code): string
    {
        if (!$challenge['code_hash']) {
            return 'no_code';
        }

        if (time() > $challenge['code_expires_at']) {
            return 'expired';
        }

        if (hash_equals($challenge['code_hash'], $this->hash($code))) {
            return 'ok';
        }

        $challenge['attempts']++;

        if ($challenge['attempts'] >= self::MAX_ATTEMPTS) {
            $this->forget($id);

            return 'locked';
        }

        $this->save($id, $challenge);

        return 'wrong';
    }

    public function forget(string $id): void
    {
        Cache::forget($this->key($id));
    }

    private function save(string $id, array $challenge): void
    {
        Cache::put($this->key($id), $challenge, max(1, $challenge['expires_at'] - time()));
    }

    private function hash(string $code): string
    {
        return hash_hmac('sha256', $code, (string) config('app.key'));
    }

    private function key(string $id): string
    {
        return 'login_challenge:' . $id;
    }
}