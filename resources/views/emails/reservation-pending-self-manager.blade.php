<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('filament.emails.reservation_pending_self_manager.subject') }}</title>
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
            background: linear-gradient(135deg, #f59e0b 0%, #3b82f6 100%);
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
        .info-box {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
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
        .action-reminder {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
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
        <h1>{{ __('filament.emails.reservation_pending_self_manager.title') }}</h1>
    </div>
    
    <div class="content">
        <p>{{ __('filament.emails.reservation_pending_self_manager.greeting', ['name' => $reservation->user->name]) }}</p>
        
        <div class="info-box">
            <strong>ℹ️ {{ __('filament.emails.reservation_pending_self_manager.info') }}</strong>
        </div>
        
        <p>{{ __('filament.emails.reservation_pending_self_manager.message') }}</p>
        
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
        
        <div class="action-reminder">
            <strong>⚠️ {{ __('filament.emails.reservation_pending_self_manager.action_required') }}</strong>
            <p style="margin: 10px 0 0 0;">{{ __('filament.emails.reservation_pending_self_manager.next_steps') }}</p>
        </div>
    </div>
    
    <div class="footer">
        <p>{{ __('filament.emails.footer') }}</p>
    </div>
</body>
</html>
