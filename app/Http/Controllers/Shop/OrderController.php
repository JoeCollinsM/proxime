<?php

namespace App\Http\Controllers\Shop;

use App\Models\Coupon;
use App\Models\Currency;
use App\Models\DeliveryMan;
use App\Events\OrderStatusUpdated;
use App\Events\OrderUpdatedByShop;
use App\Events\TransactionAdded;
use App\Helpers\TemplateBuilder;
use App\Models\LineItem;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderInvoiceToCustomer;
use App\Models\Order;
use App\Models\OrderNote;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\Shop;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTableAbstract;

class OrderController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Illuminate\View\View
     * @throws \Exception
     */
    public function index(Request $request)
    {
        /* @var Shop $shop */
        $shop = $request->user('shop');
        if ($request->ajax()) {
            $query = Order::query()->where('shop_id', $shop->id)->where('status', '!=', -1);
            if (is_numeric($request->status) && in_array($request->status, [0])) {
                $query->where('status', $request->status);
            }
            /* @var DataTableAbstract $table */
            $table = datatables()->of($query);
            $table->addColumn('order', function (Order $order) {
                return sprintf('<a href="%s">Order #%s</a>', route('shop.catalog.order.show', $order->id), $order->id);
            });
            $table->addColumn('total', function (Order $order) {
                $currency = Currency::getDefaultCurrency();
                $t = $order->gross_total + $order->shipping_charge - $order->discount;
                return $t . ' ' . $currency->code;
            });
            $table->orderColumn('total', function ($query, $order) {
                /* @var Builder $query */
                $query->addSelect(['total' => LineItem::query()->selectRaw('SUM((price+tax)*quantity) as t')->whereColumn('order_id', 'orders.id')->groupBy('order_id')])->orderBy('total', $order);
            });
            $table->filterColumn('order', function ($query, $keyword) {
                /* @var Builder $query */
                $query->where('id', $keyword);
            });
            $table->editColumn('created_at', function (Order $order) {
                return optional($order->created_at)->format('M d, Y');
            });
            $table->editColumn('status', function (Order $order) {
                if ($order->status == 0) {
                    return sprintf('<span class="badge badge-warning">Pending</span>');
                } elseif ($order->status == 1) {
                    return sprintf('<span class="badge badge-info">Processing</span>');
                } elseif ($order->status == 2) {
                    return sprintf('<span class="badge badge-info">On The Way</span>');
                } elseif ($order->status == 3) {
                    return sprintf('<span class="badge badge-success">Completed</span>');
                } elseif ($order->status == 4) {
                    return sprintf('<span class="badge badge-warning">Hold</span>');
                } elseif ($order->status == 5) {
                    return sprintf('<span class="badge badge-danger">Canceled</span>');
                }
            });
            $table->rawColumns(['order', 'status']);
            return $table->make(true);
        }
        return view('shop.order.index');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Order $order
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View|void
     */
    public function show(Request $request, Order $order)
    {
        if (!$order instanceof Order) return abort(404);
        /* @var Shop $shop */
        $shop = $request->user('shop');
        if ($order->shop_id != $shop->id) throw new AuthorizationException;
        $currency = Currency::getDefaultCurrency();
        $review = $order->reviews()->first();
        return view('shop.order.show', compact('review', 'currency', 'order'));
    }

    public function updateStatus(Request $request, $id = null)
    {
        /* @var Order $order */
        $order = Order::findOrFail($id);
        /* @var Shop $shop */
        $shop = $request->user('shop');
        if ($order->shop_id != $shop->id) throw new AuthorizationException;
        if (!in_array($order->status, [0, 1])) throw new AuthorizationException;
        $request->validate([
            'status' => 'required|in:1,5'
        ]);
        try {
            DB::beginTransaction();
            $r = $order->update([
                'status' => $request->status
            ]);
            if (!$r) throw new \Exception('Unable to update order');
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors($exception->getMessage());
        }
        event(new OrderUpdatedByShop($order));
        return redirect()->back()->withSuccess('Order updated success');
    }
}
