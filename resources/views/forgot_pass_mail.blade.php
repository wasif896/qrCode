<!DOCTYPE html>
<html>
<head>
    <title>Password Recovery</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f7;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border: 1px solid #eaeaea;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eaeaea;
        }
        .header h1 {
            color: #333333;
            font-size: 24px;
            margin: 0;
        }
        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #4caf50;
            text-align: center;
            margin: 20px 0;
        }
        .message {
            font-size: 16px;
            line-height: 1.6;
            color: #555555;
            text-align: center;
        }
        .footer {
            text-align: center;
            font-size: 14px;
            color: #999999;
            padding-top: 20px;
            border-top: 1px solid #eaeaea;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Password Recovery</h1>
        </div>
        <div class="message">
            <p>Hello,</p>
            <p>You requested a password recovery code. Use the OTP below to reset your password:</p>
        </div>
        <div class="otp-code">{{ $otp }}</div>
        <div class="message">
            <p>Please enter this code on the password recovery page. The OTP will expire shortly, so be sure to use it soon.</p>
            <p>If you didn’t request a password reset, please ignore this message.</p>
        </div>
        <div class="footer">
            <p>Thank you,</p>
            <p><strong>{{ $companyName }}</strong></p>
        </div>
    </div>
</body>
</html>

