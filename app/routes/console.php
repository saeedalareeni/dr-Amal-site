<?php

use App\Services\LegacyMediaImporter;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('cms:import-media', function (LegacyMediaImporter $importer) {
    $result = $importer->import();
    $this->info("Verified {$result['verified']} files; imported {$result['imported']}; WebP variants {$result['variants']}.");
})->purpose('Import and verify legacy portfolio media');
