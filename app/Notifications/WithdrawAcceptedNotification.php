<?php

namespace App\Notifications;

use App\Models\Currency;
use App\Helpers\TemplateBuilder;
use App\Models\Order;
use App\Models\Withdraw;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class WithdrawAcceptedNotification extends Notification
{
    use Queueable;
    public $withdraw;

    /**
     * Create a new notification instance.
     *
     * @param Withdraw $withdraw
     */
    public function __construct(Withdraw $withdraw)
    {
        $this->withdraw = $withdraw;
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
        if (config('proxime.fcm_notification') && $notifiable->push_notification == 1) {
            $ch[] = FcmChannel::class;
        }
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
        $data = $this->withdraw->toArray();
        $data['user'] = $this->withdraw->user->toArray();
        $data['method'] = $this->withdraw->method->toArray();
        $data['currency'] = Currency::getDefaultCurrency()->toArray();
        return (new TemplateBuilder)->fetch('withdraw_accepted')->parse($data)->get();
    }

    /**
     * Get the sms representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array|MailMessage|\NotificationChannels\Fcm\FcmMessage
     */
    public function toItechSms($notifiable)
    {
        $data = $this->withdraw->toArray();
        $data['user'] = $this->withdraw->user->toArray();
        $data['method'] = $this->withdraw->method->toArray();
        $data['currency'] = Currency::getDefaultCurrency()->toArray();
        return (new TemplateBuilder)->fetch('withdraw_accepted', 'sms')->parse($data)->get();
    }

    /**
     * Get the sms representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return TwilioSmsMessage
     */
    public function toTwilio($notifiable)
    {
        $data = $this->withdraw->toArray();
        $data['user'] = $this->withdraw->user->toArray();
        $data['method'] = $this->withdraw->method->toArray();
        $data['currency'] = Currency::getDefaultCurrency()->toArray();
        return (new TemplateBuilder)->fetch('withdraw_accepted', 'sms')->parse($data)->toTwilio();
    }

    /**
     * Get the fcm representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array|MailMessage|\NotificationChannels\Fcm\FcmMessage
     */
    public function toFcm($notifiable)
    {
        $data = $this->withdraw->toArray();
        $data['user'] = $this->withdraw->user->toArray();
        $data['method'] = $this->withdraw->method->toArray();
        $data['currency'] = Currency::getDefaultCurrency()->toArray();
        return (new TemplateBuilder)->fetch('withdraw_accepted', 'fcm')->parse($data)->get(['withdraw_id' => $this->withdraw->id]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $data = $this->withdraw->toArray();
        $data['user'] = $this->withdraw->user->toArray();
        $data['method'] = $this->withdraw->method->toArray();
        $data['currency'] = Currency::getDefaultCurrency()->toArray();
        return (new TemplateBuilder)->fetch('withdraw_accepted', 'fcm')->parse($data)->toDatabase(['type' => 'withdraw', 'id' => $this->withdraw->id]);
    }
}
