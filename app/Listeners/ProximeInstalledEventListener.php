<?php

namespace App\Listeners;

use App\Models\Currency;
use App\Models\NotificationTemplate;
use App\Models\PaymentMethod;
use App\Models\Staff;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RachidLaasri\LaravelInstaller\Events\LaravelInstallerFinished;


class ProximeInstalledEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param LaravelInstallerFinished $event
     * @return void
     */
    public function handle(LaravelInstallerFinished $event)
    {
        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            if (Str::contains(file_get_contents($envPath), 'APP_VERSION') === false) {
                // create new entry
                file_put_contents($envPath, PHP_EOL . 'APP_VERSION=v4.1.1' . PHP_EOL, FILE_APPEND);
            } else {
                file_put_contents($envPath, str_replace(
                    'APP_VERSION=' . env('APP_VERSION', 'v2.2.0'), 'APP_VERSION=v4.1.1', file_get_contents($envPath)
                ));
            }
        }
        // Generate JWT Secret
        Artisan::call('jwt:secret');
        // Generate Storage Link
        Artisan::call('storage:link');
        // Install Currencies
        try {
            DB::beginTransaction();
            foreach (config('currencies.names') as $code => $name) {
                Currency::query()->create([
                    'name' => html_entity_decode($name),
                    'code' => html_entity_decode($code),
                    'symbol' => html_entity_decode(config('currencies.symbols.' . $code)),
                    'is_default' => ($code == 'USD') ? 1 : 0,
                    'status' => ($code == 'USD') ? 1 : 0
                ]);
            }
            PaymentMethod::generateMethods();
            foreach (['email', 'sms', 'fcm'] as $type) {
                foreach (config($type) as $name => $item) {
                    $item['name'] = $name;
                    $item['channel'] = $type;
                    NotificationTemplate::create($item);
                }
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
        }
        // Add Super Admin Staff
//        try {
//            DB::beginTransaction();
//            Staff::query()->create([
//                'role_id' => null,
//                'name' => 'Super Admin',
//                'email' => 'admin@proxime.com',
//                'phone' => null,
//                'password' => Hash::make('SuperAdmin')
//            ]);
//            DB::commit();
//        } catch (\Exception $exception) {
//            DB::rollBack();
//        }
    }
}
