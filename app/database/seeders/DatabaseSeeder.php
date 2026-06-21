<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Page;
use App\Models\PageVersion;
use App\Services\LegacyMediaImporter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@amal.local')],
            [
                'name' => env('ADMIN_NAME', 'مدير الموقع'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'ChangeMe!2026')),
                'email_verified_at' => now(),
                'must_change_password' => true,
            ]
        );

        $defaults = config('site_content');
        $page = Page::firstOrCreate(['slug' => 'home']);
        if (! $page->published_version_id) {
            $version = PageVersion::create([
                'page_id' => $page->id,
                'version' => 1,
                'status' => 'published',
                'content' => $defaults['content'],
                'theme' => $defaults['theme'],
                'seo' => $defaults['seo'],
                'created_by' => $admin->id,
                'published_at' => now(),
            ]);
            $page->update(['published_version_id' => $version->id]);
        }

        app(LegacyMediaImporter::class)->import();
    }
}
