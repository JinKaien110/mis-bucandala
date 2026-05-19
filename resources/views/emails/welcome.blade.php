<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to Barangay Bucandala 1</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; }
        .header { background: linear-gradient(135deg, #1055C9 0%, #0d47a1 100%); color: white; padding: 30px 20px; text-align: center; }
        .header h2 { margin: 0; font-size: 24px; }
        .header p { margin: 10px 0 0; opacity: 0.9; }
        .content { padding: 30px 20px; background: #f9fafb; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .greeting { font-size: 18px; font-weight: bold; color: #1f2937; margin-bottom: 15px; }
        .message { color: #4b5563; margin-bottom: 20px; line-height: 1.7; }
        .details { background: #f0f9ff; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { font-weight: 600; color: #374151; }
        .detail-value { color: #1055C9; }
        .credentials { background: #fef3c7; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #f59e0b; }
        .credentials p { margin: 5px 0; }
        .credentials strong { color: #92400e; }
        .cta { display: inline-block; background: linear-gradient(135deg, #1055C9 0%, #0d47a1 100%); color: white; padding: 14px 28px; border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 15px; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 12px; }
        .footer p { margin: 5px 0; }
        .note { font-size: 11px; color: #9ca3af; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Barangay Bucandala 1</h2>
            <p>Management Information System</p>
        </div>
        
        <div class="content">
            <div class="card">
                <p class="greeting">Hello, {{ $name }}!</p>
                
                <p class="message">Welcome to the Barangay Bucandala 1 Management Information System! Your resident registration has been successfully processed.</p>
                
                <div class="details">
                    <div class="detail-row">
                        <span class="detail-label">Registration Date:</span>
                        <span class="detail-value">{{ now()->format('F d, Y') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value">Verified Resident</span>
                    </div>
                </div>
                
                <div class="credentials">
                    <p><strong>Your Login Credentials:</strong></p>
                    <p>Email: {{ $email }}</p>
                    <p><em>Use your registered email and password to log in.</em></p>
                </div>
                
                <p class="message">Through this portal, you can:</p>
                <ul style="color: #4b5563; padding-left: 20px;">
                    <li>Request official documents</li>
                    <li>View announcements and events</li>
                    <li>Update your profile</li>
                    <li>Access resident services</li>
                </ul>
                
                <center>
                    <a href="{{ url('/resident/dashboard') }}" class="cta">Go to Dashboard</a>
                </center>
            </div>
        </div>
        
        <div class="footer">
            <p>This is an automated message from Barangay Bucandala 1 MIS.</p>
            <p>&copy; {{ date('Y') }} Barangay Bucandala 1. All rights reserved.</p>
            <p class="note">Please do not reply to this email. For assistance, visit the Barangay Hall or contact us through the MIS portal.</p>
        </div>
    </div>
</body>
</html>