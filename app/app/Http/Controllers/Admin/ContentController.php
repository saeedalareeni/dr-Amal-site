<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
use App\Models\Page;
use App\Models\PageVersion;
use App\Services\ActivityLogger;
use App\Services\LegacyMediaImporter;
use App\Services\ThemeValidator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function edit(): View
    {
        $page = $this->homePage()->load('versions');

        return view('admin.content', [
            'pageModel' => $page,
            'version' => $this->draft($page),
            'themeDefaults' => ThemeValidator::DEFAULTS,
            'mediaAssets' => $this->mediaAssets(),
        ]);
    }

    public function update(Request $request, ThemeValidator $themeValidator): RedirectResponse
    {
        $page = $this->homePage();
        $content = $request->input('content', []);
        $seo = $request->input('seo', []);
        $theme = $themeValidator->validate($request->input('theme', []));
        $this->assertTranslations($content);
        $this->assertTranslations($seo);

        $draft = $this->draft($page);
        $draft->update(['content' => $content, 'theme' => $theme, 'seo' => [...$seo, 'indexable' => $request->boolean('seo.indexable')]]);
        ActivityLogger::log('content.saved', $draft);
        return back()->with('status', 'تم حفظ المسودة بنجاح.');
    }

    public function publish(): RedirectResponse
    {
        $page = $this->homePage();
        $draft = $this->draft($page);
        DB::transaction(function () use ($page, $draft) {
            PageVersion::whereKey($page->published_version_id)->update(['status' => 'archived']);
            $draft->update(['status' => 'published', 'published_at' => now()]);
            $page->update(['published_version_id' => $draft->id]);
            $page->versions()->create(['version' => $draft->version + 1, 'status' => 'draft', 'content' => $draft->content, 'theme' => $draft->theme, 'seo' => $draft->seo, 'created_by' => auth()->id()]);
        });
        Cache::forget('cms.home.published'); ActivityLogger::log('content.published', $draft);
        return back()->with('status', 'تم نشر النسخة الجديدة.');
    }

    public function restore(PageVersion $version): RedirectResponse
    {
        $page = $this->homePage();
        abort_unless($version->page_id === $page->id, 404);
        $draft = $this->draft($page);
        $draft->update(['content' => $version->content, 'theme' => $version->theme, 'seo' => $version->seo]);
        ActivityLogger::log('content.restored', $version, ['draft_version' => $draft->version]);
        return back()->with('status', 'تمت استعادة النسخة إلى المسودة. راجعها ثم انشرها.');
    }

    private function draft(Page $page): PageVersion
    {
        return $page->draft() ?? $page->versions()->create([
            'version' => ((int) $page->versions()->max('version')) + 1, 'status' => 'draft',
            'content' => $page->publishedVersion->content, 'theme' => $page->publishedVersion->theme,
            'seo' => $page->publishedVersion->seo, 'created_by' => auth()->id(),
        ]);
    }

    private function homePage(): Page
    {
        $page = Page::firstOrCreate(['slug' => 'home']);

        if (! $page->published_version_id || ! $page->publishedVersion()->exists()) {
            $defaults = config('site_content');
            $version = $page->versions()->create([
                'version' => 1,
                'status' => 'published',
                'content' => $defaults['content'],
                'theme' => $defaults['theme'],
                'seo' => $defaults['seo'],
                'created_by' => auth()->id(),
                'published_at' => now(),
            ]);

            $page->update(['published_version_id' => $version->id]);
        }

        return $page->loadMissing('publishedVersion');
    }

    private function mediaAssets()
    {
        if (! MediaAsset::exists()) {
            $importer = app(LegacyMediaImporter::class);
            $importer->import();
            $importer->syncPublicStorage();
        }

        return MediaAsset::query()
            ->latest()
            ->get(['id', 'path', 'original_name', 'mime_type', 'disk', 'alt']);
    }

    private function assertTranslations(array $data, string $path = 'content'): void
    {
        foreach ($data as $key => $value) {
            if (is_array($value) && array_key_exists('ar', $value) && array_key_exists('en', $value)) {
                if (trim((string) $value['ar']) === '' || trim((string) $value['en']) === '') {
                    throw ValidationException::withMessages([$path.'.'.$key => 'النص العربي والإنجليزي مطلوبان قبل النشر.']);
                }
            } elseif (is_array($value)) $this->assertTranslations($value, $path.'.'.$key);
        }
    }
}
