<?php

namespace App\Notifications;

use App\Helpers\TemplateBuilder;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class NewOrderNotificationToShop extends Notification
{
    use Queueable;

    /**
     * @var Order
     */
    public $order;

    /**
     * Create a new notification instance.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $ch = ['database'];
        if (config('proxime.email_notification')) {
            $ch[] = 'mail';
        }
        if (config('proxime.sms_notification')) {
            if (config('proxime.sms_via') == 'twilio') {
                $ch[] = TwilioChannel::class;
            } else {
                $ch[] = 'itech';
            }
        }
//        if (config('proxime.fcm_notification') && $notifiable->push_notification == 1) {
//            $ch[] = FcmChannel::class;
//        }
        return $ch;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new TemplateBuilder)->fetch('new_order_to_shop')->parse($this->order->getInvoiceParams())->get();
    }

    /**
     * Get the sms representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array|MailMessage|\NotificationChannels\Fcm\FcmMessage
     */
    public function toItechSms($notifiable)
    {
        return (new TemplateBuilder)->fetch('new_order_to_shop', 'sms')->parse($this->order->getInvoiceParams())->get();
    }

    /**
     * Get the sms representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return TwilioSmsMessage
     */
    public function toTwilio($notifiable)
    {
        return (new TemplateBuilder)->fetch('new_order_to_shop', 'sms')->parse($this->order->getInvoiceParams())->toTwilio();
    }

    /**
     * Get the fcm representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array|MailMessage|\NotificationChannels\Fcm\FcmMessage
     */
    public function toFcm($notifiable)
    {
        return (new TemplateBuilder)->fetch('new_order_to_shop', 'fcm')->parse($this->order->getInvoiceParams())->get(['order_id' => $this->order->id]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return (new TemplateBuilder)->fetch('new_order_to_shop', 'fcm')->parse($this->order->getInvoiceParams())->toDatabase(['type' => 'order', 'id' => $this->order->id]);
    }
}
