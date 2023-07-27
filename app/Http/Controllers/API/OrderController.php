<?php

namespace App\Http\Controllers\API;

use App\Models\Cart;
use App\Models\CartItem;
use App\Currency;
use App\Helpers\API\Formatter;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\ShippingMethod;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController
{
    use Formatter;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /* @var User $user */
        $user = Auth::guard('api')->user();
        $query = $user->orders()->with('shop')->where('status', '!=', -1);
        if ($request->order_id) {
            $i = $request->order_id;
            $query->where('orders.id', 'LIKE', "$i%");
        }
        $orders = $query->orderBy('id', 'DESC')->paginate();
        return $this->withSuccess($orders);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /* @var User $user */
        $user = Auth::guard('api')->user();
        $currency = Currency::getDefaultCurrency();
        /* @var Cart $cart */
        $cart = $user->carts()->where('type', 'cart')->first();
        if (!$cart) return $this->withErrors('Your cart is empty');

        $cart_items = $cart->items()->get();
        if (!$cart_items->count()) return $this->withErrors('Your cart is empty');

        (new CartController)->updateCartPrices();

        /* @var CartItem $firstItem */
        $firstItem = $cart->items()->first();
        /* @var Shop $shop */
        $shop = Shop::find($firstItem->product->shop_id);

        if ($shop->minimum_order != -1) {
            if ($cart->gross_total < $shop->minimum_order) return $this->withErrors(sprintf('Your current order total is %s %s — you must have an order with a minimum of %s %s to place your order', $cart->gross_total, $currency->code, $shop->minimum_order, $currency->code));
        }

        $request->validate([
            'payment_method' => 'required|exists:payment_methods,id',
            'shipping_method' => 'required|exists:shipping_methods,id',
            'shipping_address' => 'required|exists:addresses,id',
            'billing_address' => 'required|exists:addresses,id',
        ]);
        $shipping_method = ShippingMethod::find($request->shipping_method);
        $payment_method = PaymentMethod::find($request->payment_method);
        $shipping_address = $user->addresses()->where('id', $request->shipping_address)->first();
        $billing_address = $user->addresses()->where('id', $request->billing_address)->first();
        if (!$shipping_method) return $this->withErrors('Invalid shipping method');
        if (!$payment_method) return $this->withErrors('Invalid payment method');
        if (!$shipping_address) return $this->withErrors('Invalid shipping address');

        $shipping_charge = $shipping_method->charge;
        if ($cart->is_free_shipping($shipping_method)) {
            $shipping_charge = 0;
        }

        $shop_commission_in_percentage = 100 - $shop->system_commission;
        $shop_commission = ($cart->gross_total * $shop_commission_in_percentage) / 100;
        $system_commission = ($cart->gross_total * $shop->system_commission) / 100;

        DB::beginTransaction();
        try {
            /* @var Order|null $order */
            $order = $user->orders()->create([
                'shop_id' => $shop->id,
                'track' => Order::generateTrack(),
                'coupon_id' => $cart->coupon_id,
                'coupon_code' => $cart->coupon_code,
                'discount' => $cart->coupon_discount,
                'shipping_method_id' => $shipping_method->id,
                'shipping_method_name' => $shipping_method->name,
                'shipping_charge' => $shipping_charge,
                'system_commission' => $system_commission,
                'shop_commission' => $shop_commission
            ]);
            if (!$order) throw new \Exception('Unable to create order');
            $line_item_params = $cart_items->map(function (CartItem $item) {
                $p = [
                    'product_id' => $item->product_id,
                    'variation_id' => $item->variation_id,
                    'product_title' => $item->product->title,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'tax' => $item->tax,
                    'attrs' => $item->attrs,
                ];
                return $p;
            })->toArray();
            $line_items = $order->items()->createMany($line_item_params);
            $order->addresses()->create($billing_address->only(['type', 'name', 'email', 'phone', 'country', 'state', 'city', 'street_address_1', 'street_address_2', 'latitude', 'longitude']));
            $order->addresses()->create($shipping_address->only(['type', 'name', 'email', 'phone', 'country', 'state', 'city', 'street_address_1', 'street_address_2', 'latitude', 'longitude']));
            $net_amount = $order->gross_total + $order->shipping_charge - $order->discount;
            $payment_charge = round(($payment_method->fixed_charge + (($net_amount * $payment_method->percent_charge) / 100)), 2);
            $gross_amount = $net_amount + $payment_charge;
            $payment = $order->payments()->create([
                'payment_method_id' => $payment_method->id,
                'payment_method_name' => $payment_method->name,
                'track' => Payment::generateTrack(),
                'net_amount' => $net_amount,
                'charge' => $payment_charge,
                'gross_amount' => $gross_amount,
                'status' => 0
            ]);
            if (!$payment) throw new \Exception('Payment not initialized');
        } catch (\Exception $exception) {
            return $this->withErrors($exception->getMessage());
        }
        DB::commit();
        return $this->withSuccess(route('payment', ['type' => 'order', 'ref' => $payment->track]));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Order $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        if (!$order instanceof Order) return abort(404);
        $user = Auth::guard('api')->user();
        if ($order->user_id != $user->id) throw new AuthorizationException();
        $order->load(['shop', 'items', 'payments', 'notes', 'reviews']);
        $order->setRelation('consignments', $order->consignments()->with(['delivery_man'])->whereIn('status', [1, 3, 4])->get());
        $order->append(['net_total', 'tax_total', 'gross_total']);
        return $this->withSuccess($order);
    }
}
