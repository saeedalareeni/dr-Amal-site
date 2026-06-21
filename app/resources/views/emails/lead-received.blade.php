<div dir="rtl" style="font-family:Arial,sans-serif;line-height:1.8">
    <h2>طلب مشروع جديد</h2>
    <p><strong>الاسم:</strong> {{ $lead->name }}</p>
    <p><strong>البريد:</strong> {{ $lead->email }}</p>
    <p><strong>الخدمة:</strong> {{ $lead->service }}</p>
    <p><strong>موعد الاستشارة:</strong> {{ $lead->consultation_date?->format('Y-m-d') ?: 'غير محدد' }}</p>
    <p><strong>التفاصيل:</strong><br>{{ $lead->message }}</p>
    <p><a href="{{ url('/admin/leads/'.$lead->id) }}">فتح الطلب في لوحة التحكم</a></p>
</div>
