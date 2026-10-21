<?php

namespace App\Services\Affiliate;

use App\Enums\AffiliateStatus;
use App\Enums\AttributionStatus;
use App\Models\AffiliateAttribution;
use App\Models\AffiliateClick;
use App\Models\AffiliateProfile;
use App\Models\AffiliateSetting;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Click tracking + "this student belongs to that affiliate" attribution.
 * Used by AffiliateClickController (click) and AuthController (attribute on register).
 */
class AttributionService
{
    /**
     * A visit through /ref/{ref_code}. Always returns 200-shaped data, never throws for a bad/dead
     * ref code - the frontend just gets tracked=false and carries on with no affiliate cookie,
     * same "fail quiet" rule used for the public combo/course visibility.
     */
    public function recordClick(string $refCode, Request $request, ?string $visitorToken, ?int $courseId, ?string $landingUrl): array
    {
        $settings = AffiliateSetting::current();

        if (!$settings->is_active) {
            return ['tracked' => false];
        }

        $profile = AffiliateProfile::with('affiliate')
            ->whereRaw('upper(ref_code) = ?', [strtoupper($refCode)])
            ->first();

        if (!$profile || $profile->status !== AffiliateStatus::Approved || !$profile->affiliate?->isActive()) {
            return ['tracked' => false];
        }

        $token = $this->cleanToken($visitorToken) ?? $this->newToken();

        $click = AffiliateClick::create([
            'affiliate_id' => $profile->affiliate_id,
            'course_list_id' => $this->validCourseId($courseId),
            'visitor_token' => $token,
            'ip_hash' => $request->ip() ? hash('sha256', $request->ip()) : null,
            'user_agent' => $request->userAgent() ? substr($request->userAgent(), 0, 255) : null,
            'referer' => $request->header('referer') ? substr($request->header('referer'), 0, 500) : null,
            'landing_url' => $landingUrl ? substr($landingUrl, 0, 500) : null,
        ]);

        return [
            'tracked' => true,
            'visitor_token' => $token,
            'affiliate_name' => $profile->affiliate->name,
            'cookie_days' => $settings->cookie_days,
            'expires_at' => now()->addDays($settings->cookie_days)->toIso8601String(),
            'click_id' => $click->id,
        ];
    }

    /**
     * Called right after a student account is created. If the visitor_token sent at registration
     * matches a click within the current cookie window, for an affiliate still approved, an "open"
     * attribution is created - this is what checkout will later look up to credit a commission.
     * Silent no-op otherwise: a missing/expired/dead click is not an error, just "no affiliate".
     */
    public function attributeOnRegister(Student $student, ?string $visitorToken): void
    {
        $settings = AffiliateSetting::current();

        if (!$settings->is_active) {
            return;
        }

        $click = $this->latestValidClick($visitorToken, $settings->cookie_days);

        if (!$click) {
            return;
        }

        $profile = AffiliateProfile::where('affiliate_id', $click->affiliate_id)->first();

        if (!$profile || $profile->status !== AffiliateStatus::Approved) {
            return;
        }

        // One visitor registering twice can't happen (register fails on duplicate email/phone),
        // but guard anyway: never give a student a second open attribution.
        if (AffiliateAttribution::where('student_id', $student->id)->exists()) {
            return;
        }

        AffiliateAttribution::create([
            'affiliate_id' => $click->affiliate_id,
            'student_id' => $student->id,
            'affiliate_click_id' => $click->id,
            'visitor_token' => $click->visitor_token,
            'status' => AttributionStatus::Open,
            'expires_at' => now()->addDays($settings->cookie_days),
        ]);
    }

    /**
     * GET /ref/attribution?visitor_token=... - lets the frontend show "you're shopping via X"
     * before the visitor has signed up. Looks at the same click window attributeOnRegister would
     * use, so what this returns is exactly what would happen if they registered right now.
     */
    public function preview(?string $visitorToken): array
    {
        $settings = AffiliateSetting::current();

        if (!$settings->is_active) {
            return ['attributed' => false];
        }

        $click = $this->latestValidClick($visitorToken, $settings->cookie_days);

        if (!$click) {
            return ['attributed' => false];
        }

        $profile = AffiliateProfile::with('affiliate')->where('affiliate_id', $click->affiliate_id)->first();

        if (!$profile || $profile->status !== AffiliateStatus::Approved) {
            return ['attributed' => false];
        }

        return [
            'attributed' => true,
            'affiliate_name' => $profile->affiliate->name,
            'expires_at' => $click->created_at->copy()->addDays($settings->cookie_days)->toIso8601String(),
        ];
    }

    /** The most recent click for this visitor_token that's still inside the cookie window. */
    private function latestValidClick(?string $visitorToken, int $cookieDays): ?AffiliateClick
    {
        $token = $this->cleanToken($visitorToken);

        if (!$token) {
            return null;
        }

        return AffiliateClick::where('visitor_token', $token)
            ->where('created_at', '>=', now()->subDays($cookieDays))
            ->latest('id')
            ->first();
    }

    private function cleanToken(?string $token): ?string
    {
        $token = trim((string) $token);

        return ($token !== '' && strlen($token) <= 64) ? $token : null;
    }

    private function newToken(): string
    {
        return (string) Str::ulid();
    }

    private function validCourseId(?int $courseId): ?int
    {
        return $courseId && Course::whereKey($courseId)->exists() ? $courseId : null;
    }
}
