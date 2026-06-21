<?php

use App\Mail\LeadConfirmation;
use App\Mail\LeadReceived;
use App\Mail\VerifySubscriber;
use App\Models\Download;
use App\Models\Lead;
use App\Models\Page;
use App\Models\PageVersion;
use App\Models\Subscriber;
use App\Models\User;
use App\Services\ThemeValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

test('the bilingual homepage includes localized seo and no remote asset dependencies', function () {
    $this->get('/')->assertOk()->assertSee('أمل العيسى')->assertSee('hreflang="en"', false)->assertDontSee('cdnjs.cloudflare.com');
    $this->get('/en')->assertOk()->assertSee('Digital Marketing Portfolio')->assertSee('lang="en"', false);
});

test('admin routes require authentication and a valid login regenerates access', function () {
    $user = User::factory()->create(['password' => 'StrongPassword!22']);
    $this->get('/admin')->assertRedirect('/admin/login');
    $this->post('/admin/login', ['email' => $user->email, 'password' => 'StrongPassword!22'])->assertRedirect('/admin');
    $this->actingAs($user)->get('/admin')->assertOk();
});

test('contact requests are persisted and both messages are queued', function () {
    Mail::fake();
    $response = $this->postJson('/contact', [
        'name' => 'سارة أحمد', 'email' => 'sara@example.com', 'consultation_date' => now()->addDay()->toDateString(),
        'service' => 'حملات إعلانية', 'message' => 'أرغب في إطلاق حملة إعلانية جديدة للمشروع.',
        'locale' => 'ar', 'website' => '', 'form_started' => now()->subSeconds(5)->timestamp,
    ]);
    $response->assertCreated();
    expect(Lead::first())->not->toBeNull()->status->toBe('new');
    Mail::assertQueued(LeadReceived::class);
    Mail::assertQueued(LeadConfirmation::class, fn ($mail) => $mail->hasTo('sara@example.com'));
});

test('newsletter verification is signed and protected downloads are tracked', function () {
    Mail::fake(); Storage::fake('local');
    Storage::disk('local')->put('freebies/social media.xlsx', 'sheet');
    $verificationUrl = null;
    $this->postJson('/newsletter', ['email' => 'reader@example.com', 'locale' => 'en', 'website' => '', 'form_started' => now()->subSeconds(5)->timestamp])->assertCreated();
    Mail::assertQueued(VerifySubscriber::class, function ($mail) use (&$verificationUrl) { $verificationUrl = $mail->verificationUrl; return true; });
    $subscriber = Subscriber::first();
    $this->get($verificationUrl)->assertOk();
    expect($subscriber->fresh()->status)->toBe('verified');
    $downloadUrl = URL::temporarySignedRoute('downloads.file', now()->addMinutes(5), ['subscriber' => $subscriber->id, 'resource' => 'social-media']);
    $this->withSession(['verified_subscriber_id' => $subscriber->id])->get($downloadUrl)->assertDownload('social media.xlsx');
    expect(Download::count())->toBe(1);
});

test('publishing a draft atomically archives the old version and creates a new draft', function () {
    $user = User::factory()->create(); $defaults = config('site_content');
    $page = Page::create(['slug' => 'home']);
    $published = PageVersion::create(['page_id' => $page->id, 'version' => 1, 'status' => 'published', 'content' => $defaults['content'], 'theme' => $defaults['theme'], 'seo' => $defaults['seo'], 'created_by' => $user->id, 'published_at' => now()]);
    $page->update(['published_version_id' => $published->id]);
    $draft = PageVersion::create(['page_id' => $page->id, 'version' => 2, 'status' => 'draft', 'content' => $defaults['content'], 'theme' => $defaults['theme'], 'seo' => $defaults['seo'], 'created_by' => $user->id]);
    $this->actingAs($user)->post('/admin/content/publish')->assertRedirect();
    expect($published->fresh()->status)->toBe('archived')->and($draft->fresh()->status)->toBe('published')->and($page->fresh()->published_version_id)->toBe($draft->id);
    expect($page->versions()->where('status', 'draft')->count())->toBe(1);
});

test('all administration screens render for the single administrator', function () {
    $user = User::factory()->create(); $defaults = config('site_content');
    $page = Page::create(['slug' => 'home']);
    $version = PageVersion::create(['page_id' => $page->id, 'version' => 1, 'status' => 'published', 'content' => $defaults['content'], 'theme' => $defaults['theme'], 'seo' => $defaults['seo'], 'created_by' => $user->id, 'published_at' => now()]);
    $page->update(['published_version_id' => $version->id]);
    foreach (['/admin', '/admin/content', '/admin/media', '/admin/leads', '/admin/subscribers', '/admin/activity'] as $url) {
        $this->actingAs($user)->get($url)->assertOk();
    }
});

test('unsafe color contrast is rejected', function () {
    app(ThemeValidator::class)->validate(ThemeValidator::DEFAULTS);
    expect(fn () => app(ThemeValidator::class)->validate([...ThemeValidator::DEFAULTS, 'ink' => '#fbfaf6']))->toThrow(\Illuminate\Validation\ValidationException::class);
});
