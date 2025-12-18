<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('filament.emails.reservation_pending_user.subject') }}</title>
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
            background-color: #f59e0b;
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
        .reservation-details {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #f59e0b;
        }
        .detail-row {
            margin: 10px 0;
        }
        .detail-label {
            font-weight: bold;
            color: #6b7280;
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
        <h1>{{ __('filament.emails.reservation_pending_user.title') }}</h1>
    </div>
    
    <div class="content">
        <p>{{ __('filament.emails.reservation_pending_user.greeting', ['name' => $reservation->user->name]) }}</p>
        
        <p>{{ __('filament.emails.reservation_pending_user.message') }}</p>
        
        <div class="reservation-details">
            <h2 style="margin-top: 0; color: #f59e0b;">{{ __('filament.emails.reservation_details') }}</h2>
            
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
            
            @if($reservation->comment)
            <div class="detail-row">
                <span class="detail-label">{{ __('filament.resources.reservations.fields.comment') }} :</span>
                {{ $reservation->comment }}
            </div>
            @endif
        </div>
        
        <p>{{ __('filament.emails.reservation_pending_user.next_steps') }}</p>
    </div>
    
    <div class="footer">
        <p>{{ __('filament.emails.footer') }}</p>
    </div>
</body>
</html>
