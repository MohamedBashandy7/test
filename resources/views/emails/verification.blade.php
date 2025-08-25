<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تفعيل الحساب - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .verification-code {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 5px;
            padding: 15px;
            margin: 20px 0;
            background-color: #e8f5e9;
            border-radius: 5px;
            direction: ltr;
            display: inline-block;
            min-width: 200px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>مرحباً {{ $user->name }}،</h1>
    </div>
    
    <div class="content">
        <p>شكراً لتسجيلك معنا. يرجى استخدام كود التفعيل التالي لتفعيل حسابك:</p>
        
        <div style="text-align: center;">
            <div class="verification-code">
                {{ $verificationCode }}
            </div>
        </div>
        
        <p>هذا الكود صالح لمدة 24 ساعة.</p>
        
        <p>إذا لم تكن قد قمت بطلب هذا الرمز، فيرجى تجاهل هذه الرسالة.</p>
        
        <p>مع أطيب التحيات،<br>فريق {{ config('app.name') }}</p>
    </div>
    
    <div class="footer">
        <p>© {{ date('Y') }} جميع الحقوق محفوظة</p>
    </div>
</body>
</html>
