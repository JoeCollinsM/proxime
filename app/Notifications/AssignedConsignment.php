<?php

namespace App\Notifications;

use App\Models\Consignment;
use App\Helpers\TemplateBuilder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;

class AssignedConsignment extends Notification
{
    use Queueable;

    public $consignment;

    /**
     * Create a new notification instance.
     *
     * @param Consignment $consignment
     */
    public function __construct(Consignment $consignment)
    {
        $this->consignment = $consignment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $ch = ['database'];
        if (config('proxime.fcm_notification')) {
            $ch[] = FcmChannel::class;
        }
        return $ch;
    }

    /**
     * Get the fcm representation of the notification.
     *
     * @param mixed $notifiable
     * @return array|MailMessage|\NotificationChannels\Fcm\FcmMessage
     */
    public function toFcm($notifiable)
    {
        $data = [
            'name' => $notifiable->name,
            'id' => $this->consignment->id,
        ];
        return (new TemplateBuilder)->fetch('assigned_shipment', 'fcm')->parse($data)->get(['shipment_id' => $this->consignment->id]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $data = [
            'name' => $notifiable->name,
            'id' => $this->consignment->id,
        ];
        return (new TemplateBuilder)->fetch('assigned_shipment', 'fcm')->parse($data)->toDatabase(['type' => 'consignment', 'id' => $this->consignment->id]);
    }
}
