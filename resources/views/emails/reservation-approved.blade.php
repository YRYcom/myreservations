<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('filament.emails.reservation_approved.subject') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #10b981;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }

        .content {
            background-color: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }

        .success-icon {
            font-size: 48px;
            text-align: center;
            margin: 20px 0;
        }

        .reservation-details {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #10b981;
        }

        .detail-row {
            margin: 10px 0;
        }

        .detail-label {
            font-weight: bold;
            color: #6b7280;
        }

        .comment-box {
            background-color: #ecfdf5;
            border-left: 4px solid #10b981;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ __('filament.emails.reservation_approved.title') }}</h1>
    </div>

    @if($reservation->bien->photo)
        <div style="text-align: center; margin: 0; padding: 0;">
            <img src="{{ $message->embed(storage_path('app/public/' . $reservation->bien->photo)) }}"
                alt="{{ $reservation->bien->name }}"
                style="width: 100%; max-width: 600px; height: 300px; object-fit: cover; display: block; margin: 0;">
        </div>
    @endif

    <div class="content">
        <div class="success-icon">✅</div>

        <p>{{ __('filament.emails.reservation_approved.greeting', ['name' => $reservation->user->name]) }}</p>

        <p><strong>{{ __('filament.emails.reservation_approved.message') }}</strong></p>

        <div class="reservation-details">
            <h2 style="margin-top: 0; color: #10b981;">{{ __('filament.emails.reservation_details') }}</h2>

            <div class="detail-row">
                <span class="detail-label">{{ __('filament.resources.reservations.fields.bien_id') }} :</span>
                {{ $reservation->bien->name }}
            </div>

            <div class="detail-row">
                <span class="detail-label">{{ __('filament.resources.reservations.fields.date_start') }} :</span>
                {{ $reservation->date_start->format('d/m/Y') }}
            </div>

            <div class="detail-row">
                <span class="detail-label">{{ __('filament.resources.reservations.fields.date_end') }} :</span>
                {{ $reservation->date_end->format('d/m/Y') }}
            </div>

            <div class="detail-row">
                <span class="detail-label">{{ __('filament.resources.reservations.fields.number_of_guests') }} :</span>
                {{ $reservation->number_of_guests }}
            </div>
        </div>

        @if($comment)
            <div class="comment-box">
                <strong>{{ __('filament.emails.manager_comment') }} :</strong>
                <p style="margin: 10px 0 0 0;">{{ $comment }}</p>
            </div>
        @endif

        <p>{{ __('filament.emails.reservation_approved.enjoy') }}</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ config('app.url') }}/home/reservations/{{ $reservation->id }}/edit"
                style="display: inline-block; padding: 12px 24px; background-color: #10b981; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">
                {{ __('filament.emails.reservation_reminder.button') }}
            </a>
        </div>
    </div>

    <div class="footer">

        <p>{{ __('filament.emails.footer') }}</p>
    </div>
</body>

</html>