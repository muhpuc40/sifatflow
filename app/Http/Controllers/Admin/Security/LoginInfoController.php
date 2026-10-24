<?php

namespace App\Http\Controllers\Admin\Security;

use App\Http\Controllers\Controller;
use App\Models\UserLoginInfo;
use App\Models\UserDevice;
use App\Support\Media;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Throwable;

/** Login history of every account type, with a profile modal and an IP location modal. */
class LoginInfoController extends Controller
{
    private const STATUSES = ['success', 'failed', 'blocked'];

    /** GET /security/login-info */
    /** GET /security/login-info */
    public function index(Request $request): View
    {
        $type = $request->input('user_type');
        $status = $request->input('status');

        $logins = UserLoginInfo::query()
            ->with('user')
            ->when(in_array($type, $this->types(), true), fn($q) => $q->where('user_type', $type))
            ->when(in_array($status, self::STATUSES, true), fn($q) => $q->where('status', $status))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->input('q') . '%';
                $q->where(fn($w) => $w->where('login_identifier', 'like', $term)->orWhere('created_at', 'like', $term));
            })
            ->latest('created_at')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        /** @var \Illuminate\Support\Collection<int, UserLoginInfo> $items */
        $items = $logins->getCollection();

        // Saved devices for this page (one query), keyed by type|user|device
        $saved = UserDevice::query()
            ->whereIn('device_id', $items->pluck('device_id')->filter()->unique()->all())
            ->get()
            ->keyBy(fn($d) => $d->user_type . '|' . $d->user_id . '|' . $d->device_id);

        return view('admin.security.login-info', [
            'logins' => $logins,
            'profiles' => $items->mapWithKeys(fn(UserLoginInfo $login) => [$login->id => $this->profile($login)]),
            'devices' => $items->mapWithKeys(fn($login) => [$login->id => $this->deviceInfo($login, $saved)]),
            'types' => $this->types(),
            'statuses' => self::STATUSES,
        ]);
    }

    /** Profile of the account (user table only) for the modal. null = account not found. */
    private function profile(UserLoginInfo $login): ?array
    {
        $u = $login->user;

        if (!$u) {
            return null;
        }

        $date = fn($d, $format = 'd M Y, h:i A') => $d?->format($format);

        // Name, email, type and status are shown in the left column of the modal.
        $rows = [
            ['User ID', $u->id],
            ['Public ID', $u->public_id],
            ['Phone', $u->phone],
            ['Verified', $u->verified_at ? $date($u->verified_at) . ' via ' . ($u->verified_channel ?? '—') : 'Not verified'],
            ['Last login', $date($u->last_login_at)],
            ['Registered', $date($u->created_at)],
        ];

        $rows = array_merge($rows, match ($login->user_type) {
            'student' => [
                ['Roll', $u->roll],
                ['Gender', ucfirst((string) $u->gender)],
                ['Date of birth', $date($u->date_of_birth, 'd M Y')],
                ['Address', $u->address],
            ],
            'instructor' => [
                ['Designation', $u->designation],
                ['Bio', $u->bio],
            ],
            default => [],
        });

        if ($u->trashed()) {
            $rows[] = ['Deleted at', $date($u->deleted_at)];
        }

        return [
            'name' => $u->name,
            'email' => $u->email,
            'avatar' => Media::url($u->avatar),
            'deleted' => $u->trashed(),
            'type' => ucfirst($login->user_type),
            'status' => $u->status->value,
            'rows' => $rows,
        ];
    }

    /** Device of one login attempt: what the log saved + the saved device row (if the login finished). */
    private function deviceInfo(UserLoginInfo $login, $saved): ?array
    {
        if (!$login->device_id && !$login->user_agent) {
            return null;
        }

        $row = $saved->get($login->user_type . '|' . $login->user_id . '|' . $login->device_id);
        [$browser, $platform] = $this->detect((string) $login->user_agent);
        $date = fn($d) => $d?->format('d M Y, h:i A');

        return [
            'rows' => [
                ['Device name', $row?->device_name ?? "{$browser} on {$platform}"],
                ['Browser', $row?->browser ?? $browser],
                ['Platform', $row?->platform ?? $platform],
                ['Device ID', $login->device_id],
                ['IP address', $login->ip_address],
                ['Saved device', $row ? ($row->revoked_at ? 'Signed out' : 'Signed in') : 'Not saved (login did not finish)'],
                ['Last used', $date($row?->last_used_at)],
                ['First seen', $date($row?->created_at)],
                ['User agent', $login->user_agent],
            ],
        ];
    }

    /** @return array{0: string, 1: string} browser and platform from a user agent. */
    private function detect(string $agent): array
    {
        $browser = match (true) {
            str_contains($agent, 'Edg/') => 'Edge',
            str_contains($agent, 'OPR/') || str_contains($agent, 'Opera') => 'Opera',
            str_contains($agent, 'Firefox/') => 'Firefox',
            str_contains($agent, 'Chrome/') => 'Chrome',
            str_contains($agent, 'Safari/') => 'Safari',
            default => 'Unknown browser',
        };

        $platform = match (true) {
            str_contains($agent, 'Android') => 'Android',
            str_contains($agent, 'iPhone') || str_contains($agent, 'iPad') => 'iOS',
            str_contains($agent, 'Windows') => 'Windows',
            str_contains($agent, 'Mac OS') => 'macOS',
            str_contains($agent, 'Linux') => 'Linux',
            default => 'Unknown OS',
        };

        return [$browser, $platform];
    }

    /** GET /security/login-info/{loginInfo}/location : IP to location (JSON for the modal). */
    public function location(UserLoginInfo $loginInfo): JsonResponse
    {
        $ip = $loginInfo->ip_address;

        if (!$ip || !filter_var($ip, FILTER_VALIDATE_IP)) {
            return response()->json(['ok' => false, 'message' => 'No valid IP address was saved for this login.']);
        }

        // Localhost / private networks have no public location.
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return response()->json([
                'ok' => false,
                'message' => "{$ip} is a local or private address, so it has no public location. You see this when testing on localhost.",
            ]);
        }

        $key = 'ip-geo:' . $ip;
        $geo = Cache::get($key);

        if (!$geo) {
            try {
                $response = Http::timeout(8)->acceptJson()->get('https://ipwho.is/' . $ip);
                $json = $response->json();
            } catch (Throwable $e) {
                report($e);

                return response()->json(['ok' => false, 'message' => 'The location service is not reachable right now. Try again later.']);
            }

            if (!$response->successful() || !($json['success'] ?? false)) {
                return response()->json(['ok' => false, 'message' => 'The location service could not find this IP. ' . ($json['message'] ?? '')]);
            }

            $geo = $this->formatGeo($json);
            Cache::put($key, $geo, now()->addDay());   // failed lookups are never cached
        }

        return response()->json($geo);
    }

    /** Profile of the account (user table only) for the modal. null = account not found. */


    /** Turns the ipwho.is answer into what the modal needs. */
    private function formatGeo(array $j): array
    {
        $lat = $j['latitude'] ?? null;
        $lon = $j['longitude'] ?? null;
        $hasMap = is_numeric($lat) && is_numeric($lon);
        $conn = $j['connection'] ?? [];
        $tz = $j['timezone'] ?? [];

        $localTime = !empty($tz['current_time'])
            ? rescue(fn() => Carbon::parse($tz['current_time'])->format('d M Y, h:i A'), $tz['current_time'], false)
            : null;

        $geo = [
            'ok' => true,
            'rows' => [
                ['IP address', $j['ip'] ?? null],
                ['IP version', $j['type'] ?? null],
                ['Continent', $j['continent'] ?? null],
                ['Country', trim(($j['country'] ?? '') . ' ' . (isset($j['country_code']) ? '(' . $j['country_code'] . ')' : ''))],
                ['Region', $j['region'] ?? null],
                ['City', $j['city'] ?? null],
                ['Postal code', $j['postal'] ?? null],
                ['Latitude', $lat],
                ['Longitude', $lon],
                ['Time zone', isset($tz['id']) ? $tz['id'] . ' (UTC' . ($tz['utc'] ?? '') . ')' : null],
                ['ISP', $conn['isp'] ?? null],
                ['Organization', $conn['org'] ?? null],
                ['ASN', isset($conn['asn']) ? 'AS' . $conn['asn'] : null],
                ['Domain', $conn['domain'] ?? null],
            ],
            'map_embed' => null,
            'map_link' => null,
        ];

        if ($hasMap) {
            $lat = (float) $lat;
            $lon = (float) $lon;
            $bbox = implode(',', [$lon - 0.06, $lat - 0.04, $lon + 0.06, $lat + 0.04]);

            $geo['map_embed'] = 'https://www.openstreetmap.org/export/embed.html?'
                . http_build_query(['bbox' => $bbox, 'layer' => 'mapnik', 'marker' => "$lat,$lon"]);
            $geo['map_link'] = "https://www.openstreetmap.org/?mlat=$lat&mlon=$lon#map=11/$lat/$lon";
        }

        return $geo;
    }

    /** admin, student, instructor, affiliate (from the morph map). */
    private function types(): array
    {
        return array_keys(Relation::morphMap());
    }
}