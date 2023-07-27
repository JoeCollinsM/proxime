<?php

namespace App\Http\Controllers\Staff;

use App\Helpers\Hex;
use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    function generalSettings()
    {
        $tags = Tag::all();
        $featured_tag_ids = get_option('featured_tags', null);
        if ($featured_tag_ids) {
            $featured_tag_ids = json_decode($featured_tag_ids, true);
            if (!is_array($featured_tag_ids)) $featured_tag_ids = [];
        } else {
            $featured_tag_ids = [];
        }
        return view('staff.setting.general', compact('tags', 'featured_tag_ids'));
    }

    function updateGeneralSettings(Request $request)
    {
        $request->validate([
            'project_name' => 'required',
            'timezone' => 'required',
            'forgot_password_via' => 'required|in:email,sms',
            'faq_text' => 'nullable',
            'toc_text' => 'nullable',
            'sms_via' => 'required|in:twilio,other',
            'featured_tags' => 'nullable|array',
            'featured_tags.*' => 'nullable|exists:tags,id',
            'delivery_type' => 'required|in:fixed,custom',
            'delivery_custom_percentage' => 'required|numeric|min:0'
        ]);
        $params = $request->only(['sms_via', 'project_name', 'timezone', 'debug_mode', 'email_verification', 'sms_verification', 'forgot_password_via', 'email_notification', 'sms_notification', 'fcm_notification', 'default_user_status', 'default_vendor_status', 'faq_text', 'toc_text', 'delivery_type', 'delivery_custom_percentage']);

        if ($request->debug_mode == 'on') {
            $params['debug_mode'] = 1;
        } else {
            $params['debug_mode'] = 0;
        }
        if ($request->email_verification == 'on') {
            $params['email_verification'] = 1;
        } else {
            $params['email_verification'] = 0;
        }
        if ($request->sms_verification == 'on') {
            $params['sms_verification'] = 1;
        } else {
            $params['sms_verification'] = 0;
        }
        if ($request->email_notification == 'on') {
            $params['email_notification'] = 1;
        } else {
            $params['email_notification'] = 0;
        }
        if ($request->sms_notification == 'on') {
            $params['sms_notification'] = 1;
        } else {
            $params['sms_notification'] = 0;
        }
        if ($request->fcm_notification == 'on') {
            $params['fcm_notification'] = 1;
        } else {
            $params['fcm_notification'] = 0;
        }
        if ($request->default_user_status == 'on') {
            $params['default_user_status'] = 1;
        } else {
            $params['default_user_status'] = 0;
        }
        if ($request->default_vendor_status == 'on') {
            $params['default_vendor_status'] = 1;
        } else {
            $params['default_vendor_status'] = 0;
        }
        if (is_array($request->featured_tags)) {
            $params['featured_tags'] = json_encode($request->featured_tags);
        } else {
            $params['featured_tags'] = json_encode([]);
        }
        DB::beginTransaction();
        try {
            foreach ($params as $name => $content) {
                Option::updateOption($name, $content);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors($exception->getMessage());
        }
        DB::commit();
        return redirect()->back()->withSuccess('Settings Updated Successfully');
    }

    function logoSettings()
    {
        return view('staff.setting.logo');
    }

    function updateLogoSettings(Request $request)
    {
        $request->validate([
            'large_logo' => 'nullable',
            'small_logo' => 'nullable',
            'favicon' => 'nullable',
        ]);
        $params = $request->only(['large_logo', 'small_logo', 'favicon']);
        DB::beginTransaction();
        try {
            foreach ($params as $name => $content) {
                Option::updateOption($name, $content);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors($exception->getMessage());
        }
        DB::commit();
        return redirect()->back()->withSuccess('Settings Updated Successfully');
    }

    function emailSettings()
    {
        return view('staff.setting.email');
    }

    function updateEmailSettings(Request $request)
    {
        $request->validate([
            'smtp_host' => 'required',
            'smtp_port' => 'required',
            'smtp_encryption' => 'nullable|in:ssl,tls',
            'smtp_username' => 'required',
            'smtp_password' => 'required',
            'mail_from_name' => 'required',
            'mail_from_address' => 'required'
        ]);
        $params = $request->only(['smtp_host', 'smtp_port', 'smtp_encryption', 'smtp_username', 'smtp_password', 'mail_from_name', 'mail_from_address']);
        DB::beginTransaction();
        try {
            foreach ($params as $name => $content) {
                Option::updateOption($name, $content);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors($exception->getMessage());
        }
        DB::commit();
        return redirect()->back()->withSuccess('Settings Updated Successfully');
    }

    function serviceSettings()
    {
        $firebase_credentials = file_get_contents(storage_path('firebase_credentials.json'));
        return view('staff.setting.service', compact('firebase_credentials'));
    }

    function updateServiceSettings(Request $request)
    {
        $request->validate([
            'currencylayer_access_key' => 'required',
            'sms_api' => (config('proxime.sms_via') == 'twilio' ? 'nullable' : 'required'),
            'twilio_auth_token' => (config('proxime.sms_via') == 'twilio' ? 'required' : 'nullable'),
            'twilio_account_sid' => (config('proxime.sms_via') == 'twilio' ? 'required' : 'nullable'),
            'twilio_from' => (config('proxime.sms_via') == 'twilio' ? 'required' : 'nullable'),
            'firebase_credentials' => 'required'
        ]);
        $params = $request->only(['currencylayer_access_key', 'sms_api', 'twilio_auth_token', 'twilio_account_sid', 'twilio_from'/*, 'facebook_client_id', 'facebook_client_secret', 'google_client_id', 'google_client_secret'*/]);
        if ($request->firebase_credentials) {
            file_put_contents(storage_path('firebase_credentials.json'), $request->firebase_credentials);
        }
        DB::beginTransaction();
        try {
            foreach ($params as $name => $content) {
                Option::updateOption($name, $content);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors($exception->getMessage());
        }
        DB::commit();
        return redirect()->back()->withSuccess('Settings Updated Successfully');
    }

    function appSettings()
    {
        return view('staff.setting.app');
    }

    function updateAppSettings(Request $request)
    {
        $request->validate([
            'android_app_version' => 'required',
            'app_primary_color' => ['required', new Hex],
            'app_primary_dark_color' => ['required', new Hex],
            'app_accent_color' => ['required', new Hex],
            'app_button_color_1' => ['required', new Hex],
            'app_button_color_2' => ['required', new Hex],
            'google_map_api_key' => 'nullable',
            'direction_api_key' => 'nullable',
            'app_splash_logo' => 'required',
        ]);
        $params = $request->only(['android_app_version', 'app_primary_color', 'app_primary_dark_color', 'app_accent_color', 'app_button_color_1', 'app_button_color_2', 'google_map_api_key', 'direction_api_key', 'app_splash_logo']);
        DB::beginTransaction();
        try {
            foreach ($params as $name => $content) {
                Option::updateOption($name, $content);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors($exception->getMessage());
        }
        DB::commit();
        return redirect()->back()->withSuccess('Settings Updated Successfully');
    }
}
