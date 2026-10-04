<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
class PasswordReset extends Model
{
    protected $guarded = ['id'];
    protected $hidden = ['code_hash', 'reset_token_hash', 'password_fingerprint'];
    protected function casts(): array
    {
        return ['challenge_expires_at' => 'datetime', 'code_sent_at' => 'datetime',
            'code_expires_at' => 'datetime', 'reset_token_expires_at' => 'datetime',
            'verified_at' => 'datetime', 'used_at' => 'datetime', 'attempts' => 'integer'];
    }
    public function user(): MorphTo { return $this->morphTo('user', 'user_type', 'user_id'); }
}
