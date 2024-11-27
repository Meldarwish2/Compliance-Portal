<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AuditorActionNotification extends Notification
{
    protected $action;
    protected $statement;
    protected $comment;

    public function __construct($action, $statement, $comment = null)
    {
        $this->action = $action;
        $this->statement = $statement;
        $this->comment = $comment;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $actionText = '';
        switch ($this->action) {
            case 'approved':
                $actionText = 'approved';
                break;
            case 'rejected':
                $actionText = 'rejected';
                break;
            case 'commented':
                $actionText = 'added a comment';
                break;
        }

        return (new MailMessage)
            ->subject("Your statement has been $actionText")
            ->line("The auditor has $actionText your statement: {$this->statement->name}.")
            ->line("Comment: {$this->comment}")
            ->action('View Statement', url("/statements/{$this->statement->id}"))
            ->line('Thank you for using our application!');
    }
}
