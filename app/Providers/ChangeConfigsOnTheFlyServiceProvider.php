<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class ChangeConfigsOnTheFlyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            \config()->set('app.name', get_option('project_name', config('app.name')));
            \config()->set('app.debug', (get_option('debug_mode', (config('app.debug') ? 1 : 0))) == 1);
            \config()->set('app.timezone', get_option('timezone', config('app.timezone')));
            date_default_timezone_set(\config('app.timezone', 'UTC'));
            \config()->set('proxime.forgot_password_via', get_option('forgot_password_via', config('proxime.forgot_password_via')));
            \config()->set('catalog.colors', explode('|', get_option('colors', implode('|', config('catalog.colors')))));
            \config()->set('proxime.email_verification', (get_option('email_verification', (config('proxime.email_verification') ? 1 : 0)) == 1));
            \config()->set('proxime.sms_verification', (get_option('sms_verification', (config('proxime.sms_verification') ? 1 : 0)) == 1));
            \config()->set('proxime.email_notification', (get_option('email_notification', (config('proxime.email_notification') ? 1 : 0)) == 1));
            \config()->set('proxime.sms_notification', (get_option('sms_notification', (config('proxime.sms_notification') ? 1 : 0)) == 1));
            \config()->set('proxime.sms_via', get_option('sms_via', config('proxime.sms_via', 'twilio')));
            \config()->set('proxime.fcm_notification', (get_option('fcm_notification', (config('proxime.fcm_notification') ? 1 : 0)) == 1));
            \config()->set('proxime.default_user_status', (get_option('default_user_status', (config('proxime.default_user_status') ? 1 : 0)) == 1));
            \config()->set('proxime.default_vendor_status', (get_option('default_vendor_status', (config('proxime.default_vendor_status') ? 1 : 0)) == 1));
            \config()->set('proxime.faq_text', get_option('faq_text', \config('proxime.faq_text')));
            \config()->set('proxime.toc_text', get_option('toc_text', \config('proxime.toc_text')));
            \config()->set('proxime.delivery.type', get_option('delivery_type', \config('proxime.delivery.type')));
            \config()->set('proxime.delivery.custom_percentage', get_option('delivery_custom_percentage', \config('proxime.delivery.custom_percentage')));

            \config()->set('ui.logo.large', get_option('large_logo', \config('ui.logo.large')));
            \config()->set('ui.logo.small', get_option('small_logo', \config('ui.logo.small')));

            \config()->set('mail.default', 'smtp');
            \config()->set('mail.mailers.smtp.host', get_option('smtp_host', config('mail.mailers.smtp.host')));
            \config()->set('mail.mailers.smtp.port', get_option('smtp_port', config('mail.mailers.smtp.port')));
            \config()->set('mail.mailers.smtp.encryption', get_option('smtp_encryption', config('mail.mailers.smtp.encryption')));
            \config()->set('mail.mailers.smtp.username', get_option('smtp_username', config('mail.mailers.smtp.username')));
            \config()->set('mail.mailers.smtp.password', get_option('smtp_password', config('mail.mailers.smtp.password')));
            \config()->set('mail.from.name', get_option('mail_from_name', config('mail.from.name')));
            \config()->set('mail.from.address', get_option('mail_from_address', config('mail.from.address')));

            \config()->set('services.currencylayer.access_key', get_option('currencylayer_access_key', config('services.currencylayer.access_key')));
            \config()->set('services.itech.sms.endpoint', get_option('sms_api', config('services.itech.sms.endpoint')));
            \config()->set('twilio-notification-channel.auth_token', get_option('twilio_auth_token', config('twilio-notification-channel.auth_token')));
            \config()->set('twilio-notification-channel.account_sid', get_option('twilio_account_sid', config('twilio-notification-channel.account_sid')));
            \config()->set('twilio-notification-channel.from', get_option('twilio_from', config('twilio-notification-channel.from')));


            \config()->set('proxime.app.app_version.android', get_option('android_app_version', config('proxime.app.app_version.android')));
            \config()->set('proxime.app.app_version.ios', get_option('android_app_version', config('proxime.app.app_version.ios')));

            \config()->set('proxime.app.color.color_primary', get_option('app_primary_color', config('proxime.app.color.color_primary')));
            \config()->set('proxime.app.color.color_primary_dark', get_option('app_primary_dark_color', config('proxime.app.color.color_primary_dark')));
            \config()->set('proxime.app.color.color_accent', get_option('app_accent_color', config('proxime.app.color.color_accent')));
            \config()->set('proxime.app.color.button_color_1', get_option('app_button_color_1', config('proxime.app.color.button_color_1')));
            \config()->set('proxime.app.color.button_color_2', get_option('app_button_color_2', config('proxime.app.color.button_color_2')));

            \config()->set('proxime.app.api_key.google_map_api_key', get_option('google_map_api_key', config('proxime.app.api_key.google_map_api_key')));
            \config()->set('proxime.app.api_key.direction_api_key', get_option('direction_api_key', config('proxime.app.api_key.direction_api_key')));

            \config()->set('proxime.app.splash.logo', get_option('app_splash_logo', config('proxime.app.splash.logo')));
        } catch (\Exception $exception) {

        }
    }
}
