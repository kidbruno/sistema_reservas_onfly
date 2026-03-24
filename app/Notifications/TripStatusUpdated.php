<?php

namespace App\Notifications;

use App\Models\Trip;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TripStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(private readonly Trip $trip)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Atualização do pedido de viagem')
            ->greeting('Olá, '.($notifiable->nome ?? 'Solicitante').'!')
            ->line('O status do seu pedido de viagem foi atualizado.')
            ->line('Destino: '.$this->trip->destino)
            ->line('Status atual: '.strtoupper($this->trip->status))
            ->line('Data de ida: '.$this->trip->data_viagem_ida?->format('d/m/Y'))
            ->line('Data de volta: '.$this->trip->data_viagem_volta?->format('d/m/Y'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'trip_id' => $this->trip->Id,
            'destino' => $this->trip->destino,
            'status'  => $this->trip->status,
        ];
    }
}