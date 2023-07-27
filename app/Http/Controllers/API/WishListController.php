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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class WishListController
{
    use Formatter;

    function index(Request $request)
    {
        $this->updateCartPrices();
        /* @var User $user */
        $user = Auth::guard('api')->user();
        /* @var Cart $cart */
        $cart = $user->carts()->where('type', 'wishlist')->firstOrCreate([
            'type' => 'wishlist',
            'currency_code' => Context::getCurrency()
        ]);
        if ($cart) {
            if ($request->only_product) {
                return $this->withSuccess($cart->items()->pluck('product_id'));
            }
            $cart->load('items');
        }
        return $this->withSuccess($cart);
    }

    function store(Request $request)
    {
        $this->updateCartPrices();
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|numeric',
            'attrs' => 'nullable|array',
        ]);
        /* @var User $user */
        $user = Auth::guard('api')->user();
        /* @var Product $product */
        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity ?? 1;
        $cart = $user->carts()->where('type', 'wishlist')->firstOrCreate([
            'type' => 'wishlist',
            'currency_code' => Context::getCurrency()
        ]);
        if (!$cart instanceof \App\Cart) throw ValidationException::withMessages(['error' => 'Unable to create new wishlist instance']);

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
//                    $q->whereRaw("LOWER(name)=?", strtolower($attribute_name))->whereRaw("LOWER(content)=?", strtolower($attribute_value));
                    $q->where('name', $attribute_name)->where('content', $attribute_value);
                });
            }
            /* @var Product $variant */
            $variant = $variationQuery->firstOrFail();
            $variation_id = $variant->id;
            $price = convert_currency($variant->sale_price, Context::getCurrency(), Currency::getDefaultCurrency());
            $tax = convert_currency($variant->tax, Context::getCurrency(), Currency::getDefaultCurrency());
        } else {
            $price = convert_currency($product->sale_price, Context::getCurrency(), Currency::getDefaultCurrency());
            $tax = convert_currency($product->tax, Context::getCurrency(), Currency::getDefaultCurrency());
            $variation_id = null;
        }
        $cartItem = $cart->items()->where('product_id', $product->id)->where('variation_id', $variation_id)->first();
        if ($cartItem) {
            throw ValidationException::withMessages(['product_id' => 'Product or it\'s variation already exists']);
        }

        DB::beginTransaction();
        try {
            $cartItem = $cart->items()->create([
                'product_id' => $product->id,
                'variation_id' => $variation_id,
                'quantity' => $quantity,
                'price' => $price,
                'tax' => $tax,
                'attrs' => $request->input('attrs')
            ]);
            if (!$cartItem instanceof CartItem) throw new \Exception('Unable to add the product to wishlist');

            $this->updateCartPrices();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->withErrors($exception->getMessage());
        }
        DB::commit();
        $cart = $user->carts()->where('type', 'wishlist')->first();
        $cart->load('items');
        return $this->withSuccess($cart);
    }

    function update(Request $request, CartItem $cartItem)
    {
        $this->updateCartPrices();
        if (!$cartItem instanceof CartItem) return abort(404);
        /* @var User $user */
        $user = Auth::guard('api')->user();
        if ($user->id != $cartItem->cart->user_id) throw new AuthorizationException;
        /* @var Product $product */
        $product = $cartItem->variant ?? $cartItem->product;
        $quantity = $request->quantity ?? $cartItem->quantity;
        DB::beginTransaction();
        try {
            $res = $cartItem->update([
                'quantity' => $quantity
            ]);
            if (!$res) throw new \Exception('Unable to update wishlist item');
            $this->updateCartPrices();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->withErrors($exception->getMessage());
        }
        DB::commit();
        $cart = $user->carts()->where('type', 'wishlist')->first();
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
            if (!$res) throw new \Exception('Unable to delete wishlist item');
            $this->updateCartPrices();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->withErrors($exception->getMessage());
        }
        DB::commit();
        $cart = $user->carts()->where('type', 'wishlist')->first();
        $cart->load('items');
        return $this->withSuccess($cart);
    }

    private function updateCartPrices()
    {
        /* @var User $user */
        $user = Auth::guard('api')->user();
        /* @var \App\Cart|null $cart */
        $cart = $user->carts()->where('type', 'wishlist')->first();
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
    }
}
