<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\PageVersion;
use App\Support\PageData;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    public function home(string $locale = 'ar'): Response
    {
        abort_unless(in_array($locale, ['ar', 'en'], true), 404);
        app()->setLocale($locale);

        $version = Cache::remember('cms.home.published', 3600, fn () =>
            Page::where('slug', 'home')->with('publishedVersion')->first()?->publishedVersion
        );
        $data = $this->data($version, $locale);

        return response()->view('site.home', ['page' => $data, 'locale' => $locale]);
    }

    public function preview(PageVersion $version, string $locale = 'ar'): Response
    {
        abort_unless($version->page?->slug === 'home', 404);
        return response()->view('site.home', [
            'page' => $this->data($version, $locale),
            'locale' => $locale,
            'isPreview' => true,
        ]);
    }

    private function data(?PageVersion $version, string $locale): PageData
    {
        $defaults = config('site_content');
        return new PageData(
            $version?->content ?? $defaults['content'],
            $version?->theme ?? $defaults['theme'],
            $version?->seo ?? $defaults['seo'],
            $locale,
        );
    }
}
