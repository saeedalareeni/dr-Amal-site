<div dir="{{ $lead->locale === 'en' ? 'ltr' : 'rtl' }}" style="font-family:Arial,sans-serif;line-height:1.8">
    @if($lead->locale === 'en')
        <h2>Thank you, {{ $lead->name }}</h2><p>We received your request and will review it shortly.</p>
    @else
        <h2>شكرًا لك {{ $lead->name }}</h2><p>استلمنا تفاصيل طلبك وسنراجعها ونتواصل معك قريبًا.</p>
    @endif
</div>
