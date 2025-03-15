<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CitaCreada extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $cita;

    public function __construct($cita)
    {
        $this->cita = $cita;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Cita Creada')
                    ->greeting('Hola ' . $notifiable->name)
                    ->line('Has creado una nueva cita.')
                    ->line('Descripción: ' . $this->cita->descripcion)
                    ->line('Fecha: ' . $this->cita->fecha)
                    ->line('Hora: ' . $this->cita->hora)
                    ->action('Ver Cita', url('/citas/' . $this->cita->id))
                    ->line('Gracias por usar nuestra aplicación!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        return [
            'cita_id' => $this->cita->id,
            'descripcion' => $this->cita->descripcion,
            'fecha' => $this->cita->fecha,
            'hora' => $this->cita->hora,
        ];
    }
}
