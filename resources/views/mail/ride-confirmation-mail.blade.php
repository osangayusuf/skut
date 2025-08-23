<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKUT Ride Confirmation</title>
    <style>
        body { font-family: 'Inter', sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); overflow: hidden; }
        .header { background-color: #1a1a1a; padding: 20px; text-align: center; color: #fff; }
        .content { padding: 30px; line-height: 1.6; color: #333; }
        .section-title { font-size: 1.25rem; font-weight: 600; color: #2d3748; margin-top: 20px; margin-bottom: 10px; }
        .card { background-color: #f7fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 20px; }
        .card-item { margin-bottom: 10px; }
        .highlight { font-weight: 600; color: #1a202c; }
        .footer { background-color: #f1f1f1; padding: 20px; text-align: center; font-size: 0.875rem; color: #718096; }
        .footer-link { color: #4299e1; text-decoration: none; font-weight: 500; }
        .button { display: inline-block; padding: 10px 20px; background-color: #000; color: #fff; text-decoration: none; border-radius: 6px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="font-size: 1.5rem; margin: 0;">Your SKUT Ride is Confirmed 🚀</h1>
        </div>

        <div class="content">
            <p>Hi {{ $customerName }},</p>
            <p>Thank you for booking with SKUT! 🎉 Your ride has been successfully confirmed. Below are your ride details:</p>

            <h2 class="section-title">🛴 Ride Details</h2>
            <div class="card">
                <div class="card-item">
                    <span class="highlight">Vehicle:</span> {{ $skutType }}
                </div>
                <div class="card-item">
                    <span class="highlight">Duration:</span> {{ $duration == 1 ? $duration . ' hour' : $duration . ' hours' }}
                </div>
                <div class="card-item">
                    <span class="highlight">Booking Date & Time:</span> {{ $bookingDate }} – {{ $bookingTime }}
                </div>
                <div class="card-item">
                    <span class="highlight">Price Paid:</span> CAD ${{ $amount }}
                </div>
            </div>

            <h2 class="section-title">📍 Pickup Location</h2>
            <div class="card">
                <p>21 Woodmount Crescent, Ottawa, ON K2E 5P9</p>
            </div>

            <h2 class="section-title">What’s Next?</h2>
            <ul>
                <li>Arrive at the pickup location 5–10 minutes before your start time.</li>
                <li>Ride safely and enjoy exploring Ottawa with SKUT!</li>
            </ul>

            <h2 class="section-title">⚠️ Quick Safety Reminder</h2>
            <ul>
                <li>Always wear a helmet.</li>
                <li>Follow Ottawa’s traffic and park rules.</li>
                <li>Park responsibly in designated areas.</li>
            </ul>

            <p>If you have any questions or need assistance, reach us at <a href="mailto:support@rideskut.com" class="footer-link">support@rideskut.com</a>.</p>

            <p>Thanks for choosing SKUT — Move Freely. Ride Smarter.</p>

            <p>Best,<br>The SKUT Team 🚲⚡</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} SKUT. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

