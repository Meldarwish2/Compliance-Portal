<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Comment;

class CommentAddedNotification extends Notification
{
    use Queueable;

    public $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('New Comment Notification')
            ->line("A new comment has been added by {$this->comment->role}.")
            ->line("Comment: {$this->comment->content}")
            ->action('View Statement', url("/statements/{$this->comment->statement_id}"))
            ->line('Thank you for using our application!');
    }
}
