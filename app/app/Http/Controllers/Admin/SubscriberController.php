<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubscriberController extends Controller
{
    public function index(Request $request): View
    {
        $subscribers = Subscriber::withCount('downloads')->when($request->status, fn ($q, $status) => $q->where('status', $status))->latest()->paginate(25)->withQueryString();
        return view('admin.subscribers', compact('subscribers'));
    }

    public function export(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w'); fwrite($out, "\xEF\xBB\xBF"); fputcsv($out, ['Email', 'Status', 'Verified at', 'Created']);
            Subscriber::orderBy('id')->chunk(200, fn ($items) => $items->each(fn ($row) => fputcsv($out, [$row->email, $row->status, $row->verified_at, $row->created_at]))); fclose($out);
        }, 'subscribers-'.now()->format('Y-m-d').'.csv');
    }
}
