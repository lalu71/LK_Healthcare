<!DOCTYPE html>
<html>
<head>
    <title>Reply to your inquiry</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="margin-bottom: 20px;">
        {!! nl2br(e($replyMessage)) !!}
    </div>
    
    <div style="margin-top: 30px; border-top: 1px solid #ddd; padding-top: 15px; color: #666; font-size: 0.9em;">
        <p><strong>LK Healthcare</strong><br>
        <a href="{{ config('app.url') }}">{{ config('app.url') }}</a></p>
    </div>
</body>
</html>
