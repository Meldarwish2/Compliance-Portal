<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ClientActionNotification extends Notification
{
    protected $action;
    protected $client;
    protected $comment;
    protected $evidence;

    public function __construct($action, $client, $comment = null, $evidence = null)
    {
        $this->action = $action;
        $this->client = $client;
        $this->comment = $comment;
        $this->evidence = $evidence;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $actionText = '';
        switch ($this->action) {
            case 'assigned':
                $actionText = 'assigned to';
                break;
            case 'revoked':
                $actionText = 'revoked from';
                break;
            case 'uploaded_evidence':
                $actionText = 'uploaded evidence';
                break;
        }

        return (new MailMessage)
            ->subject("You have been $actionText a project or statement")
            ->line("You have been $actionText the project: {$this->client->name}.")
            ->line($this->evidence ? "Evidence uploaded: $this->evidence" : '')
            ->action('View Client Profile', url("/clients/{$this->client->id}"))
            ->line('Thank you for using our application!');
    }
}

