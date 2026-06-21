<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

final class PageData
{
    public function __construct(
        public readonly array $content,
        public readonly array $theme,
        public readonly array $seo,
        public readonly string $locale,
    ) {}

    public function get(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->content, $key, $default);
    }

    public function trans(string|array $key, string $default = ''): string
    {
        $value = is_array($key) ? $key : $this->get($key, $default);
        if (! is_array($value)) {
            return (string) $value;
        }

        return (string) ($value[$this->locale] ?? $value['ar'] ?? $value['en'] ?? $default);
    }

    public function items(string $key): array
    {
        $items = $this->get($key, []);

        return collect(is_array($items) ? $items : [])
            ->filter(fn (array $item) => (bool) ($item['visible'] ?? true))
            ->sortBy(fn (array $item) => (int) ($item['order'] ?? 0))
            ->values()->all();
    }

    public function media(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }

    public function seo(string $key, string $default = ''): string
    {
        $value = Arr::get($this->seo, $key, $default);
        if (! is_array($value)) return (string) $value;
        return (string) ($value[$this->locale] ?? $value['ar'] ?? $value['en'] ?? $default);
    }
}
