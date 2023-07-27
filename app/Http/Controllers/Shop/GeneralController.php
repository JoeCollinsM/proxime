<?php

namespace App\Http\Controllers\Shop;

use App\Models\Currency;
use App\Models\LineItem;
use App\Models\Order;
use App\Models\Shop;
use App\Models\Staff;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GeneralController
{
    function dashboard(Request $request)

    {
        /* @var Shop $shop */
        $shop = $request->user('shop');
        $data = [];
        $data['currency'] = Currency::getDefaultCurrency();
        $total_sale = Order::query()
            ->where('shop_id', $shop->id)
            ->selectRaw("SUM(line_items.quantity*(line_items.price+line_items.tax)) as t")
            ->join('line_items', 'orders.id', '=', 'line_items.order_id')
            ->whereIn('status', [0, 1, 2, 3])
            ->first();
        $data['total_sale'] = $total_sale ? number_format($total_sale->t, config('proxime.decimals', 2)) : 0;

        $today_sale = Order::query()
            ->where('shop_id', $shop->id)
            ->selectRaw("SUM(line_items.quantity*(line_items.price+line_items.tax)) as t")
            ->join('line_items', 'orders.id', '=', 'line_items.order_id')
            ->whereDate('orders.created_at', today())
            ->whereIn('status', [0, 1, 2, 3])
            ->first();
        $data['today_sale'] = $today_sale ? number_format($today_sale->t, config('proxime.decimals', 2)) : 0;

        $data['t_orders'] = Order::query()->where('shop_id', $shop->id)->count();
        $data['new_orders'] = Order::query()->where('shop_id', $shop->id)->whereIn('status', [0])->count();
        $data['processing_orders'] = Order::query()->where('shop_id', $shop->id)->whereIn('status', [1])->count();
        $data['way_orders'] = Order::query()->where('shop_id', $shop->id)->whereIn('status', [2])->count();
        $data['completed_orders'] = Order::query()->where('shop_id', $shop->id)->whereIn('status', [3])->count();

        $startDate = Carbon::today()->firstOfMonth();
        $endDate = Carbon::today()->lastOfMonth();
        $query = Order::query()->where('shop_id', $shop->id);
        if (file_exists(database_path('sqls/get-sales-by-date.sql'))) {
            $query->selectRaw(file_get_contents(database_path('sqls/get-sales-by-date.sql')));
        }
        $query->whereDate('created_at', '>=', $startDate);
        $query->whereDate('created_at', '<=', $endDate);
        $query->groupBy('day');

        $data['order_placed_in_month_by_date'] = (clone $query)->pluck('total_orders');
        $data['net_sale_in_month_by_date'] = (clone $query)->pluck('total_net_amount')->map(function ($i) {
            return round($i, 2);
        });
        $data['tax_in_month_by_date'] = (clone $query)->pluck('total_tax_amount')->map(function ($i) {
            return round($i, 2);
        });
        $data['discount_in_month_by_date'] = (clone $query)->pluck('total_discount')->map(function ($i) {
            return round($i, 2);
        });
        $data['gross_sale_in_month_by_date'] = (clone $query)->pluck('total_gross_amount')->map(function ($i) {
            return round($i, 2);
        });
        $data['dates_in_month_by_order'] = $query->pluck('day');

        return view('shop.dashboard', $data);

    }

    function profile()
    {
        $shop = Auth::guard('shop')->user();
        $meta = $shop->metas()->pluck('content', 'name');
        return view('shop.profile', compact('shop', 'meta'));
    }

    function updateProfile(Request $request)
    {
        /* @var Shop $user */
        $user = Auth::guard('shop')->user();
        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'required|max:255',
            'cover' => 'required|max:255',
            'address' => 'required|max:255',
            'latitude' => 'required|numeric|max:255',
            'longitude' => 'required|numeric|max:255',
            'opening_at' => 'required|max:255|date_format:"h:i a"',
            'closing_at' => 'required|max:255|date_format:"h:i a"',
            'details' => 'nullable',
            'vendor_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:shops,email,' . $user->id,
            'phone' => 'nullable|numeric|unique:shops,phone,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
        ]);
        $params = $request->only(['name', 'logo', 'cover', 'address', 'latitude', 'longitude', 'opening_at', 'closing_at', 'details', 'vendor_name', 'email', 'phone']);
        if ($request->password) $params['password'] = Hash::make($request->password);
        DB::beginTransaction();
        try {
            $res = $user->update($params);
            if (!$res) throw new \Exception('Unable to update profile');
            if (is_array($request->meta)) {
                $user->metas()->delete();
                $metas = [];
                foreach ($request->meta as $name => $content) {
                    $metas[] = ['name' => $name, 'content' => $content];
                }
                $user->metas()->createMany($metas);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors('Unable to update profile');
        }
        DB::commit();
        return redirect()->back()->withSuccess('Profile updated successfully');
    }
}
