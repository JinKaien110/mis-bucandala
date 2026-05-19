<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hearing Scheduled</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #1e40af; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .details { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; }
        .detail-label { font-weight: bold; color: #666; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Barangay Bucandala 1</h2>
            <p>Hearing Notice</p>
        </div>
        
        <div class="content">
            <p>Dear <strong>{{ $recipientName }}</strong>,</p>
            
            <p>You are hereby summoned to appear for a hearing regarding Case No. <strong>{{ $hearing->case->case_no }}</strong>.</p>
            
            <div class="details">
                <div class="detail-row">
                    <span class="detail-label">Hearing Date:</span>
                    <span>{{ $hearing->scheduled_at->format('F d, Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Time:</span>
                    <span>{{ $hearing->scheduled_at->format('h:i A') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Location:</span>
                    <span>{{ $hearing->location }}</span>
                </div>
                @if($hearing->notes)
                <div class="detail-row">
                    <span class="detail-label">Notes:</span>
                    <span>{{ $hearing->notes }}</span>
                </div>
                @endif
            </div>
            
            <p><strong>Please be advised that your presence is required.</strong> Failure to appear may result in further legal action.</p>
            
            <p>If you have any questions, please contact the Barangay Hall.</p>
        </div>
        
        <div class="footer">
            <p>This is an automated message from Barangay Bucandala 1 Management Information System.</p>
            <p>&copy; {{ date('Y') }} Barangay Bucandala 1. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
