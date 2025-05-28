<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PendingRegistrationNotification extends Notification
{
  use Queueable;

  public $userData;

  public function __construct($userData)
  {
    $this->userData = $userData;
  }

  public function via($notifiable)
  {
    return ['mail', 'database'];
  }

  public function toMail($notifiable)
  {
    $message = $notifiable->role === 'superadmin'
      ? 'Um novo registo de estudante está pendente de aprovação.'
      : 'O seu registo está pendente de aprovação pelo administrador.';
    return (new MailMessage)
      ->subject('Registo Pendente')
      ->line($message)
      ->action('Ver Perfil', url('/admin/users/' . ($this->userData->id ?? '')));
  }

  public function toArray($notifiable)
  {
    return [
      'user_email' => $this->userData['email'] ?? $this->userData->email,
      'message' => 'Registo pendente para ' . ($this->userData['name'] ?? $this->userData->name),
    ];
  }
}
