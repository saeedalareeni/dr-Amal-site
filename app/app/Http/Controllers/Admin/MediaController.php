<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
use App\Models\PageVersion;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function index(Request $request): View
    {
        $assets = MediaAsset::query()->when($request->q, fn ($q, $term) => $q->where('original_name', 'like', '%'.$term.'%'))->latest()->paginate(30)->withQueryString();
        return view('admin.media', compact('assets'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'max:25600', 'mimes:jpg,jpeg,png,webp,avif,gif,svg,pdf,mp3,m4a,xlsx'],
            'alt_ar' => ['nullable', 'string', 'max:190'], 'alt_en' => ['nullable', 'string', 'max:190'],
        ]);
        $file = $data['file'];
        if ($file->getClientOriginalExtension() === 'svg' && preg_match('/<script|on\w+\s*=|javascript:/i', $file->get())) {
            return back()->withErrors(['file' => 'ملف SVG يحتوي عناصر غير آمنة.']);
        }
        $path = $file->store('media/uploads/'.now()->format('Y/m'), 'public');
        $asset = MediaAsset::create([
            'disk' => 'public', 'path' => $path, 'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType() ?: 'application/octet-stream', 'size' => $file->getSize(),
            'checksum' => hash_file('sha256', $file->getRealPath()),
            'alt' => ['ar' => $data['alt_ar'] ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), 'en' => $data['alt_en'] ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)],
        ]);
        ActivityLogger::log('media.uploaded', $asset);
        return back()->with('status', 'تم رفع الملف.');
    }

    public function update(Request $request, MediaAsset $media): RedirectResponse
    {
        $data = $request->validate(['alt_ar' => ['required', 'string', 'max:190'], 'alt_en' => ['required', 'string', 'max:190']]);
        $media->update(['alt' => ['ar' => $data['alt_ar'], 'en' => $data['alt_en']]]);
        ActivityLogger::log('media.updated', $media);
        return back()->with('status', 'تم تحديث النص البديل.');
    }

    public function destroy(MediaAsset $media): RedirectResponse
    {
        $needle = json_encode($media->path, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $used = PageVersion::query()->whereRaw('CAST(content AS CHAR) LIKE ?', ['%'.trim($needle, '"').'%'])->exists();
        if ($used) return back()->withErrors(['media' => 'لا يمكن حذف ملف مستخدم داخل إحدى نسخ الصفحة.']);
        Storage::disk($media->disk)->delete($media->path); $media->delete();
        ActivityLogger::log('media.deleted', null, ['path' => $media->path]);
        return back()->with('status', 'تم حذف الملف.');
    }
}
