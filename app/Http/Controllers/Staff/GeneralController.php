<?php

namespace App\Http\Controllers\Staff;

use App\Models\Currency;
use App\Models\LineItem;
use App\Models\Order;
use App\Models\Staff;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GeneralController
{
    function dashboard()

    {

        $data = [];
        $data['currency'] = Currency::getDefaultCurrency();
        $total_sale = Order::query()
            ->selectRaw("SUM(line_items.quantity*(line_items.price+line_items.tax)) as t")
            ->join('line_items', 'orders.id', '=', 'line_items.order_id')
            ->whereIn('status', [0, 1, 2, 3])
            ->first();
        $data['total_sale'] = $total_sale?number_format($total_sale->t, config('proxime.decimals', 2)):0;

        $today_sale = Order::query()
            ->selectRaw("SUM(line_items.quantity*(line_items.price+line_items.tax)) as t")
            ->join('line_items', 'orders.id', '=', 'line_items.order_id')
            ->whereDate('orders.created_at', today())
            ->whereIn('status', [0, 1, 2, 3])
            ->first();
        $data['today_sale'] = $today_sale?number_format($today_sale->t, config('proxime.decimals', 2)):0;

        $data['t_orders'] = Order::query()->count();
        $data['new_orders'] = Order::query()->whereIn('status', [0])->count();
        $data['processing_orders'] = Order::query()->whereIn('status', [1])->count();
        $data['way_orders'] = Order::query()->whereIn('status', [2])->count();
        $data['completed_orders'] = Order::query()->whereIn('status', [3])->count();
        $data['total_customers'] = User::query()->where('status', '!=', 0)->count();

        $startDate = Carbon::today()->firstOfMonth();
        $endDate = Carbon::today()->lastOfMonth();
        $query = Order::query();
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

        return view('staff.dashboard', $data);

    }

    function profile()
    {
        $user = Auth::guard('staff')->user();
        return view('staff.profile', compact('user'));
    }

    function updateProfile(Request $request)
    {
        /* @var Staff $user */
        $user = Auth::guard('staff')->user();
        $request->validate([
            'name' => 'required|max:191',
            'email' => 'required|max:191|email|unique:staff,email,' . $user->id,
            'phone' => 'required|numeric|unique:staff,phone,' . $user->id,
            'password' => 'nullable|min:6|confirmed'
        ]);
        $p = $request->only(['name', 'email', 'phone', 'avatar']);
        if ($request->password) {
            $p['password'] = Hash::make($request->password);
        }
        try {
            $r = $user->update($p);
            if (!$r) throw new \Exception('Unable to update profile');
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors($exception->getMessage());
        }
        return redirect()->back()->withSuccess('Profile updated successfully');
    }
}
