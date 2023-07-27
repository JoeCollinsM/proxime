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

class OrderUpdated extends Notification
{
    use Queueable;

    public $order;
    public $status;

    /**
     * Create a new notification instance.
     *
     * @param Order $order
     * @param $status
     */
    public function __construct(Order $order, $status)
    {
        $this->order = $order;
        $this->status = $status;
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
        if (config('proxime.fcm_notification') && $notifiable->push_notification == 1) {
            $ch[] = FcmChannel::class;
        }
        return $ch;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $params = $this->order->getInvoiceParams();
        $params['status_string'] = $this->status;
        return (new TemplateBuilder)->fetch('order_updated')->parse($params)->get();
    }

    /**
     * Get the sms representation of the notification.
     *
     * @param mixed $notifiable
     * @return array|MailMessage|\NotificationChannels\Fcm\FcmMessage
     */
    public function toItechSms($notifiable)
    {
        $params = $this->order->getInvoiceParams();
        $params['status_string'] = $this->status;
        return (new TemplateBuilder)->fetch('order_updated', 'sms')->parse($params)->get();
    }

    /**
     * Get the sms representation of the notification.
     *
     * @param mixed $notifiable
     * @return TwilioSmsMessage
     */
    public function toTwilio($notifiable)
    {
        $params = $this->order->getInvoiceParams();
        $params['status_string'] = $this->status;
        return (new TemplateBuilder)->fetch('order_updated', 'sms')->parse($params)->toTwilio();
    }

    /**
     * Get the fcm representation of the notification.
     *
     * @param mixed $notifiable
     * @return array|MailMessage|\NotificationChannels\Fcm\FcmMessage
     */
    public function toFcm($notifiable)
    {
        $params = $this->order->getInvoiceParams();
        $params['status_string'] = $this->status;
        return (new TemplateBuilder)->fetch('order_updated', 'fcm')->parse($params)->get(['order_id' => $this->order->id]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $params = $this->order->getInvoiceParams();
        $params['status_string'] = $this->status;
        return (new TemplateBuilder)->fetch('order_updated', 'fcm')->parse($params)->toDatabase(['type' => 'order', 'id' => $this->order->id]);
    }
}
