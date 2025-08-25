<x-mail::message>
# كود التحقق

عزيزي المستخدم،

لقد تلقيت طلب تسجيل في نظامنا. يرجى استخدام كود التحقق التالي لإكمال عملية التسجيل:

<div style="text-align: center; margin: 20px 0;">
    <span style="font-size: 24px; font-weight: bold; letter-spacing: 5px; background: #f4f4f4; padding: 10px 20px; border-radius: 5px;">
        {{ $code }}
    </span>
</div>

<p>هذا الكود صالح لمدة 30 دقيقة فقط.</p>
<p>إذا لم تطلب هذا الكود، فيرجى تجاهل هذه الرسالة.</p>

مع أطيب التحيات،<br>
{{ config('app.name') }}
</x-mail::message>
