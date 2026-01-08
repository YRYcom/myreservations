<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendReservationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails for reservations starting tomorrow';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = now()->addDay()->startOfDay();
        $dayAfterTomorrow = $tomorrow->copy()->addDay();

        $reservationsStartingTomorrow = \App\Models\Reservation::query()
            ->where('status', \App\Enums\ReservationStatus::Accepte)
            ->whereNull('reminder_sent_at')
            ->whereBetween('date_start', [$tomorrow, $dayAfterTomorrow])
            ->with(['user', 'bien'])
            ->get();

        $count = 0;

        foreach ($reservationsStartingTomorrow as $reservation) {
            try {
                \Illuminate\Support\Facades\Mail::to($reservation->user->email)
                    ->send(new \App\Mail\ReservationReminderNotification($reservation));

                $reservation->update(['reminder_sent_at' => now()]);

                $count++;
                $this->info("Reminder sent to {$reservation->user->email} for reservation #{$reservation->id}");
            } catch (\Exception $e) {
                $this->error("Failed to send reminder for reservation #{$reservation->id}: {$e->getMessage()}");
            }
        }

        $this->info("Total reminders sent: {$count}");

        return Command::SUCCESS;
    }
}
