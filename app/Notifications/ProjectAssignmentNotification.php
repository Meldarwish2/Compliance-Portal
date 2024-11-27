<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ProjectAssignmentNotification extends Notification
{
    protected $project;
    protected $action;

    public function __construct($project, $action)
    {
        $this->project = $project;
        $this->action = $action; // either 'assigned' or 'revoked'
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $actionText = $this->action === 'assigned' ? 'assigned to' : 'revoked from';
        
        return (new MailMessage)
            ->subject("You have been $actionText a project")
            ->line("You have been $actionText the project: {$this->project->name}.")
            ->action('View Project', url("/projects/{$this->project->id}"))
            ->line('Thank you for using our application!');
    }
}

