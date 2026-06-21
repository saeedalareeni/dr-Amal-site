<?php

namespace App\Http\Controllers;

use App\Models\Download;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadController extends Controller
{
    public function __invoke(Request $request, Subscriber $subscriber, string $resource): BinaryFileResponse
    {
        abort_unless($request->hasValidSignature(), 403);
        abort_unless($subscriber->status === 'verified' && session('verified_subscriber_id') === $subscriber->id, 403);
        $item = collect(config('site_content.content.freebies'))->firstWhere('key', $resource);
        abort_unless($item && Storage::disk('local')->exists($item['file']), 404);

        Download::create([
            'subscriber_id' => $subscriber->id, 'resource_key' => $resource,
            'ip_address' => $request->ip(), 'user_agent' => $request->userAgent(), 'downloaded_at' => now(),
        ]);

        return response()->download(Storage::disk('local')->path($item['file']), basename($item['file']));
    }
}
