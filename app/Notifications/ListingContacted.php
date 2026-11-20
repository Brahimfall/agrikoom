<?php

namespace App\Notifications;

use App\Models\ContactRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ListingContacted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ContactRequest $contact)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nouveau contact pour votre annonce AgriKoom')
            ->line($this->contact->buyer_name.' veut vous contacter pour : '.$this->contact->listing->title)
            ->line($this->contact->message ?: 'Aucun message ajoute.')
            ->action('Voir l annonce', route('listings.show', $this->contact->listing))
            ->line('Repondez rapidement pour ne pas perdre l opportunite.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'contact_id' => $this->contact->id,
            'listing_id' => $this->contact->listing_id,
            'listing_title' => $this->contact->listing->title,
            'buyer_name' => $this->contact->buyer_name,
            'buyer_phone' => $this->contact->buyer_phone,
        ];
    }
}
