<?php

namespace App\Http\Controllers\API\DeliveryMan;

use App\Models\Currency;
use App\Models\DeliveryMan;
use App\Helpers\API\Formatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class MiscController
{
    use Formatter;

    public function transaction(Request $request)
    {
        /* @var DeliveryMan $user */
        $user = $request->user('delivery_man');
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

    public function profile()
    {
        $user = Auth::guard('delivery_man')->user();
        return $this->withSuccess($user);
    }

    public function updateProfile(Request $request)
    {
        /* @var DeliveryMan $user */
        $user = Auth::guard('delivery_man')->user();
        $request->validate([
            'username' => 'required|alpha_dash|max:191|unique:delivery_men,username,' . $user->id,
            'email' => 'required|email|max:191|unique:delivery_men,email,' . $user->id,
            'phone' => 'required|numeric|unique:delivery_men,phone,' . $user->id,
            'avatar' => 'nullable|image',
            'name' => 'required|string|max:191'
        ]);
        $params = $request->only(['username', 'email', 'phone', 'name']);
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
        /* @var DeliveryMan $user */
        $user = Auth::guard('delivery_man')->user();
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

    public function updatePassword(Request $request)
    {
        /* @var DeliveryMan $user */
        $user = Auth::guard('delivery_man')->user();
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
}
