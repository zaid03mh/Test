<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL; // Import URL facade
use Illuminate\Support\Facades\Route; // Import Route facade

class LeaveRequestSubmitted extends Notification
{
    use Queueable;

    protected $user;
    protected $leaveRequest;

    public function __construct($user, $leaveRequest)
    {
        $this->user = $user;
        $this->leaveRequest = $leaveRequest;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "une nouvelle demande soumise de la part de {$this->user->name}",
            'url' => route('admin.leave-requests.show', ['leaveRequest' => $this->leaveRequest->id]) // Use route helper
        ];
    }
}
