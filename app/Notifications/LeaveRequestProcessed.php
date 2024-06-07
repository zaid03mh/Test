<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LeaveRequestProcessed extends Notification
{
    use Queueable;

    protected $leaveRequest;

    public function __construct($leaveRequest)
    {
        $this->leaveRequest = $leaveRequest;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "La demande de date {$this->leaveRequest->start_date} a été {$this->leaveRequest->status}",
            'url' => route('leave-requests.show', ['leaveRequest' => $this->leaveRequest->id])
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Demande de congé traitée')
                    ->greeting('Bonjour,')
                    ->line("La demande de congé de date début  {$this->leaveRequest->start_date} a été {$this->leaveRequest->status}.")
                    ->action('Voir la demande', route('leave-requests.show', ['leaveRequest' => $this->leaveRequest->id]))
                    ->line('Merci d\'utiliser notre application!');
    }
}
