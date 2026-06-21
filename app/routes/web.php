<?php

use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\SubscriberController as AdminSubscriberController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\LeadSubmissionController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SubscriberController;
use App\Models\Page;
use Illuminate\Support\Facades\Route;

Route::get('/', fn (PageController $controller) => $controller->home('ar'))->name('home.ar');
Route::get('/en', fn (PageController $controller) => $controller->home('en'))->name('home.en');

Route::post('/contact', [LeadSubmissionController::class, 'store'])->middleware('throttle:contact')->name('contact.store');
Route::post('/newsletter', [SubscriberController::class, 'store'])->middleware('throttle:newsletter')->name('subscribers.store');
Route::get('/newsletter/verify/{subscriber}', [SubscriberController::class, 'verify'])->name('subscribers.verify');
Route::get('/newsletter/unsubscribe/{subscriber}', [SubscriberController::class, 'unsubscribe'])->name('subscribers.unsubscribe');
Route::get('/downloads/{subscriber}/{resource}', DownloadController::class)->name('downloads.file');

Route::get('/sitemap.xml', function () {
    $updated = Page::where('slug', 'home')->with('publishedVersion')->first()?->publishedVersion?->published_at ?? now();
    return response()->view('site.sitemap', compact('updated'))->header('Content-Type', 'application/xml');
})->name('sitemap');
Route::get('/robots.txt', fn () => response("User-agent: *\nAllow: /\nDisallow: /admin\nSitemap: ".route('sitemap')."\n", 200, ['Content-Type' => 'text/plain']));

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [LoginController::class, 'create'])->name('login');
    Route::post('/admin/login', [LoginController::class, 'store'])->middleware('throttle:login');
    Route::get('/admin/forgot-password', [PasswordController::class, 'forgot'])->name('password.request');
    Route::post('/admin/forgot-password', [PasswordController::class, 'email'])->name('password.email')->middleware('throttle:5,1');
    Route::get('/admin/reset-password/{token}', [PasswordController::class, 'reset'])->name('password.reset');
    Route::post('/admin/reset-password', [PasswordController::class, 'update'])->name('password.update');
});

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::put('/password', [PasswordController::class, 'change'])->name('password.change');
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('/content', [ContentController::class, 'edit'])->name('content.edit');
    Route::put('/content', [ContentController::class, 'update'])->name('content.update');
    Route::post('/content/publish', [ContentController::class, 'publish'])->name('content.publish');
    Route::post('/content/restore/{version}', [ContentController::class, 'restore'])->name('content.restore');
    Route::get('/preview/{version}/{locale?}', [PageController::class, 'preview'])->where('locale', 'ar|en')->name('preview');
    Route::get('/media', [MediaController::class, 'index'])->name('media.index');
    Route::post('/media', [MediaController::class, 'store'])->name('media.store');
    Route::put('/media/{media}', [MediaController::class, 'update'])->name('media.update');
    Route::delete('/media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
    Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
    Route::get('/leads/export', [LeadController::class, 'export'])->name('leads.export');
    Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
    Route::put('/leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
    Route::post('/leads/{lead}/notes', [LeadController::class, 'note'])->name('leads.notes.store');
    Route::get('/subscribers', [AdminSubscriberController::class, 'index'])->name('subscribers.index');
    Route::get('/subscribers/export', [AdminSubscriberController::class, 'export'])->name('subscribers.export');
    Route::get('/activity', ActivityController::class)->name('activity');
});
