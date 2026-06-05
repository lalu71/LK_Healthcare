<!DOCTYPE html>
<html>
<head>
    <title>New Contact Message</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>New Contact Inquiry</h2>
    <p>You have received a new contact message from the LK Healthcare website.</p>
    
    <table style="width: 100%; border-collapse: collapse; margin-top: 15px; text-align: left;">
        <tr>
            <td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold; width: 100px;">Name:</td>
            <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $contactData['name'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Email:</td>
            <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $contactData['email'] }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Phone:</td>
            <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $contactData['phone'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border-bottom: 1px solid #ddd; font-weight: bold;">Subject:</td>
            <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $contactData['subject'] ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; font-weight: bold; vertical-align: top;">Message:</td>
            <td style="padding: 8px;">{!! nl2br(e($contactData['message'])) !!}</td>
        </tr>
    </table>
    
    <p style="margin-top: 20px; font-size: 0.9em; color: #777;">This email was sent automatically from your website contact form.</p>
</body>
</html>
