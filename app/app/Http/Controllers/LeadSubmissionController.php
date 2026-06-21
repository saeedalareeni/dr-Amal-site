<?php

namespace App\Http\Controllers;

use App\Mail\LeadConfirmation;
use App\Mail\LeadReceived;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class LeadSubmissionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'consultation_date' => ['nullable', 'date', 'after_or_equal:today'],
            'service' => ['required', 'string', 'max:120'],
            'message' => ['required', 'string', 'min:12', 'max:5000'],
            'locale' => ['nullable', 'in:ar,en'],
            'website' => ['nullable', 'max:0'],
            'form_started' => ['required', 'integer'],
        ]);

        if (now()->timestamp - (int) $data['form_started'] < 2) {
            throw ValidationException::withMessages(['message' => 'تعذر التحقق من الإرسال.']);
        }

        $lead = Lead::create([
            ...collect($data)->except(['website', 'form_started'])->all(),
            'locale' => $data['locale'] ?? 'ar',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $mailbox = config('mail.admin_address', config('mail.from.address'));
        Mail::to($mailbox)->queue(new LeadReceived($lead));
        Mail::to($lead->email)->queue(new LeadConfirmation($lead));

        return response()->json(['message' => $lead->locale === 'en' ? 'Your request was received successfully.' : 'تم استلام طلبك بنجاح.'], 201);
    }
}
