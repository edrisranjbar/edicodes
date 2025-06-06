<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پاسخ جدید به دیدگاه شما | ادیکدز</title>
    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            direction: rtl;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .content {
            padding: 20px 0;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #888;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 15px;
            border: none;
            cursor: pointer;
        }
        .comment {
            background-color: #f5f8fa;
            border-right: 4px solid #4CAF50;
            padding: 10px 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .reply {
            background-color: #e8f5e9;
            border-right: 4px solid #2196F3;
            padding: 10px 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>پاسخ جدید به دیدگاه شما</h1>
        </div>
        
        <div class="content">
            <p>سلام {{ $parentComment->name }} عزیز،</p>
            
            <p>یک پاسخ جدید به دیدگاه شما در مطلب "{{ $post->title }}" ارسال شده است.</p>
            
            <div class="comment">
                <strong>دیدگاه شما:</strong>
                <p>{{ $parentComment->content }}</p>
            </div>
            
            <div class="reply">
                <strong>پاسخ جدید:</strong>
                <p>{{ $reply->content }}</p>
            </div>
            
            <p>
                برای مشاهده این گفتگو در سایت، روی دکمه زیر کلیک کنید:
            </p>
            
            <div style="text-align: center;">
                <a href="{{ $postUrl }}" class="btn">مشاهده پاسخ</a>
            </div>
            
            <p>با تشکر،<br>تیم ادیکدز</p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} ادیکدز - تمامی حقوق محفوظ است</p>
            <p>
                اگر مایل به دریافت ایمیل‌های بیشتر نیستید، می‌توانید 
                <a href="{{ url('/unsubscribe') }}">لغو اشتراک</a> کنید.
            </p>
        </div>
    </div>
</body>
</html> 