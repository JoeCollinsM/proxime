<?php

namespace App\Notifications;

use App\Models\Currency;
use App\Models\DeliveryMan;
use App\Helpers\TemplateBuilder;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class NewTransactionMade extends Notification
{
    use Queueable;

    /**
     * @var Transaction
     */
    public $transaction;
    public $data;

    /**
     * Create a new notification instance.
     *
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
        $this->data = $this->transaction->toArray();
        $this->data['user'] = $this->transaction->user->toArray();
        $this->data['currency'] = Currency::getDefaultCurrency()->toArray();
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
        if (config('proxime.fcm_notification') && $notifiable instanceof User && $notifiable->push_notification == 1) {
            $ch[] = FcmChannel::class;
        } elseif (config('proxime.fcm_notification') && $notifiable instanceof DeliveryMan) {
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
        return (new TemplateBuilder)->fetch('new_transaction')->parse($this->data)->get();
    }

    /**
     * Get the sms representation of the notification.
     *
     * @param mixed $notifiable
     * @return array|MailMessage|\NotificationChannels\Fcm\FcmMessage
     */
    public function toItechSms($notifiable)
    {
        return (new TemplateBuilder)->fetch('new_transaction', 'sms')->parse($this->data)->get();
    }

    /**
     * Get the sms representation of the notification.
     *
     * @param mixed $notifiable
     * @return TwilioSmsMessage
     */
    public function toTwilio($notifiable)
    {
        return (new TemplateBuilder)->fetch('new_transaction', 'sms')->parse($this->data)->toTwilio();
    }

    /**
     * Get the fcm representation of the notification.
     *
     * @param mixed $notifiable
     * @return array|MailMessage|\NotificationChannels\Fcm\FcmMessage
     */
    public function toFcm($notifiable)
    {
        return (new TemplateBuilder)->fetch('new_transaction', 'fcm')->parse($this->data)->get(['transaction_id' => $this->transaction->id]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return (new TemplateBuilder)->fetch('new_transaction', 'fcm')->parse($this->data)->toDatabase(['type' => 'transaction', 'id' => $this->transaction->id]);
    }
}
