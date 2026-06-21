<?php

namespace App\Http\Controllers;

use App\Mail\VerifySubscriber;
use App\Models\Subscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class SubscriberController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email:rfc', 'max:190'],
            'locale' => ['nullable', 'in:ar,en'],
            'website' => ['nullable', 'max:0'],
            'form_started' => ['required', 'integer'],
        ]);

        $subscriber = Subscriber::firstOrNew(['email' => strtolower($data['email'])]);
        if ($subscriber->status === 'verified') {
            return response()->json(['message' => $data['locale'] === 'en' ? 'This email is already verified.' : 'هذا البريد موثق مسبقًا.']);
        }

        $rawToken = bin2hex(random_bytes(32));
        $subscriber->fill([
            'status' => 'pending', 'verification_token' => hash('sha256', $rawToken),
            'verification_expires_at' => now()->addHour(), 'locale' => $data['locale'] ?? 'ar',
            'ip_address' => $request->ip(), 'user_agent' => $request->userAgent(),
        ])->save();

        $url = URL::temporarySignedRoute('subscribers.verify', now()->addHour(), ['subscriber' => $subscriber->id, 'token' => $rawToken]);
        Mail::to($subscriber->email)->queue(new VerifySubscriber($subscriber, $url));

        return response()->json(['message' => $subscriber->locale === 'en' ? 'Check your inbox for the verification link.' : 'تحقق من بريدك، أرسلنا لك رابط التفعيل.'], 201);
    }

    public function verify(Request $request, Subscriber $subscriber): View
    {
        abort_unless($request->hasValidSignature(), 403);
        abort_if($subscriber->verification_expires_at?->isPast(), 410);
        abort_unless(hash_equals((string) $subscriber->verification_token, hash('sha256', (string) $request->query('token'))), 403);

        $subscriber->update(['status' => 'verified', 'verified_at' => now(), 'verification_token' => null, 'verification_expires_at' => null]);
        session(['verified_subscriber_id' => $subscriber->id]);

        return view('downloads.index', ['subscriber' => $subscriber, 'resources' => config('site_content.content.freebies')]);
    }

    public function unsubscribe(Request $request, Subscriber $subscriber): RedirectResponse
    {
        abort_unless($request->hasValidSignature(), 403);
        $subscriber->update(['status' => 'unsubscribed', 'unsubscribed_at' => now()]);
        return redirect($subscriber->locale === 'en' ? '/en' : '/')->with('status', $subscriber->locale === 'en' ? 'You have been unsubscribed.' : 'تم إلغاء اشتراكك.');
    }
}
