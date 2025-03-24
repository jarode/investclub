<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Investment;

class NewInvestmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $investment;

    public function __construct(Investment $investment)
    {
        $this->investment = $investment;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nowa inwestycja w projekcie ' . $this->investment->project->name)
            ->line('Otrzymałeś nową inwestycję w projekcie ' . $this->investment->project->name)
            ->line('Inwestor: ' . $this->investment->user->name)
            ->line('Kwota: ' . number_format($this->investment->amount, 2, ',', ' ') . ' zł')
            ->line('Preferowana metoda kontaktu: ' . $this->investment->contact_preference)
            ->line('Dane kontaktowe: ' . $this->investment->contact_details)
            ->action('Zobacz szczegóły inwestycji', url('/investments/' . $this->investment->id))
            ->line('Dziękujemy za korzystanie z naszej platformy!');
    }

    public function toArray($notifiable): array
    {
        return [
            'investment_id' => $this->investment->id,
            'project_id' => $this->investment->project_id,
            'project_name' => $this->investment->project->name,
            'investor_name' => $this->investment->user->name,
            'amount' => $this->investment->amount,
            'contact_preference' => $this->investment->contact_preference,
            'contact_details' => $this->investment->contact_details,
        ];
    }
} 