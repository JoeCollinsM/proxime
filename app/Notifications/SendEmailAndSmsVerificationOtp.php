<?php

namespace App\Notifications;

use App\Helpers\TemplateBuilder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class SendEmailAndSmsVerificationOtp extends Notification
{
    use Queueable;
    private $type;
    private $otp;
    /**
     * @var null
     */
    private $signature;

    /**
     * Create a new notification instance.
     *
     * @param $otp
     * @param string $type
     * @param null $signature
     */
    public function __construct($otp, $type = 'email', $signature = null)
    {
        $this->type = $type;
        $this->otp = $otp;
        $this->signature = $signature;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
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
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new TemplateBuilder)->fetch('verify', 'email')->parse(['name' => $notifiable->name, 'otp' => $this->otp])->get();
    }

    /**
     * Get the sms representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    public function toItechSms($notifiable)
    {
        return (new TemplateBuilder)->fetch('verify', 'sms')->parse(['name' => $notifiable->name, 'otp' => $this->otp])->toSMS($this->signature);
    }

    /**
     * Get the sms representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return TwilioSmsMessage
     */
    public function toTwilio($notifiable)
    {
        return (new TemplateBuilder)->fetch('verify', 'sms')->parse(['name' => $notifiable->name, 'otp' => $this->otp])->toTwilio($this->signature);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
