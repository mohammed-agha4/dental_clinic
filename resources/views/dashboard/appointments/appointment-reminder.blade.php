<!DOCTYPE html>
<html>
<head>
    <title>Appointment Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .header {
            background-color: #4a90e2;
            color: white;
            padding: 10px 20px;
            text-align: center;
            border-radius: 3px 3px 0 0;
        }
        .content {
            padding: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Dental Appointment Reminder</h2>
        </div>
        <div class="content">
            <p>Dear {{ $appointment->patient->fname }} {{ $appointment->patient->lname }},</p>

            <p>This is a friendly reminder that you have a dental appointment scheduled for tomorrow, <strong>{{ $appointment->appointment_date->format('l, F j, Y') }}</strong> at <strong>{{ $appointment->appointment_date->format('h:i A') }}</strong>.</p>

            <p><strong>Details:</strong></p>
            <ul>
                <li>Service: {{ $appointment->service->name }}</li>
                <li>Duration: {{ $appointment->duration }} minutes</li>
                @if($appointment->notes)
                <li>Notes: {{ $appointment->notes }}</li>
                @endif
            </ul>

            <p>If you need to reschedule or have any questions, please call us at [CLINIC_PHONE_NUMBER] as soon as possible.</p>

            <p>Thank you for choosing our dental clinic.</p>

            <p>Regards,<br>
            The Dental Clinic Team</p>
        </div>
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
