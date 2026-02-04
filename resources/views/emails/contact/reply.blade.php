<!DOCTYPE html>
<html>
<head>
    <title>Reply to your contact message</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2>Hello {{ $contactMessage->name }},</h2>
        
        <p>Thank you for contacting us. We have received your message regarding "<strong>{{ $contactMessage->subject }}</strong>".</p>
        
        <div style="background-color: #f8f9fa; border-left: 4px solid #e3342f; padding: 15px; margin: 20px 0;">
            <strong>Your Message:</strong><br>
            {{ $contactMessage->message }}
        </div>

        <p><strong>Our Reply:</strong></p>
        <div style="background-color: #e9ecef; padding: 15px; border-radius: 5px;">
            {{ $contactMessage->reply }}
        </div>

        <p style="margin-top: 30px;">
            Best regards,<br>
            {{ config('app.name') }} Team
        </p>
    </div>
</body>
</html>
