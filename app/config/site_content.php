<?php

$t = static fn (string $ar, string $en): array => ['ar' => $ar, 'en' => $en];
$media = static fn (string $path): string => 'media/source/'.$path;

return [
    'content' => [
        'brand' => [
            'name' => $t('أمل العيسى', 'Amal Aleissa'),
            'logo' => $media('logo.webp'),
        ],
        'navigation' => [
            ['label' => $t('نتائج الأعمال', 'Results'), 'anchor' => 'work', 'order' => 1, 'visible' => true],
            ['label' => $t('المتاجر', 'Stores'), 'anchor' => 'stores', 'order' => 2, 'visible' => true],
            ['label' => $t('العملاء', 'Clients'), 'anchor' => 'clients', 'order' => 3, 'visible' => true],
            ['label' => $t('ملفات مجانية', 'Free resources'), 'anchor' => 'freebies', 'order' => 4, 'visible' => true],
            ['label' => $t('ابدأ مشروعك', 'Start a project'), 'anchor' => 'contact', 'order' => 5, 'visible' => true],
        ],
        'hero' => [
            'eyebrow' => $t('تسويق رقمي مبني على نتائج حقيقية', 'Digital marketing built on real results'),
            'title' => $t('بورتفوليو يحكي نمو العلامات بالصورة والرقم', 'A portfolio that tells brand growth through visuals and numbers'),
            'description' => $t(
                'هنا ستجد أعمال أمل العيسى: حملات إعلانية، متاجر إلكترونية، إدارة سوشال ميديا، آراء عملاء، تسجيلات صوتية، شعارات، وملفات مجانية.',
                'Explore Amal Aleissa’s work across paid campaigns, e-commerce, social media, client stories, audio testimonials, brand identities, and practical resources.'
            ),
            'primary_button' => $t('مشاهدة الأعمال', 'View the work'),
            'secondary_button' => $t('الملفات المجانية', 'Free resources'),
            'feature_image' => $media('نتائج الحملات الاعلانية/حملة الورد.png'),
            'secondary_image' => $media('انشاء متاجر الكترونية/متجر ميك اب.png'),
            'words' => ['ROAS', 'تحليل بيانات', 'Google Ads', 'سوشال ميديا', 'نمو المبيعات', 'Meta Ads', 'Content Strategy', 'Leads', 'حملات إعلانية', 'A/B Testing', 'Retargeting', 'Conversion Rate', 'SEO', 'هوية بصرية'],
        ],
        'platforms' => [
            ['name' => 'Google Ads', 'url' => 'https://ads.google.com/', 'icon' => 'fa-brands fa-google', 'order' => 1, 'visible' => true],
            ['name' => 'Salla', 'url' => 'https://salla.sa/', 'icon' => 'fa-solid fa-bag-shopping', 'order' => 2, 'visible' => true],
            ['name' => 'Google Analytics', 'url' => 'https://analytics.google.com/', 'icon' => 'fa-solid fa-chart-column', 'order' => 3, 'visible' => true],
            ['name' => 'Meta', 'url' => 'https://www.facebook.com/business/', 'icon' => 'fa-brands fa-meta', 'order' => 4, 'visible' => true],
            ['name' => 'TikTok', 'url' => 'https://www.tiktok.com/business/', 'icon' => 'fa-brands fa-tiktok', 'order' => 5, 'visible' => true],
            ['name' => 'Snapchat', 'url' => 'https://forbusiness.snapchat.com/', 'icon' => 'fa-brands fa-snapchat', 'order' => 6, 'visible' => true],
        ],
        'stats' => [
            ['value' => '8', 'suffix' => '+', 'label' => $t('سنوات خبرة عملية', 'Years of experience'), 'icon' => 'fa-solid fa-award', 'order' => 1, 'visible' => true],
            ['value' => '18', 'suffix' => 'M+', 'label' => $t('ريال ميزانيات إعلانية مُدارة', 'SAR in managed ad spend'), 'icon' => 'fa-solid fa-chart-line', 'order' => 2, 'visible' => true],
            ['value' => '43', 'suffix' => '+', 'label' => $t('متجر إلكتروني', 'E-commerce stores'), 'icon' => 'fa-solid fa-store', 'order' => 3, 'visible' => true],
            ['value' => '24', 'suffix' => 'M+', 'label' => $t('مشاهدة عضوية', 'Organic views'), 'icon' => 'fa-solid fa-eye', 'order' => 4, 'visible' => true],
        ],
        'headings' => [
            'services' => ['kicker' => $t('من الفكرة إلى الأثر', 'From idea to impact'), 'title' => $t('خدمات واضحة، ومخرجات قابلة للعرض', 'Clear services, presentation-ready outcomes'), 'intro' => $t('كل خدمة مدعومة بنماذج فعلية من الأعمال تظهر جودة التنفيذ وثقة العملاء.', 'Every service is supported by real work that demonstrates execution quality and client confidence.'), 'visible' => true],
            'work' => ['kicker' => $t('نتائج الأعمال', 'Business results'), 'title' => $t('مشاريع حققت نمواً حقيقياً', 'Projects that achieved real growth'), 'intro' => $t('أبرز الحملات والمشاريع مع أرقام النتائج الفعلية ونماذج موثقة من التنفيذ.', 'Selected campaigns and projects with real results and documented execution.'), 'visible' => true],
            'stores' => ['kicker' => $t('إنشاء متاجر إلكترونية', 'E-commerce builds'), 'title' => $t('واجهات متجرية جاهزة للإقناع', 'Storefronts designed to persuade'), 'intro' => $t('نماذج تركز على وضوح الهوية وتجربة الشراء وطريقة عرض المنتجات.', 'Store experiences focused on identity, purchase flow, and product presentation.'), 'visible' => true],
            'social' => ['kicker' => $t('سوشال ميديا', 'Social media'), 'title' => $t('محتوى يعكس هوية العلامة ويعزز حضورها الرقمي', 'Content that reflects the brand and grows its digital presence'), 'intro' => $t('محتوى احترافي يعزز التواصل مع الجمهور ويدعم الأهداف التسويقية.', 'Professional content that strengthens audience relationships and marketing goals.'), 'visible' => true],
            'clients' => ['kicker' => $t('آراء العملاء والصوتيات', 'Client testimonials'), 'title' => $t('ثقة موثقة بالصورة والصوت', 'Trust documented in image and sound'), 'intro' => $t('لقطات آراء العملاء بجانب تسجيلات صوتية مباشرة.', 'Client messages and authentic audio testimonials.'), 'visible' => true],
            'logos' => ['kicker' => $t('شعارات العملاء', 'Client logos'), 'title' => $t('علامات متنوعة في قطاعات مختلفة', 'Distinct brands across diverse sectors'), 'intro' => $t('نماذج شعارات وهويات بصرية من العطور إلى الفعاليات والخدمات.', 'Logos and identities spanning fragrance, events, commerce, and services.'), 'visible' => true],
            'freebies' => ['kicker' => $t('ملفات مجانية', 'Free resources'), 'title' => $t('موارد قابلة للتحميل مباشرة', 'Practical resources ready to use'), 'intro' => $t('قوالب عملية لتنظيم المحتوى وتحليل الإعلانات.', 'Practical templates for content planning and advertising analysis.'), 'visible' => true],
        ],
        'services' => [
            ['title' => $t('الحملات الإعلانية', 'Paid campaigns'), 'description' => $t('عرض نتائج الحملات حسب المنصة مع لقطات واضحة وسهلة القراءة.', 'Campaign outcomes by platform with clear, readable evidence.'), 'icon' => 'fa-solid fa-bullhorn', 'order' => 1, 'visible' => true],
            ['title' => $t('إنشاء المتاجر', 'E-commerce stores'), 'description' => $t('نماذج متاجر إلكترونية بصور كبيرة ومعلومات مختصرة.', 'E-commerce showcases with strong visuals and concise context.'), 'icon' => 'fa-solid fa-store', 'order' => 2, 'visible' => true],
            ['title' => $t('سوشال ميديا', 'Social media'), 'description' => $t('تصاميم محتوى لحسابات مختلفة في شبكة مرتبة.', 'Organized social content created for a range of brands.'), 'icon' => 'fa-solid fa-hashtag', 'order' => 3, 'visible' => true],
        ],
        'cases' => [
            [
                'title' => $t('Sleep Nice | شركة سليب نايس', 'Sleep Nice'), 'subtitle' => $t('شركة أثاث ومفروشات · أكثر من 15 فرعاً داخل وخارج المملكة', 'Furniture company with more than 15 branches'), 'badge' => 'Head of Marketing',
                'metrics' => [['value' => '37.7M+', 'label' => 'Impressions'], ['value' => '375K+', 'label' => 'Swipe Ups'], ['value' => '932K+', 'label' => 'Clicks'], ['value' => '15+', 'label' => $t('الفروع', 'Branches')]],
                'tags' => ['Google Ads', 'Snapchat Ads', 'Influencer Marketing', 'SEO', 'Social Media'],
                'images' => [$media('نتائج الحملات الاعلانية/Sleepnice(1).png'), $media('نتائج الحملات الاعلانية/سناب شات.png'), $media('سوشال ميديا/Sleepnice.png')], 'order' => 1, 'visible' => true,
            ],
            [
                'title' => $t('دهليز للكتب', 'Dahleez Books'), 'subtitle' => $t('متجر إلكتروني متخصص في بيع الكتب', 'An online bookstore'), 'badge' => 'Performance Marketing',
                'metrics' => [['value' => '+747%', 'label' => $t('نمو بالمبيعات', 'Sales growth')], ['value' => '252K+', 'label' => $t('ريال مبيعات', 'SAR sales')], ['value' => '256', 'label' => $t('طلب', 'Orders')], ['value' => '675 ر', 'label' => $t('متوسط الطلب', 'Average order')]],
                'tags' => ['TikTok Ads', 'Meta Ads', 'Performance Marketing', 'Data Analysis'],
                'images' => [$media('نتائج الحملات الاعلانية/حملات دهليز.png'), $media('نتائج الحملات الاعلانية/دهليز قبل.png'), $media('نتائج الحملات الاعلانية/دهليز بعد.png')], 'order' => 2, 'visible' => true,
            ],
            [
                'title' => $t('هديتي للهدايا والورد', 'Hadiyati Gifts & Flowers'), 'subtitle' => $t('متجر إلكتروني متخصص في الهدايا والورد', 'An online gifts and flowers store'), 'badge' => 'E-commerce Growth',
                'metrics' => [['value' => '+350%', 'label' => $t('نمو بالمبيعات', 'Sales growth')], ['value' => '110K+', 'label' => $t('ريال مبيعات', 'SAR sales')], ['value' => '277', 'label' => $t('طلب', 'Orders')], ['value' => '394 ر', 'label' => $t('متوسط الطلب', 'Average order')]],
                'tags' => ['TikTok Ads', 'Google Ads', 'E-commerce', 'Performance Analysis'],
                'images' => [$media('نتائج الحملات الاعلانية/حملة الورد.png'), $media('نتائج الحملات الاعلانية/الورد قبل.png'), $media('نتائج الحملات الاعلانية/الورد بعد.png')], 'order' => 3, 'visible' => true,
            ],
            [
                'title' => $t('Nova by Riyadh Safari', 'Nova by Riyadh Safari'), 'subtitle' => $t('مسابقة عالمية للفنون والنحت · استهداف متعدد الدول', 'A global art and sculpture competition'), 'badge' => 'Global Campaign',
                'metrics' => [['value' => 'Multi', 'label' => 'Country Targeting'], ['value' => 'Global', 'label' => 'Art Competition']], 'tags' => ['Global Campaign Management', 'Audience Targeting', 'Meta Ads'],
                'images' => [$media('المشاريع/nova-riyadh-safari-1.jpg'), $media('المشاريع/nova-riyadh-safari-2.jpg'), $media('المشاريع/nova-riyadh-safari-3.jpg')], 'order' => 4, 'visible' => true,
            ],
            [
                'title' => $t('SureFanni App | تطبيق شورفني', 'SureFanni App'), 'subtitle' => $t('تطبيق خدمي وصل إلى المركز الأول خلال أسبوع', 'A service app that reached first place in one week'), 'badge' => 'Growth Strategy',
                'metrics' => [['value' => '#1', 'label' => 'Top App Store Ranking'], ['value' => $t('أسبوع', 'One week'), 'label' => $t('للوصول إلى القمة', 'To reach the top')]], 'tags' => ['Growth Strategy', 'Ad Campaign Management', 'B2B & B2C', 'Creative Direction'],
                'images' => [$media('نتائج الحملات الاعلانية/تطبيق شورفني.jpg')], 'order' => 5, 'visible' => true,
            ],
        ],
        'stores' => [
            ['title' => $t('متجر ميك أب', 'Makeup Store'), 'category' => $t('واجهة متجر', 'Storefront'), 'description' => $t('واجهة مرتبة تبرز المنتجات وتقدم تجربة شراء واضحة.', 'A polished storefront that puts products first and keeps purchasing clear.'), 'url_label' => 'amal.showcase/store/makeup', 'chips' => ['Clear UI', 'Product focus', 'Mobile ready'], 'image' => $media('انشاء متاجر الكترونية/متجر ميك اب.png'), 'order' => 1, 'visible' => true],
            ['title' => $t('متجر متاع', 'Mata Store'), 'category' => $t('متجر إلكتروني', 'E-commerce'), 'description' => $t('تصميم هادئ ومنظم يسهل المقارنة ويدعم قرار الشراء.', 'A calm, organized experience that supports comparison and purchase decisions.'), 'url_label' => 'amal.showcase/store/mata', 'chips' => ['Clear browsing', 'Organized products'], 'image' => $media('انشاء متاجر الكترونية/متجر متاع.png'), 'order' => 2, 'visible' => true],
            ['title' => $t('عالطاير', 'Al Tayer'), 'category' => $t('واجهة متجر', 'Storefront'), 'description' => $t('واجهة عملية تقدم تجربة سريعة ومناسبة للجوال.', 'A practical, fast, mobile-first shopping experience.'), 'url_label' => 'amal.showcase/store/altayer', 'chips' => ['Fast access', 'Mobile first'], 'image' => $media('انشاء متاجر الكترونية/متجر عالطاير.png'), 'order' => 3, 'visible' => true],
            ['title' => $t('سمار', 'Smar'), 'category' => $t('تصميم متجر', 'Store design'), 'description' => $t('عرض بصري متوازن مع حضور واضح للهوية.', 'A balanced visual presentation with a strong brand presence.'), 'url_label' => 'amal.showcase/store/smar', 'chips' => ['Brand identity', 'Product display'], 'image' => $media('انشاء متاجر الكترونية/سمار.png'), 'order' => 4, 'visible' => true],
            ['title' => 'Folaz', 'category' => $t('واجهة متجر', 'Storefront'), 'description' => $t('واجهة واسعة تناسب العلامات التي تحتاج عرضاً بصرياً غنياً.', 'A spacious storefront for brands that need rich visual storytelling.'), 'url_label' => 'amal.showcase/store/folaz', 'chips' => ['Visual focus', 'Shopping experience'], 'image' => $media('انشاء متاجر الكترونية/folaz.JPEG'), 'order' => 5, 'visible' => true],
        ],
        'social' => array_map(static fn ($row) => ['image' => $media($row[0]), 'alt' => $t($row[1], $row[2]), 'order' => $row[3], 'visible' => true], [
            ['سوشال ميديا/Petcure.png', 'تصميم سوشال ميديا لعلامة Petcure', 'Social media design for Petcure', 1], ['سوشال ميديا/Diggipacks.png', 'تصميم لعلامة Diggipacks', 'Social media design for Diggipacks', 2], ['سوشال ميديا/Sleepnice.png', 'تصميم لعلامة Sleepnice', 'Social media design for Sleepnice', 3], ['سوشال ميديا/كيد فيرست سوشال ميديا.png', 'تصميم لعلامة كيد فيرست', 'Social media design for Kid First', 4], ['سوشال ميديا/نوفا ايفنت سوشال ميديا.png', 'تصميم لنوفا إيفنت', 'Social media design for Nova Event', 5], ['سوشال ميديا/انشاء متجر NEE Makeup Milano.png', 'تصميم NEE Makeup Milano', 'Design for NEE Makeup Milano', 6], ['سوشال ميديا/1سوشال ميديا.png', 'نموذج سوشال ميديا 1', 'Social media sample 1', 7], ['سوشال ميديا/2سوشال ميديا.png', 'نموذج سوشال ميديا 2', 'Social media sample 2', 8], ['سوشال ميديا/3سوشال ميديا.png', 'نموذج سوشال ميديا 3', 'Social media sample 3', 9],
        ]),
        'testimonials' => array_map(static fn ($file, $i) => ['image' => $media('اراء العملاء/'.$file), 'alt' => $t('رأي عميل', 'Client testimonial'), 'order' => $i + 1, 'visible' => true], ['IMG_9683.PNG', 'IMG_1884.PNG', 'IMG_1883.PNG', 'IMG_1688.PNG', 'IMG_1667.PNG', 'IMG_0970.PNG'], range(0, 5)),
        'audio' => array_map(static fn ($file, $i) => ['title' => $t('تسجيل عميل '.str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT), 'Client recording '.str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)), 'file' => $media('صوتيات/'.$file), 'order' => $i + 1, 'visible' => true], ['AUDIO-2026-05-28-21-41-08.m4a', 'AUDIO-2026-05-05-05-43-16.m4a', 'AUDIO-2026-04-30-13-37-50.m4a', 'AUDIO-2026-04-20-20-31-37.m4a', 'AUDIO-2026-03-22-00-03-50.m4a'], range(0, 4)),
        'logos' => array_map(static fn ($file, $i) => ['image' => $media('لوقو العملاء/'.$file), 'alt' => $t('شعار عميل', 'Client logo'), 'order' => $i + 1, 'visible' => true], ['dail.png', '2000x2000.png', '1080 x 1920.png', '1.png', 'تصميم بدون عنوان.png', 'الشرهان للعود .jpeg', 'WhatsApp Image 2026-04-29 at 8.47.33 PM.jpeg', 'Sure-Fanni-Logo-side-1 (1).png', 'Profile.png', 'nee.png', 'final logo-3.png', 'final logo-01.jpeg', 'صفوة الجوف .png', 'سليب نايس.jpeg', 'سكر ولبن.jpeg', 'زائر.svg', 'مرايا.webp', 'كيد فيرست.jpeg', 'عشاق العطور.jpeg', 'نعناع.png', 'هدايا جدة.avif'], range(0, 20)),
        'freebies' => [
            ['key' => 'social-media', 'title' => $t('ملف Social Media', 'Social Media Planner'), 'description' => $t('قالب لتنظيم محتوى وإدارة حسابات السوشال ميديا.', 'A template for organizing content and managing social accounts.'), 'file' => 'freebies/social media.xlsx', 'icon' => 'fa-solid fa-table-cells-large', 'order' => 1, 'visible' => true],
            ['key' => 'google-ads', 'title' => $t('نموذج Google Ads', 'Google Ads Analysis'), 'description' => $t('نموذج عملي لتحليل بيانات الحملات الإعلانية.', 'A practical template for analyzing campaign data.'), 'file' => 'freebies/Google Ads نموذج التحليل والبيانات.xlsx', 'icon' => 'fa-solid fa-chart-simple', 'order' => 2, 'visible' => true],
        ],
        'newsletter' => [
            'title' => $t('اشترك بالنشرة البريدية', 'Join the newsletter'),
            'description' => $t('أدخل بريدك وسنرسل رابط تحقق آمن للوصول إلى الملفات المجانية.', 'Enter your email and we will send a secure verification link for the free resources.'),
            'button' => $t('إرسال رابط التحقق', 'Send verification link'),
        ],
        'contact' => [
            'kicker' => $t('ابدأ مشروعك', 'Start your project'), 'title' => $t('خلينا نحول الداتا إلى قرار نمو واضح', 'Let’s turn data into a clear growth decision'),
            'description' => $t('اكتب احتياجك باختصار لنحوله إلى خطة محتوى أو حملة أو متجر جاهز للانطلاق.', 'Tell us what you need and we will shape it into a content plan, campaign, or store ready to launch.'),
            'button' => $t('إرسال الطلب', 'Send request'),
            'services' => [$t('حملات إعلانية', 'Paid campaigns'), $t('إنشاء متجر إلكتروني', 'E-commerce store'), $t('إدارة سوشال ميديا', 'Social media management'), $t('هوية بصرية وشعارات', 'Brand identity and logos')],
        ],
    ],
    'theme' => \App\Services\ThemeValidator::DEFAULTS,
    'seo' => [
        'title' => $t('أمل العيسى | بورتفوليو تسويق رقمي', 'Amal Aleissa | Digital Marketing Portfolio'),
        'description' => $t('بورتفوليو أمل العيسى في التسويق الرقمي والحملات والمتاجر والسوشال ميديا.', 'Amal Aleissa’s digital marketing portfolio across campaigns, e-commerce, and social media.'),
        'keywords' => $t('أمل العيسى، تسويق رقمي، حملات إعلانية، متاجر إلكترونية', 'Amal Aleissa, digital marketing, paid campaigns, e-commerce'),
        'og_title' => $t('أمل العيسى | نمو العلامات بالصورة والرقم', 'Amal Aleissa | Brand growth in visuals and numbers'),
        'og_description' => $t('نتائج حقيقية وأعمال موثقة في التسويق الرقمي.', 'Real results and documented digital marketing work.'),
        'og_image' => $media('logo.webp'),
        'indexable' => true,
        'schema' => ['job_title' => $t('خبيرة تسويق رقمي', 'Digital Marketing Specialist'), 'service_area' => 'Saudi Arabia'],
    ],
];
