<?php

namespace App\Helpers;

use App\Models\NotificationTemplate;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Str;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\AndroidConfig;
use NotificationChannels\Fcm\Resources\AndroidFcmOptions;
use NotificationChannels\Fcm\Resources\AndroidNotification;
use NotificationChannels\Fcm\Resources\ApnsConfig;
use NotificationChannels\Fcm\Resources\ApnsFcmOptions;
use NotificationChannels\Twilio\TwilioSmsMessage;

class TemplateBuilder
{
    private $template;
    private $title;
    private $channel;
    private $except = ['if', 'endif'];

    function fetch($name, $channel = 'email')
    {
        $this->channel = $channel;
        $data = NotificationTemplate::query()->where('name', $name)->where('channel', $channel)->first();
        if ($data) {
            $this->template = $data->content;
            $this->title = $data->title;
        } else {
            $data = config($channel . '.' . $name);
            $this->template = $data['content'];
            $this->title = $data['title'];
        }
        return $this;
    }

    function parse($data = [])
    {
        $this->template = $this->resolveMatches($this->template, $data);
        $this->title = $this->resolveMatches($this->title, $data);
        return $this;
    }

    function toEmail()
    {
        return (new MailMessage)->subject($this->title)->view('email.default', ['data' => $this->template]);
    }

    function toPdf()
    {
        $pdf = PDF::loadView('email.default', ['data' => $this->template]);
        $name = Str::slug($this->title);
        return $pdf->download($name . '.pdf');
    }

    function toView()
    {
        return view('email.default', ['data' => $this->template]);
    }

    function toSMS($signature = null)
    {
        if ($signature) {
            return '<#> ' . config('app.name') . ': ' . $this->template . ' ' . $signature;
        }
        return $this->template;
    }

    function toTwilio($signature = null)
    {
        if ($signature) {
            return '<#> ' . config('app.name') . ': ' . $this->template . ' ' . $signature;
        }
        return (new TwilioSmsMessage)->content($this->template);
    }

    function toDatabase($data = [])
    {
        return [
            'title' => $this->title,
            'message' => $this->template,
            'data' => $data
        ];
    }

    function toFCM($data = [])
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[(string)$k] = (string)$v;
            }
        }
        return FcmMessage::create()
            ->setData($data)
            ->setNotification(\NotificationChannels\Fcm\Resources\Notification::create()
                ->setTitle($this->title)
                ->setBody($this->template)
                ->setImage(config('ui.logo.small')))
            ->setAndroid(
                AndroidConfig::create()
                    ->setFcmOptions(AndroidFcmOptions::create()->setAnalyticsLabel('analytics'))
                    ->setNotification(AndroidNotification::create()->setColor('#0A0A0A'))
            )->setApns(
                ApnsConfig::create()
                    ->setFcmOptions(ApnsFcmOptions::create()->setAnalyticsLabel('analytics_ios')));
    }

    function get($data = [])
    {
        if ($this->channel == 'sms') return $this->toSMS();
        if ($this->channel == 'fcm') return $this->toFCM($data);
        return $this->toEmail();
    }

    private function resolveMatches($string, $data = [])
    {
        $except = $this->except;
        return preg_replace_callback('/\[([\w.]+)]/', function ($matches) use ($data, $except) {
            if (in_array($matches[1], $except)) return $matches[0];
            $keys = explode('.', $matches[1]);
            $replacement = '';
            if (sizeof($keys) === 1) {
                $replacement = isset($data[$keys[0]]) ? $data[$keys[0]] : null;
            } else {
                $replacement = $data;

                foreach ($keys as $key) {
                    if (!isset($replacement[$key])) return null;
                    $replacement = $replacement[$key];
                }
            }

            return $replacement;
        }, $string);
    }
}
