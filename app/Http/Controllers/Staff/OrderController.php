<?php

namespace App\Http\Controllers\Staff;

use App\Models\Coupon;
use App\Models\Currency;
use App\Models\DeliveryMan;
use App\Events\OrderStatusUpdated;
use App\Events\TransactionAdded;
use App\Helpers\TemplateBuilder;
use App\Models\LineItem;
use App\Notifications\NewOrderNotification;
use App\Notifications\NewOrderNotificationToShop;
use App\Notifications\OrderInvoiceToCustomer;
use App\Models\Order;
use App\Models\OrderNote;
use App\Models\PaymentMethod;
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Order::query()->with(['shop']);
            if (is_numeric($request->status) && in_array($request->status, [0])) {
                $query->where('status', $request->status);
            } else {
                $query->where('status', '!=', -1);
            }
            /* @var DataTableAbstract $table */
            $table = datatables()->of($query);
            $table->addColumn('order', function (Order $order) {
                return sprintf('<a href="%s">#%s %s</a>', route('staff.catalog.order.show', $order->id), $order->id, optional($order->user)->name);
            });
            $table->editColumn('shop.name', function (Order $order) {
                if (!$order->shop) return null;
                return $order->shop->name . '<br>' . $order->shop->rating_html . '<a href="' . route('staff.catalog.review.index', ['type' => 'shop', 'type_id' => $order->shop->id]) . '">More...</a>';
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
                $query->where('id', $keyword)->orWhereHas('user', function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%$keyword%");
                });
            });
            $table->editColumn('created_at', function (Order $order) {
                return optional($order->created_at)->format('M d, Y');
            });
            $table->editColumn('status', function (Order $order) {
                if ($order->status == -1) {
                    return sprintf('<span class="badge badge-secondary">Placed</span>');
                } elseif ($order->status == 0) {
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
            $table->rawColumns(['order', 'status', 'shop.name']);
            return $table->make(true);
        }
        return view('staff.order.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $customers = User::all();
        $currency = Currency::getDefaultCurrency();
        $products = Product::query()->whereNull('parent_id')->where('status', 1)->get();
        $variations = Product::query()->with('attrs')->whereNotNull('parent_id')->get()->map(function (Product $product) {
            $product->setRelation('attrs', $product->attrs->pluck('content', 'name'));
            return $product;
        });
        $shipping_methods = ShippingMethod::query()->where('status', 1)->get();
        $now = now();
        $coupons = Coupon::query()->with(['products', 'users'])->whereDate('start_at', '<=', $now)->whereDate('expire_at', '>=', $now)->get();
        $shops = Shop::query()->where('status', 1)->get();
        return view('staff.order.create', compact('coupons', 'shops', 'customers', 'currency', 'products', 'variations', 'shipping_methods'));
    }

    public function storeNote(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'context' => 'required|in:1,2',
            'content' => 'required'
        ]);
        try {
            $note = OrderNote::create($request->only(['order_id', 'content', 'context']));
        } catch (\Exception $exception) {
            return response($exception->getMessage());
        }
        return response($note);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        return $request->all();
        $request->validate([
            'status' => 'required|numeric|in:-1,0,1,2,3,4,5',
            'customer' => 'required|exists:users,id',
            'shop' => 'required|exists:shops,id',
            'billing.name' => 'required',
            'billing.email' => 'required',
            'billing.phone' => 'required',
            'billing.city' => 'required',
            'billing.street_address_1' => 'required',
            'billing.country' => 'required',
            'billing.latitude' => 'required|numeric',
            'billing.longitude' => 'required|numeric',

            'shipping.name' => 'required',
            'shipping.email' => 'required',
            'shipping.phone' => 'required',
            'shipping.city' => 'required',
            'shipping.street_address_1' => 'required',
            'shipping.country' => 'required',
            'shipping.latitude' => 'required|numeric',
            'shipping.longitude' => 'required|numeric',

            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variation_id' => 'nullable|exists:products,id',
            'items.*.product_title' => 'required',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric',
            'items.*.tax' => 'required|numeric',

            'shipping_method_id' => 'required|exists:shipping_methods,id',
            'shipping_method_name' => 'required',
            'shipping_charge' => 'required|numeric|min:0',
            'coupon_id' => 'nullable|exists:coupons,id',
            'coupon_code' => 'nullable',
            'discount' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $op = $request->only(['coupon_id', 'coupon_code', 'discount', 'shipping_method_id', 'shipping_method_name', 'shipping_charge', 'status']);
            $op['track'] = Order::generateTrack();
            $op['user_id'] = $request->customer;
            $op['shop_id'] = $request->shop;
            /* @var Order $order */
            $order = Order::create($op);
            $line_items = array_map(function ($item) {
                return [
                    'product_id' => $item['product_id'],
                    'variation_id' => $item['variation_id'],
                    'product_title' => $item['product_title'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'tax' => $item['tax'],
                    'attrs' => isset($item['attrs']) ? $item['attrs'] : []
                ];
            }, $request->items);
            $order->items()->createMany($line_items);
            $order->addresses()->create(Arr::add($request->shipping, 'type', 'shipping'));
            $order->addresses()->create(Arr::add($request->billing, 'type', 'billing'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors($exception->getMessage());
        }
        DB::commit();
        return redirect()->route('staff.catalog.order.index')->withSuccess('Order created successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param \App\Order $order
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View|void
     */
    public function show(Order $order)
    {
        if (!$order instanceof Order) return abort(404);
        $currency = Currency::getDefaultCurrency();
        $billing = $order->addresses()->where('type', 'billing')->first();
        $shipping = $order->addresses()->where('type', 'shipping')->first();
        $payments = $order->payments()->orderByDesc('id')->get();
        $notes = $order->notes()->get();
        $delivery_mans = DeliveryMan::query()->where('status', 1)->get();
        $review = $order->reviews()->first();
        $payable_total = $order->gross_total + $order->shipping_charge - $order->discount;
        $methods = PaymentMethod::query()->where(function ($q1) use ($payable_total) {
            /* @var Builder $q1 */
            $q1->where('min', -1)->orWhere('min', '<=', $payable_total);
        })->where(function ($q2) use ($payable_total) {
            /* @var Builder $q2 */
            $q2->where('max', -1)->orWhere('max', '>=', $payable_total);
        })->get(['id', 'name', 'description']);
        return view('staff.order.show', compact('review', 'delivery_mans', 'currency', 'order', 'billing', 'shipping', 'payments', 'notes', 'methods'));
    }

    /**
     * Display the specified order invoice.
     *
     * @param \App\Order $order
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View|void
     */
    public function invoice(Order $order, $type = 'html')
    {
        if (!$order instanceof Order) return abort(404);
        if (!in_array($type, ['html', 'pdf'])) return abort(404);
        $params = $order->getInvoiceParams();
        $invoice = (new TemplateBuilder)->fetch('invoice')->parse($params);
        if ($type == 'pdf') {
            return $invoice->toPdf();
        }
        return $invoice->toView();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Order $order
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View|void
     */
    public function edit(Order $order)
    {
        if ($order == null) return abort(404);
        $order->load(['shipping_method', 'coupon', 'user']);
        $order->setRelation('items', $order->items()->with(['product', 'variant'])->get());
        $billing = $order->addresses()->where('type', 'billing')->first();
        $shipping = $order->addresses()->where('type', 'shipping')->first();

        $customers = User::all();
        $currency = Currency::getDefaultCurrency();
        $products = Product::query()->whereNull('parent_id')->where('status', 1)->get();
        $variations = Product::query()->with('attrs')->whereNotNull('parent_id')->get()->map(function (Product $product) {
            $product->setRelation('attrs', $product->attrs->pluck('content', 'name'));
            return $product;
        });
        $shipping_methods = ShippingMethod::query()->where('status', 1)->get();
        $now = now();
        $coupons = Coupon::query()->with(['products', 'users'])->whereDate('start_at', '<=', $now)->whereDate('expire_at', '>=', $now)->get();
        $shops = Shop::query()->where('status', 1)->get();
        return view('staff.order.edit', compact('order', 'shops', 'billing', 'shipping', 'coupons', 'customers', 'currency', 'products', 'variations', 'shipping_methods'));
    }

    public function postAction(Request $request, Order $order)
    {
        if (!$order) return abort(404);
        $request->validate([
            'action' => 'required|in:1,2,3'
        ]);
        /* @var User $customer */
        $customer = $order->user;
        if ($request->action == 1) {
            // Send Invoice To Customer
            $customer->notify(new OrderInvoiceToCustomer($order));
        } elseif ($request->action == 2) {
            // Send New Order Notification To Customer
            $customer->notify(new NewOrderNotification($order));
        } elseif ($request->action == 3) {
            // Send New Order Notification To Shop
            $customer->notify(new NewOrderNotificationToShop($order));
        }
        return redirect()->back()->withSuccess('Action executed successfully');
    }

    public function refund(Request $request, Order $order)
    {
        if (!$order) return abort(404);
        if ($order->status != 5) throw new AuthorizationException;
        if ($order->refundExist()) throw new AuthorizationException;
        /* @var User $customer */
        $customer = $order->user;
        $net_amount = $order->gross_total + $order->shipping_charge - $order->discount;
        try {
            DB::beginTransaction();
            $r = $customer->update([
                'balance' => ($customer->balance + $net_amount)
            ]);
            if (!$r) throw new \Exception('Unable to update customer balance');
            /* @var Transaction $transaction */
            $transaction = $customer->transactions()->create([
                'track' => Transaction::generateTrack(),
                'title' => 'Order Refund. Order ID #' . $order->id,
                'ref_type' => get_class($order),
                'ref_id' => $order->id,
                'type' => '+',
                'amount' => $net_amount,
                'matter' => 'order_refund'
            ]);
            if (!$transaction) throw new \Exception('Unable to add new transaction');
            event(new TransactionAdded($transaction));
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors($exception->getMessage());
        }
        return redirect()->back()->withSuccess('Order refunded successfully');
    }

    public function commission(Request $request, Order $order)
    {
        if (!$order) return abort(404);
        if ($order->status != 3) throw new AuthorizationException;
        if (!$order->shop) return abort(404);
        if ($order->shopCommissionExist()) throw new AuthorizationException;
        /* @var Shop $shop */
        $shop = $order->shop;
        try {
            DB::beginTransaction();
            $r = $shop->update([
                'balance' => ($shop->balance + $order->shop_commission)
            ]);
            if (!$r) throw new \Exception('Unable to update shop balance');
            /* @var Transaction $transaction */
            $transaction = $shop->transactions()->create([
                'track' => Transaction::generateTrack(),
                'title' => 'Received Order Profit. Order ID #' . $order->id,
                'ref_type' => get_class($order),
                'ref_id' => $order->id,
                'type' => '+',
                'amount' => $order->shop_commission,
                'matter' => 'shop_commission'
            ]);
            if (!$transaction) throw new \Exception('Unable to add new transaction');
            event(new TransactionAdded($transaction));
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors($exception->getMessage());
        }
        return redirect()->back()->withSuccess('Shop Commission Paid Successfully');
    }

    public function updateStatus(Request $request, $id = null)
    {
        $request->validate([
            'status' => 'required|in:0,1,2,3,4,5'
        ], [
            'status.*' => 'Invalid status'
        ]);
        $orders = [];
        if ($order = Order::find($id)) {
            $orders[] = $order;
        } else {
            $orders = Order::query()->whereIn('id', (is_array($request->orders) ? $request->orders : []))->get();
        }
        try {
            foreach ($orders as $order) {
                $from = $order->status;
                $r = $order->update(['status' => $request->status]);
                if ($r) {
                    event(new OrderStatusUpdated($order, $from, $request->status));
                }
            }
        } catch (\Exception $exception) {
            return redirect()->back()->withSuccess($exception->getMessage());
        }
        return redirect()->back()->withSuccess('Status updated successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Order $order
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        if ($order == null) return abort(404);
        $request->validate([
            'status' => 'required|numeric|in:0,1,2,3,4,5',
            'customer' => 'required|exists:users,id',
            'shop' => 'required|exists:shops,id',
            'billing.name' => 'required',
            'billing.email' => 'required',
            'billing.phone' => 'required',
            'billing.city' => 'required',
            'billing.street_address_1' => 'required',
            'billing.country' => 'required',

            'shipping.name' => 'required',
            'shipping.email' => 'required',
            'shipping.phone' => 'required',
            'shipping.city' => 'required',
            'shipping.street_address_1' => 'required',
            'shipping.country' => 'required',

            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variation_id' => 'nullable|exists:products,id',
            'items.*.product_title' => 'required',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric',
            'items.*.tax' => 'required|numeric',

            'shipping_method_id' => 'required|exists:shipping_methods,id',
            'shipping_method_name' => 'required',
            'shipping_charge' => 'required|numeric|min:0',
            'coupon_id' => 'nullable|exists:coupons,id',
            'coupon_code' => 'nullable',
            'discount' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $op = $request->only(['coupon_id', 'coupon_code', 'discount', 'shipping_method_id', 'shipping_method_name', 'shipping_charge', 'status']);
            $op['user_id'] = $request->customer;
            $op['shop_id'] = $request->shop;
            $from = $order->status;
            $to = $request->status;
            $order->update($op);
            $line_items = array_map(function ($item) {
                return [
                    'id' => (isset($item['id']) && is_numeric($item['id'])) ? $item['id'] : 0,
                    'product_id' => $item['product_id'],
                    'variation_id' => $item['variation_id'],
                    'product_title' => $item['product_title'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'tax' => $item['tax'],
                    'attrs' => isset($item['attrs']) ? $item['attrs'] : []
                ];
            }, $request->items);
            $existing_line_item_ids = $order->items()->pluck('id')->toArray();
            $return_line_item_ids = [];
            foreach ($line_items as $item) {
                if ($item['id']) {
                    $return_line_item_ids[] = $item['id'];
                }
                $order->items()->updateOrCreate(['id' => $item['id']], Arr::except($item, ['id']));
            }

            $order->addresses()->delete();
            $order->addresses()->create(Arr::add($request->shipping, 'type', 'shipping'));
            $order->addresses()->create(Arr::add($request->billing, 'type', 'billing'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors($exception->getMessage());
        }
        DB::commit();
        event(new OrderStatusUpdated($order, $from, $to));
        return redirect()->route('staff.catalog.order.index')->withSuccess('Order updated successfully');
    }
}
