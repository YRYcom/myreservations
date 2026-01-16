<?php

namespace App\Enums;

enum ActivityAction: string
{
    // Email actions - Reservation related
    case EMAIL_RESERVATION_CREATED = 'email.reservation.created';
    case EMAIL_RESERVATION_APPROVED = 'email.reservation.approved';
    case EMAIL_RESERVATION_REJECTED = 'email.reservation.rejected';
    case EMAIL_RESERVATION_REMINDER = 'email.reservation.reminder';
    case EMAIL_RESERVATION_PENDING_MANAGER = 'email.reservation.pending.manager';
    case EMAIL_RESERVATION_PENDING_USER = 'email.reservation.pending.user';
    case EMAIL_RESERVATION_PENDING_SELF_MANAGER = 'email.reservation.pending.self_manager';
}
