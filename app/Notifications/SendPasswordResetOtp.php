<?php

namespace App\Notifications;

use App\Helpers\TemplateBuilder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class SendPasswordResetOtp extends Notification
{
    use Queueable;

    private $type;
    private $otp;

    /**
     * Create a new notification instance.
     *
     * @param $otp
     * @param string $type
     */
    public function __construct($otp, $type = 'email')
    {
        $this->type = $type;
        $this->otp = $otp;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if ($this->type == 'email') {
            if (config('proxime.email_notification')) {
                return ['mail'];
            }
        }
        if ($this->type == 'sms') {
            if (config('proxime.sms_notification')) {
                if (config('proxime.sms_via') == 'twilio') {
                    return [TwilioChannel::class];
                } else {
                    return ['itech'];
                }
            }
        }
        return [];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new TemplateBuilder)->fetch('reset_password', 'email')->parse(['name' => $notifiable->name, 'otp' => $this->otp])->get();
    }

    /**
     * Get the sms representation of the notification.
     *
     * @param mixed $notifiable
     * @return array|MailMessage|\NotificationChannels\Fcm\FcmMessage
     */
    public function toItechSms($notifiable)
    {
        return (new TemplateBuilder)->fetch('reset_password', 'sms')->parse(['name' => $notifiable->name, 'otp' => $this->otp])->get();
    }

    /**
     * Get the sms representation of the notification.
     *
     * @param mixed $notifiable
     * @return TwilioSmsMessage
     */
    public function toTwilio($notifiable)
    {
        return (new TemplateBuilder)->fetch('reset_password', 'sms')->parse(['name' => $notifiable->name, 'otp' => $this->otp])->toTwilio();
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
