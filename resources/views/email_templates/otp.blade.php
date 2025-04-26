<!DOCTYPE html>
<html>
<head>
    <title>Login OTP</title>
</head>
<body>
<div style="font-family: Helvetica,Arial,sans-serif;min-width:1000px;overflow:auto;line-height:2">
  <div style="margin:50px auto;width:70%;padding:20px 0">
    <div style="border-bottom:1px solid #eee">

        <a href="#" target="_blank">
            <img style="padding: 15px 0;width: 100%; height: 80px; object-fit: contain;"
                src="{{ asset('admin-assets/images/mrcem.png') }}" alt="logo-image">
        </a>
    </div>
    <p style="font-size:1.1em">Dear {{$data['name']}},</p>
    <p>You have requested a one-time password (OTP) for logging into your account at MRCEM Pro. Please use the following OTP to complete your login:</p>
    <h2 style="background: #00466a;margin: 0 auto;width: max-content;padding: 0 10px;color: #fff;border-radius: 4px;">{{$data['otp']}}</h2>
    <p style="font-size:0.9em;">Regards,<br />MRCEM Pro</p>
  </div>
</div>
</body>
</html>