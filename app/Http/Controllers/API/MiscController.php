<?php

namespace App\Http\Controllers\API;

use App\Models\Banner;
use App\Models\Currency;
use App\Helpers\API\Formatter;
use App\Models\Language;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class MiscController
{
    use Formatter;

    public function transaction(Request $request)
    {
        /* @var User $user */
        $user = $request->user('api');
        $query = $user->transactions();
        if (in_array($request->type, ['+', '-'])) {
            $query->where('type', $request->type);
        }
        if ($request->order_by && in_array($request->order, ['asc', 'desc'])) {
            $query->orderBy($request->order_by, $request->order);
        } else {
            $query->orderByDesc('id');
        }
        if ($request->paginate) {
            return $this->withSuccess($query->paginate($request->perpage));
        } else {
            if ($request->limit) {
                $query->take($request->limit);
            }
        }
        return $this->withSuccess($query->get());
    }

    public function configs()
    {
        $config = config('proxime.app');
        $config['logo'] = $config['splash']['logo'];
        unset($config['splash']);
        $config['name'] = config('app.name');
        $config['currency'] = Currency::getDefaultCurrency();
        return $config;
    }

    public function languages()
    {
        $languages = Language::query()->get(['name', 'code']);
        return $this->withSuccess($languages);
    }

    public function colors()
    {
        return $this->withSuccess(config('catalog.colors'));
    }

    public function help()
    {
        $data = [
            'faq_text' => config('proxime.faq_text'),
            'toc_text' => config('proxime.toc_text'),
        ];
        return $this->withSuccess($data);
    }

    public function currencies()
    {
        $currencies = Currency::query()->get(['name', 'code']);
        return $this->withSuccess($currencies);
    }

    public function banner()
    {
        return $this->withSuccess(Banner::all());
    }

    public function singleBanner(Request $request, Banner $banner)
    {
        if ($banner == null) return abort(404);
        $productQuery = $banner->products();
        $orderBy = in_array($request->order_by, $banner->toArray()) ? $request->order_by : 'id';
        $order = in_array($request->order, ['ASC', 'DESC']) ? $request->order : 'DESC';
        $productQuery->orderBy($orderBy, $order);
        if (is_numeric($request->paginate)) {
            $products = $productQuery->paginate($request->paginate);
        } else {
            $products = $productQuery->get();
        }
        return $this->withSuccess($products);
    }

    public function profile()
    {
        $user = Auth::guard('api')->user();
        return $this->withSuccess($user);
    }

    public function updateProfile(Request $request)
    {
        /* @var User $user */
        $user = Auth::guard('api')->user();
        $request->validate([
            'username' => 'required|alpha_dash|max:191|unique:users,username,' . $user->id,
            'email' => 'required|email|max:191|unique:users,email,' . $user->id,
            'phone' => 'required|numeric|unique:users,phone,' . $user->id,
            'avatar' => 'nullable|image',
            'name' => 'required|string|max:191',
            'push_notification' => 'nullable|in:0,1'
        ]);
        $params = $request->only(['username', 'email', 'phone', 'name']);
        if ($request->push_notification == 1) {
            $params['push_notification'] = 1;
        } else {
            $params['push_notification'] = 0;
        }
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $p = 'avatar' . DIRECTORY_SEPARATOR . $avatar->hashName();
            $resized = Image::make($avatar)->orientate()->resize(200, 200)->encode();
            if (Storage::disk('public')->put($p, $resized)) $params['avatar'] = Storage::disk('public')->url($p);
        }
        try {
            $res = $user->update($params);
            if (!$res) throw new \Exception('Unable to update profile');
        } catch (\Exception $exception) {
            return $this->withErrors($exception->getMessage());
        }
        $user->refresh();
        return $this->withSuccess($user);
    }

    public function updateDeviceToken(Request $request)
    {
        $request->validate([
            'token' => 'required|max:191'
        ]);
        /* @var User $user */
        $user = Auth::guard('api')->user();
        try {
            $res = $user->update([
                'device_token' => $request->token
            ]);
            if (!$res) throw new \Exception('Unable to update token');
        } catch (\Exception $exception) {
            return $this->withErrors($exception->getMessage());
        }
        return $this->withSuccess('Token updated successfully');
    }

    public function updatePushNotification(Request $request)
    {
        $request->validate([
            'push_notification' => 'nullable|in:0,1'
        ]);
        /* @var User $user */
        $user = Auth::guard('api')->user();
        try {
            $res = $user->update([
                'push_notification' => $request->push_notification == 1 ? 1 : 0
            ]);
            if (!$res) throw new \Exception('Unable to update setting');
        } catch (\Exception $exception) {
            return $this->withErrors($exception->getMessage());
        }
        return $this->withSuccess('Setting updated successfully');
    }

    public function updatePassword(Request $request)
    {
        /* @var User $user */
        $user = Auth::guard('api')->user();
        $request->validate([
            'old_password' => 'required|min:6|max:191',
            'password' => 'required|min:6|max:191|confirmed',
        ]);
        if (!Hash::check($request->old_password, $user->password)) return $this->withErrors('Old password is wrong');
        try {
            $res = $user->update([
                'password' => Hash::make($request->password)
            ]);
            if (!$res) throw new \Exception('Unable to update password');
        } catch (\Exception $exception) {
            return $this->withErrors($exception->getMessage());
        }
        return $this->withSuccess('Password updated successfully');
    }

    public function notification(Request $request, $type = null)
    {
        /* @var User $user */
        $user = Auth::guard('api')->user();
        if ($type == 'unread') {
            $query = $user->unreadNotifications()->orderBy('created_at', 'desc');
        } else {
            $query = $user->notifications();
        }
        if ($request->limit) {
            $query->take($request->limit);
        }
        if ($request->paginate) {
            return $this->withSuccess($query->paginate());
        }
        return $this->withSuccess($query->get());
    }

    public function markAsRead(Request $request, DatabaseNotification $notification)
    {
        if (!$notification) return abort(404);
        /* @var User $user */
        $user = Auth::guard('api')->user();
        if ($notification->notifiable_type != User::class || $notification->notifiable_id != $user->id) throw new AuthorizationException;
        $notification->markAsRead();
        return $this->withSuccess(['success' => true]);
    }
}
