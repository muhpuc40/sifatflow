<?php

namespace App\Http\Controllers\Admin\Sales;

use App\Enums\MessageChannel;
use App\Enums\VoucherStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentVoucherRequest;
use App\Models\PaymentVoucher;
use App\Models\Student;
use App\Services\Messaging\MessageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class PaymentVoucherController extends Controller
{
    /** GET /vouchers */
    public function index(Request $request): View
    {
        $filter = $request->input('status');

        $vouchers = PaymentVoucher::query()
            ->with('student:id,name,email')
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.$request->input('q').'%';
                $q->where(fn ($w) => $w->where('code', 'like', $term)
                    ->orWhereHas('student', fn ($s) => $s->where('name', 'like', $term)->orWhere('email', 'like', $term)));
            })
            ->when($filter === 'active', fn ($q) => $q->active())
            ->when($filter === 'expired', fn ($q) => $q->where('status', VoucherStatus::Active)->where('expires_at', '<', now()))
            ->when($filter === 'used', fn ($q) => $q->where('status', VoucherStatus::Used))
            ->when($filter === 'cancelled', fn ($q) => $q->where('status', VoucherStatus::Cancelled))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.vouchers.index', [
            'vouchers' => $vouchers,
            'filter' => $filter,
            'students' => Student::where('status', 'active')->orderBy('name')->get(['id', 'name', 'email']),
        ]);
    }

    /** POST /vouchers */
    public function store(PaymentVoucherRequest $request, MessageService $messages): RedirectResponse
    {
        $data = $request->safe()->except(['notify', 'code']);
        $data['code'] = $this->codeFor($request->input('code'));
        $data['created_by'] = $request->user()->id;

        $voucher = PaymentVoucher::create($data);
        $voucher->load('student');

        $message = 'Voucher created.';

        if ($request->boolean('notify', true)) {
            try {
                $messages->send(MessageChannel::Email, $voucher->student->email, 'voucher-code', [
                    'name' => $voucher->student->name,
                    'code' => $voucher->code,
                    'amount_label' => $this->amountLabel($voucher),
                    'expires_at' => $voucher->expires_at->format('d M Y'),
                ], 'Your discount voucher - '.config('app.name'));
                $voucher->update(['emailed_at' => now()]);
            } catch (Throwable) {
                $message = 'Voucher created, but the email could not be sent. Use "Resend email" below.';
            }
        }

        return back()->with('success', $message);
    }

    /** POST /vouchers/{voucher}/resend */
    public function resend(PaymentVoucher $voucher, MessageService $messages): RedirectResponse
    {
        if (! $voucher->canBeUsed()) {
            return back()->with('error', 'This voucher is no longer active, so it was not sent.');
        }

        $voucher->loadMissing('student');

        try {
            $messages->send(MessageChannel::Email, $voucher->student->email, 'voucher-code', [
                'name' => $voucher->student->name,
                'code' => $voucher->code,
                'amount_label' => $this->amountLabel($voucher),
                'expires_at' => $voucher->expires_at->format('d M Y'),
            ], 'Your discount voucher - '.config('app.name'));
            $voucher->update(['emailed_at' => now()]);

            return back()->with('success', 'Voucher emailed to '.$voucher->student->email.'.');
        } catch (Throwable) {
            return back()->with('error', 'The email could not be sent. Please try again.');
        }
    }

    /** PATCH /vouchers/{voucher}/cancel */
    public function cancel(PaymentVoucher $voucher): RedirectResponse
    {
        if ($voucher->status !== VoucherStatus::Active || $voucher->used_at) {
            return back()->with('error', 'Only an active, unused voucher can be cancelled.');
        }

        $voucher->update(['status' => VoucherStatus::Cancelled]);

        return back()->with('success', 'Voucher cancelled.');
    }

    /** DELETE /vouchers/{voucher} - only when it was never used */
    public function destroy(PaymentVoucher $voucher): RedirectResponse
    {
        if ($voucher->used_at) {
            return back()->with('error', 'A used voucher cannot be deleted.');
        }

        $voucher->delete();

        return back()->with('success', 'Voucher deleted.');
    }

    private function codeFor(?string $given): string
    {
        if ($given) {
            return strtoupper($given);
        }

        do {
            $code = 'VCH-'.strtoupper(Str::random(6));
        } while (PaymentVoucher::where('code', $code)->exists());

        return $code;
    }

    private function amountLabel(PaymentVoucher $voucher): string
    {
        return $voucher->amount_type->value === 'percent'
            ? rtrim(rtrim((string) $voucher->amount, '0'), '.').'% off'
            : number_format((float) $voucher->amount).' Tk off';
    }
}
