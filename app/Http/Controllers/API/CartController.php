<?php

namespace App\Http\Controllers\API;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Currency;
use App\Helpers\API\Context;
use App\Helpers\API\Formatter;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CartController
{
    use Formatter;

    function index()
    {
        $this->updateCartPrices();
        /* @var User $user */
        $user = Auth::guard('api')->user();
        $cart = $user->carts()->where('type', 'cart')->firstOrCreate([
            'type' => 'cart',
            'currency_code' => Context::getCurrency()
        ]);
        if ($cart) {
            $cart->load('items');
        }
        return $this->withSuccess($cart);
    }

    function attachCoupon(Request $request)
    {
        $this->updateCartPrices();
        /* @var User $user */
        $user = Auth::guard('api')->user();
        /* @var Cart|null $cart */
        $cart = $user->carts()->where('type', 'cart')->firstOrFail();

        $request->validate([
            'code' => 'required|exists:coupons,code'
        ]);
        /* @var Coupon $coupon */
        $coupon = Coupon::query()->where('code', $request->code)->first();

        if (!Carbon::now()->between($coupon->start_at, $coupon->expire_at)) {
            return $this->withErrors('Coupon expired');
        }

        if ($coupon->maximum_use_limit != -1) {
            $used_by_current_user = $coupon->orders()->where('user_id', $user->id)->count();
            if ($coupon->maximum_use_limit <= $used_by_current_user) return false;
        }

        if ($coupon->min != -1) {
            if ($cart->net_total < $coupon->min) {
                return $this->withErrors('Not enough cart amount for this coupon');
            }
        }

        if ($coupon->products()->count()) {
            $intersection = $cart->items()->pluck('product_id')->intersect($coupon->products()->pluck('products.id'));
            if (!$intersection->count()) {
                return $this->withErrors('Unable to find related product in cart');
            }
        }

        if ($coupon->users()->count()) {
            if (!$coupon->users()->pluck('users.id')->contains($user->id)) {
                return $this->withErrors('This coupon isn\'t allowed for this customer');
            }
        }

        $cart->update([
            'coupon_id' => $coupon->id,
            'coupon_code' => $coupon->code,
            'coupon_discount' => $this->calculateCouponDiscount($cart, $coupon)
        ]);

        return $this->withSuccess($user->carts()->with('items')->where('type', 'cart')->first());

    }

    function detachCoupon(Request $request)
    {
        $this->updateCartPrices();
        /* @var User $user */
        $user = Auth::guard('api')->user();
        /* @var Cart|null $cart */
        $cart = $user->carts()->where('type', 'cart')->firstOrFail();

        if (!$cart->coupon) return abort(404);

        $cart->update([
            'coupon_id' => null,
            'coupon_code' => null,
            'coupon_discount' => 0
        ]);

        return $this->withSuccess($user->carts()->with('items')->where('type', 'cart')->first());

    }

    function store(Request $request)
    {
        $this->updateCartPrices();
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|numeric|min:1',
            'attrs' => 'nullable|array',
        ]);
        /* @var User $user */
        $user = Auth::guard('api')->user();
        /* @var Product $product */
        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity ?? 1;

        $cart = $user->carts()->where('type', 'cart')->firstOrCreate([
            'type' => 'cart',
            'currency_code' => Context::getCurrency()
        ]);
        if (!$cart instanceof \App\Cart) return $this->withErrors('Unable to create new cart instance');

        if ($cart->items()->count()) {
            /* @var CartItem $firstItem */
            $firstItem = $cart->items()->first();
            if ($firstItem->product->shop_id != $product->shop_id) 
            return $this->withErrors('You can\'t add products to cart from different stores');
        }

        if ($product->type == 'variable') {
            $att = is_array($request->attrs) ? $request->attrs : [];
            $rules = $product->attrs()->pluck('content', 'name')->map(function ($content) {
                return 'required|in:' . implode(',', $content);
            })->toArray();
            Validator::make($att, $rules)->validate();
            $variationQuery = $product->variations();
            foreach (array_keys($rules) as $attribute_name) {
                $attribute_value = $att[$attribute_name];
                $variationQuery->whereHas('attrs', function ($q) use ($attribute_name, $attribute_value) {
                    /* @var Builder $q */
                    $q->where('name', $attribute_name)->where('content', $attribute_value);
                });
            }
            /* @var Product $variant */
            $variant = $variationQuery->firstOrFail();
            if (!$variant->isAvailable($quantity)) {
                return $this->withErrors('Product not available enough in stock');
            }
            $variation_id = $variant->id;
            $price = convert_currency($variant->sale_price, Context::getCurrency(), Currency::getDefaultCurrency());
            $tax = convert_currency($variant->tax, Context::getCurrency(), Currency::getDefaultCurrency());
        } else {
            if (!$product->isAvailable($quantity)) {
                return $this->withErrors('Product not available enough in stock');
            }
            $price = convert_currency($product->sale_price, Context::getCurrency(), Currency::getDefaultCurrency());
            $tax = convert_currency($product->tax, Context::getCurrency(), Currency::getDefaultCurrency());
            $variation_id = null;
        }
        DB::beginTransaction();
        try {
            $cartItem = $cart->items()->where('product_id', $product->id)->where('variation_id', $variation_id)->first();
            if ($cartItem) {
                $updateQuantity = $cartItem->quantity + $quantity;
                if (isset($variant) && $variant instanceof Product) {
                    if (!$variant->isAvailable($updateQuantity)) {
                        return $this->withErrors('Product not available enough in stock');
                    }
                } else {
                    if (!$product->isAvailable($updateQuantity)) {
                        return $this->withErrors('Product not available enough in stock');
                    }
                }
                $res = $cartItem->update([
                    'quantity' => $updateQuantity
                ]);
                if (!$res) throw new \Exception('Unable to update cart item');
            } else {
                $cartItem = $cart->items()->create([
                    'product_id' => $product->id,
                    'variation_id' => $variation_id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'tax' => $tax,
                    'attrs' => $request->input('attrs')
                ]);
                if (!$cartItem instanceof CartItem) throw new \Exception('Unable to add the product to cart');
            }
            $this->updateCartPrices();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->withErrors($exception->getMessage());
        }
        DB::commit();
        $cart = $user->carts()->where('type', 'cart')->first();
        $cart->load('items');
        return $this->withSuccess($cart);
    }

    function update(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:1'
        ]);
        $this->updateCartPrices();
        if (!$cartItem instanceof CartItem) return abort(404);
        /* @var User $user */
        $user = Auth::guard('api')->user();
        if ($user->id != $cartItem->cart->user_id) throw new AuthorizationException;
        /* @var Product $product */
        $product = $cartItem->variant ?? $cartItem->product;
        $quantity = $request->quantity ?? $cartItem->quantity;
        if (!$product->isAvailable($quantity)) {
            return $this->withErrors('Product is not available enough in stock');
        }
        DB::beginTransaction();
        try {
            $res = $cartItem->update([
                'quantity' => $quantity
            ]);
            if (!$res) throw new \Exception('Unable to update cart item');
            $this->updateCartPrices();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->withErrors($exception->getMessage());
        }
        DB::commit();
        $cart = $user->carts()->where('type', 'cart')->first();
        $cart->load('items');
        return $this->withSuccess($cart);
    }

    function destroy(Request $request, CartItem $cartItem)
    {
        if (!$cartItem instanceof CartItem) return abort(404);
        /* @var User $user */
        $user = Auth::guard('api')->user();
        if ($user->id != $cartItem->cart->user_id) throw new AuthorizationException;
        DB::beginTransaction();
        try {
            $res = $cartItem->delete();
            if (!$res) throw new \Exception('Unable to delete cart item');
            $this->updateCartPrices();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->withErrors($exception->getMessage());
        }
        DB::commit();
        $cart = $user->carts()->where('type', 'cart')->first();
        $cart->load('items');
        return $this->withSuccess($cart);
    }

    private function calculateCouponDiscount(Cart $cart, Coupon $coupon)
    {
        if ($coupon->discount_type == 2) return $coupon->amount;
        $on = $cart->net_total;
        $couponProducts = $coupon->products()->pluck('products.id')->toArray();
        if (count($couponProducts)) {
            $on = 0;
            /* @var CartItem $item */
            foreach ($cart->items as $item) {
                if (in_array($item['product_id'], $couponProducts)) {
                    $on += $item['price'] * $item['quantity'];
                }
            }
        }
        $discount = ($on * $coupon->amount) / 100;
        if ($coupon->upto != -1 && is_numeric($coupon->upto)) {
            if ($discount > $coupon->upto) {
                $discount = $coupon->upto;
            }
        }
        return round($discount, 2);
    }

    public function updateCartPrices()
    {
        /* @var User $user */
        $user = Auth::guard('api')->user();
        /* @var \App\Cart|null $cart */
        $cart = $user->carts()->where('type', 'cart')->first();
        if (!$cart instanceof Cart) return;
        $currency = Context::getCurrency();
        if ($cart->currency_code != $currency) {
            /* @var CartItem $item */
            foreach ($cart->items as $item) {
                $item->update([
                    'price' => convert_currency($item->price, $currency, $cart->currency_code),
                    'tax' => convert_currency($item->tax, $currency, $cart->currency_code)
                ]);
            }
            $cart->update([
                'currency_code' => $currency,
            ]);
        }
        if ($cart->coupon instanceof Coupon) {
            $discountAmount = $this->calculateCouponDiscount($cart, $cart->coupon);
            if ($cart->coupon_discount != $discountAmount) {
                $cart->update([
                    'coupon_discount' => $discountAmount
                ]);
            }
        }
    }
}
