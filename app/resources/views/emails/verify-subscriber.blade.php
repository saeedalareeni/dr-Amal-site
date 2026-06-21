<div dir="{{ $subscriber->locale === 'en' ? 'ltr' : 'rtl' }}" style="font-family:Arial,sans-serif;line-height:1.8">
    @if($subscriber->locale === 'en')
        <h2>Verify your email</h2><p>Use the button below within one hour to access the free resources.</p><p><a href="{{ $verificationUrl }}">Verify and access files</a></p>
    @else
        <h2>تأكيد البريد الإلكتروني</h2><p>اضغط الرابط خلال ساعة واحدة لتأكيد بريدك والوصول إلى الملفات المجانية.</p><p><a href="{{ $verificationUrl }}">تأكيد البريد والوصول للملفات</a></p>
    @endif
</div>
