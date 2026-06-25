@php
    $keyString = (string) $key;
    $isAssoc = is_array($value) && array_keys($value) !== range(0, count($value) - 1);
    $isList = is_array($value) && ! $isAssoc;
    $isTranslation = is_array($value)
        && array_key_exists('ar', $value)
        && array_key_exists('en', $value)
        && count(array_diff(array_keys($value), ['ar', 'en'])) === 0;

    $labelMap = [
        'brand' => 'الهوية',
        'navigation' => 'القائمة',
        'hero' => 'الواجهة الرئيسية',
        'platforms' => 'المنصات',
        'stats' => 'الإحصاءات',
        'why' => 'لماذا أمل؟',
        'expertise' => 'قسم الخبرة',
        'headings' => 'عناوين الأقسام',
        'services' => 'الخدمات',
        'cases' => 'نتائج الأعمال',
        'stores' => 'المتاجر الإلكترونية',
        'social' => 'السوشال ميديا',
        'testimonials' => 'آراء العملاء',
        'audio' => 'الصوتيات',
        'logos' => 'الشعارات',
        'freebies' => 'الملفات المجانية',
        'blog' => 'المدونة',
        'newsletter' => 'النشرة',
        'contact' => 'التواصل',
        'title' => 'العنوان',
        'description' => 'الوصف',
        'intro' => 'المقدمة',
        'label' => 'التسمية',
        'image' => 'الصورة',
        'images' => 'الصور',
        'image_captions' => 'تسميات الصور',
        'visible' => 'ظاهر',
        'order' => 'الترتيب',
        'name' => 'الاسم',
        'url' => 'الرابط',
        'url_label' => 'نص الرابط',
        'icon' => 'الأيقونة',
        'alt' => 'النص البديل',
        'subtitle' => 'العنوان الفرعي',
        'badge' => 'الشارة',
        'achievements' => 'الإنجازات',
        'challenge' => 'التحدي',
        'solution' => 'الحل',
        'metrics' => 'الأرقام',
        'tags' => 'الوسوم',
        'file' => 'الملف',
        'button' => 'نص الزر',
        'kicker' => 'الترويسة',
        'words' => 'الكلمات المتحركة',
        'chips' => 'الخصائص',
        'cards' => 'البطاقات',
        'links' => 'الروابط',
        'services' => 'الخدمات',
        'value' => 'القيمة',
        'suffix' => 'اللاحقة',
        'category' => 'التصنيف',
    ];
    $label = $labelMap[$keyString] ?? str_replace('_', ' ', $keyString);
    $fieldName = "{$prefix}[{$keyString}]";
    $depth = substr_count($prefix, '[');
    $mediaCollection = collect($mediaAssets ?? []);
    $isMediaValue = is_string($value) && str_starts_with($value, 'media/');
    $isMediaField = $isMediaValue || preg_match('/(^|_)(image|images|file|logo|audio|og_image)$/i', $keyString);
    $expectsImage = preg_match('/(^|_)(image|images|logo|og_image)$/i', $keyString);
    $mediaOptions = $expectsImage
        ? $mediaCollection->filter(fn ($asset) => str_starts_with((string) $asset->mime_type, 'image/'))
        : $mediaCollection;
    $currentMediaUrl = $isMediaValue ? Storage::disk('public')->url($value) : '';
    $currentIsImage = $isMediaValue && preg_match('/\.(jpe?g|png|webp|avif|gif|svg)$/i', $value);
@endphp

@if($isTranslation)
    <div class="grid gap-3 md:grid-cols-2">
        <label>
            <span class="admin-label">{{ $label }} - عربي</span>
            <textarea class="admin-input min-h-24" name="{{ $fieldName }}[ar]" required>{{ $value['ar'] }}</textarea>
        </label>
        <label dir="ltr">
            <span class="admin-label text-left">{{ $label }} - English</span>
            <textarea class="admin-input min-h-24 text-left" name="{{ $fieldName }}[en]" required>{{ $value['en'] }}</textarea>
        </label>
    </div>
@elseif(is_bool($value))
    <label class="flex items-center gap-2 rounded-xl bg-slate-50 p-3">
        <input type="hidden" name="{{ $fieldName }}" value="0">
        <input type="checkbox" name="{{ $fieldName }}" value="1" @checked($value)>
        <span class="font-bold">{{ $label }}</span>
    </label>
