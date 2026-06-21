<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Download;
use App\Models\Lead;
use App\Models\MediaAsset;
use App\Models\Subscriber;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => ['leads' => Lead::count(), 'newLeads' => Lead::where('status', 'new')->count(), 'subscribers' => Subscriber::where('status', 'verified')->count(), 'downloads' => Download::count(), 'media' => MediaAsset::count()],
            'recentLeads' => Lead::latest()->limit(6)->get(),
            'activities' => ActivityLog::latest()->limit(8)->get(),
        ]);
    }
}
