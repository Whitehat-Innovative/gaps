@extends('vendor.mail.html.layout')

@section('header', 'FITBASE SUBSCRIPTION EXPIRY NOTICE')

@section('content')
    <div style="font-family: 'Segoe UI', Arial, sans-serif; color: #333; background-color: #f9fafb; padding: 30px; border-radius: 12px;">

        <!-- Header -->
        <h1 style="color: #d32f2f; font-size: 24px; font-weight: 700; margin-bottom: 10px;">
            Your FitBase Subscription Expires in {{ $message['daysLeft'] }} days
        </h1>

        <p style="font-size: 15px; color: #555; line-height: 1.6;">
            Hello {{ $message['name'] }},<br><br>
            We noticed that your <strong>FitBase subscription</strong> has officially expired.  
            To avoid losing access to premium features and tools, we recommend renewing as soon as possible.
        </p>

        <!-- Details Card -->
        <div style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 20px; margin-top: 20px; margin-bottom: 25px; box-shadow: 0 2px 6px rgba(0,0,0,0.05);">
            <h3 style="font-size: 18px; color: #111827; margin-bottom: 10px;">Subscription Details</h3>

            <!-- <p style="margin: 5px 0;"><strong>User:</strong> {{ $message['name'] }}</p>
            <p style="margin: 5px 0;"><strong>Email:</strong> {{ $message['email'] }}</p>
            <p style="margin: 5px 0;"><strong>Plan:</strong> {{ $message['plan_name'] }}</p>
            <p style="margin: 5px 0;"><strong>Expired On:</strong> {{ $message['end_date'] }}</p> -->
        </div>

        <!-- Renewal Notice -->
        <div style="background: #fdecea; border-left: 4px solid #d32f2f; border-radius: 8px; padding: 20px; margin-bottom: 25px;">
            <p style="font-size: 15px; color: #444;">
                To continue enjoying FitBase services, please renew your subscription using the button below.
            </p>

            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
               style="display: inline-block; margin-top: 10px; background-color: #d32f2f; color: #fff; text-decoration: none; padding: 10px 22px; border-radius: 8px; font-weight: 600;">
                Renew Subscription
            </a>
        </div>

        <!-- Footer -->
        <p style="margin-top: 25px; font-size: 15px; color: #555;">
            If you have already renewed, kindly ignore this message.<br><br>
            Thank you,<br>
            <strong style="color: #d32f2f;">FitBase Support Team</strong>
        </p>
    </div>
@endsection