@elseif($isList)
    <details class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4" data-content-list data-list-prefix="{{ $fieldName }}" {{ $depth < 1 ? 'open' : '' }}>
        <summary class="cursor-pointer font-extrabold text-slate-700">
            {{ $label }} <small class="text-slate-400">({{ count($value) }})</small>
        </summary>

        @if(count($value))
            @php($templateKey = array_key_first($value))
            <template data-content-list-template>
                <div class="content-list-item rounded-2xl border border-slate-200 bg-white p-4" data-content-list-item>
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                        <strong class="text-slate-700" data-content-item-title>عنصر</strong>
                        <div class="flex gap-2">
                            <button class="admin-btn-secondary px-3 py-1.5 text-sm" type="button" data-content-duplicate>نسخ</button>
                            <button class="rounded-xl border border-red-200 bg-red-50 px-3 py-1.5 text-sm font-bold text-red-700" type="button" data-content-remove>حذف</button>
                        </div>
                    </div>
                    <div class="space-y-4">
                        @include('admin.partials.content-field', ['key' => $templateKey, 'value' => $value[$templateKey], 'prefix' => $prefix.'['.$keyString.']'])
                    </div>
                </div>
            </template>
        @endif

        <div class="mt-4 space-y-4" data-content-items>
            @foreach($value as $childKey => $childValue)
                <div class="content-list-item rounded-2xl border border-slate-200 bg-white p-4" data-content-list-item>
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                        <strong class="text-slate-700" data-content-item-title>عنصر {{ is_numeric($childKey) ? ((int) $childKey + 1) : $childKey }}</strong>
                        <div class="flex gap-2">
                            <button class="admin-btn-secondary px-3 py-1.5 text-sm" type="button" data-content-duplicate>نسخ</button>
                            <button class="rounded-xl border border-red-200 bg-red-50 px-3 py-1.5 text-sm font-bold text-red-700" type="button" data-content-remove>حذف</button>
                        </div>
                    </div>
                    <div class="space-y-4">
                        @include('admin.partials.content-field', ['key' => $childKey, 'value' => $childValue, 'prefix' => $prefix.'['.$keyString.']'])
                    </div>
                </div>
            @endforeach
        </div>

        @if(count($value))
            <button class="admin-btn-secondary mt-4" type="button" data-content-add>
                <i class="fa-solid fa-plus"></i>
                إضافة عنصر
            </button>
        @endif
    </details>
@elseif(is_array($value))
    <details class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4" {{ $depth < 1 ? 'open' : '' }}>
        <summary class="cursor-pointer font-extrabold text-slate-700">
            {{ $label }} <small class="text-slate-400">({{ count($value) }})</small>
        </summary>
        <div class="mt-4 space-y-4">
            @foreach($value as $childKey => $childValue)
                @include('admin.partials.content-field', ['key' => $childKey, 'value' => $childValue, 'prefix' => $fieldName])
            @endforeach
        </div>
    </details>
@else
    <label class="block" @if($isMediaField) data-media-field @endif>
        <span class="admin-label">{{ $label }}</span>

        @if(is_string($value) && (mb_strlen($value) > 80 || str_contains($keyString, 'description') || str_contains($keyString, 'solution') || str_contains($keyString, 'challenge')))
            <textarea class="admin-input min-h-24" name="{{ $fieldName }}">{{ $value }}</textarea>
        @else
            <input class="admin-input" name="{{ $fieldName }}" value="{{ $value }}" @if($keyString === 'order') type="number" @endif @if($isMediaField) data-media-input @endif>
        @endif

        @if($isMediaField && $mediaOptions->isNotEmpty())
            <div class="mt-2 grid gap-2 md:grid-cols-[1fr_auto]">
                <select class="admin-input" data-media-picker>
                    <option value="">اختر من مكتبة الوسائط</option>
                    @foreach($mediaOptions as $asset)
                        <option value="{{ $asset->path }}" data-url="{{ $asset->url }}" data-type="{{ $asset->mime_type }}" @selected($value === $asset->path)>
                            {{ $asset->original_name }}
                        </option>
                    @endforeach
                </select>
                <a class="admin-btn-secondary" href="{{ route('admin.media.index') }}" target="_blank">
                    <i class="fa-solid fa-photo-film"></i>
                    المكتبة
                </a>
            </div>
            <div class="mt-2 rounded-xl border border-slate-200 bg-white p-2">
                <img class="h-28 w-full rounded-lg object-contain {{ $currentIsImage ? '' : 'hidden' }}" src="{{ $currentIsImage ? $currentMediaUrl : '' }}" alt="" data-media-preview>
                <a class="text-sm font-bold text-emerald-700 {{ $isMediaValue && ! $currentIsImage ? '' : 'hidden' }}" href="{{ $isMediaValue ? $currentMediaUrl : '#' }}" target="_blank" data-file-preview>
                    فتح الملف الحالي
                </a>
                <p class="text-xs text-slate-400 {{ $isMediaValue ? 'hidden' : '' }}" data-media-empty>لم يتم اختيار ملف بعد.</p>
            </div>
        @elseif($currentIsImage)
            <img class="mt-2 h-24 rounded-xl border bg-white object-contain" src="{{ $currentMediaUrl }}" alt="">
        @endif
    </label>
@endif
