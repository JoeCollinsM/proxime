<?php

namespace App\Http\Controllers\API;

use App\Models\Attribute;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Helpers\API\Context;
use App\Helpers\API\Formatter;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\Shop;
use App\Models\ShopCategory;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CatalogController
{
    use Formatter;

    public function attributes()
    {
        return $this->withSuccess(Attribute::query()->with('terms')->get());
    }

    public function categories(Request $request)
    {
        $query = Category::query();

        // Write Other Filter here
        if ($request->keyword) {
            $s = $request->keyword;
            $query->where('name', 'LIKE', "%$s%");
        }
        if ($request->tag) {
            $e = config('catalog.category.' . $request->tag, []);
            if (count($e)) {
                $query->whereIn('id', $e);
            }
        }
        if ($request->limit) {
            $query->take($request->limit);
        }
        return $this->withSuccess($query->get());
    }

    public function shopCategories(Request $request)
    {
        $query = ShopCategory::query();

        // Write Other Filter here
        if ($request->keyword) {
            $s = $request->keyword;
            $query->where('name', 'LIKE', "%$s%");
        }
        if ($request->limit) {
            $query->take($request->limit);
        }
        return $this->withSuccess($query->get());
    }

    public function followShop(Request $request, Shop $shop)
    {
        if (!$shop) return abort(404);
        /* @var User $customer */
        $customer = $request->user('api');
        try {
            DB::beginTransaction();
            $exists = $customer->followings()->where('shops.id', $shop->id)->exists();
            if (!$exists) {
                $customer->followings()->attach($shop->id);
            }
            DB::commit();
            return $this->withSuccess(['success' => true]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->withErrors($exception->getMessage());
        }
    }

    public function unfollowShop(Request $request, Shop $shop)
    {
        if (!$shop) return abort(404);
        /* @var User $customer */
        $customer = $request->user('api');
        try {
            DB::beginTransaction();
            $exists = $customer->followings()->where('shops.id', $shop->id)->exists();
            if ($exists) {
                $customer->followings()->detach($shop->id);
            }
            DB::commit();
            return $this->withSuccess(['success' => true]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->withErrors($exception->getMessage());
        }
    }

    public function followedShop(Request $request)
    {
        /* @var User $customer */
        $customer = $request->user('api');
        $query = $customer->followings();

        if ($request->limit) {
            $query->take($request->limit);
        }
        $shops = $query->get()->map(function (Shop $shop) {
            $shop->makeHidden(['pivot']);
            $shop->append(['opening_status', 'star']);
            return $shop;
        });
        return $this->withSuccess($shops);
    }

    public function shops(Request $request)
    {
        $query = Shop::query();

        // Write Other Filter Here
        if ($request->keyword) {
            $s = $request->keyword;
            $query->where('name', 'LIKE', "%$s%");
        }
        if ($request->category_id) {
            $cats = $request->category_id;
            if (!is_array($cats)) {
                $cats = [$cats];
            }
            $query->whereIn('shop_category_id', $cats);
        }
        if ($request->limit) {
            $query->take($request->limit);
        }
        $shops = $query->get()->map(function (Shop $shop) {
            $shop->append(['opening_status', 'star']);
            return $shop;
        });
        return $this->withSuccess($shops);
    }

    public function nearbyShops(Request $request)
    {
        $query = Shop::query();

        // Write Other Filter Here

        $gr_circle_radius = 6371;
        $max_distance = 500;
        $lat = $request->latitude;
        $long = $request->longitude;

        $distance_select = sprintf(
            "
                                    ( %d * acos( cos( radians(%s) ) " .
            " * cos( radians( latitude ) ) " .
            " * cos( radians( longitude ) - radians(%s) ) " .
            " + sin( radians(%s) ) * sin( radians( latitude ) ) " .
            " ) " .
            ")
                                     ",
            $gr_circle_radius,
            $lat,
            $long,
            $lat
        );


        $query->select('*')->where(DB::raw($distance_select), '<=', $max_distance);

        if ($request->limit) {
            $query->take($request->limit);
        }
        $shops = $query->get()->map(function (Shop $shop) {
            $shop->append(['opening_status', 'star']);
            return $shop;
        });
        return $this->withSuccess($shops);
    }

    public function shop(Shop $shop)
    {
        if (!$shop) return abort(404);
        $shop->append(['opening_status', 'star', 'is_following']);
        return $this->withSuccess($shop);
    }

    public function searchSuggestions(Request $request)
    {
        $featured_tag_ids = get_option('featured_tags', null);
        if ($featured_tag_ids) {
            $featured_tag_ids = json_decode($featured_tag_ids, true);
            if (!is_array($featured_tag_ids)) $featured_tag_ids = [];
        } else {
            $featured_tag_ids = [];
        }
        if (count($featured_tag_ids)) {
            $tag_names = Tag::query()->whereIn('id', $featured_tag_ids)->pluck('tags.name');
            return $this->withSuccess($tag_names);
        }
        return $this->withSuccess([]);
    }

    public function products(Request $request)
    {
        $query = Product::query();

        if ($request->free_shipping) {
            $query->where('is_free_shipping', 1);
        }

        if ($request->discount) {
            $query->whereRaw('general_price>sale_price');
        }

        if ($request->shop_open) {
            $query->whereHas('shop', function ($q) {
                /* @var Builder $q */
                $now = Carbon::now();
                $q->whereTime('opening_at', '<=', $now)->whereTime('closing_at', '>=', $now);
            });
        }

        // Write Other Filter Here
        if ($request->shop_id) {
            $i = $request->shop_id;
            $query->where('shop_id', $i);
        }
        if ($request->category_id) {
            $cats = $request->category_id;
            if (!is_array($cats)) {
                $cats = [$cats];
            }
            $query->whereIn('category_id', $cats);
        }

        if ($request->keyword) {
            $s = $request->keyword;
            $query->where(function ($q) use ($s) {
                /* @var Builder $q */
                $q->where('title', 'LIKE', "%$s%");
            });
        }

        if ($request->get('attributes')) {
            $attrs = json_decode($request->get('attributes'), true);
            if (is_array($attrs)) {
                $query->where(function ($q) use ($attrs) {
                    /* @var Builder $q */
                    $q->whereHas('variations', function ($q2) use ($attrs) {
                        /* @var Builder $q2 */
                        $q2->whereHas('attrs', function ($q3) use ($attrs) {
                            /* @var Builder $q3 */
                            foreach ($attrs as $attr_name => $attr_value) {
                                $q3->where('name', $attr_name)->where('content', $attr_value);
                            }
                        });
                    });
                });
            }
        }

        if ($request->tag) {
            $t = $request->tag;
            if (!is_array($t)) {
                $t = [$t];
            }
            $query->whereHas('tags', function ($q) use ($t) {
                /* @var Builder $q */
                $q->whereIn('tags.name', $t);
            });
        }

        if ($request->order_by && $request->order) {
            if ($request->order_by == 'average_rating') {
                $query->withCount(['reviews as average_rating' => function ($q) {
                    /* @var Builder $q */
                    $q->selectRaw('coalesce(avg(rating),0)');
                }]);
            }
            $query->orderBy($request->order_by, $request->order);
        }

        if (is_numeric($request->get('paginate')) && $request->get('paginate') == 0) {
            if ($request->limit) {
                $query->take($request->limit);
            }
            $products = $query->get();
        } else {
            $products = $query->paginate($request->limit);
        }
        return $this->withSuccess($products);
    }

    public function product(Product $product)
    {
        if (!$product) return abort(404);
        try {
            $product->update([
                'views' => ($product->views + 1)
            ]);
            $product->refresh();
        } catch (\Exception $exception) {
        }
        $product->load(['variations', 'attrs' => function($q) {
            $q->with('attribute');
        }]);
        $product->setRelation('attrs', $product->attrs->map(function ($attr) {
            $attr->terms = [];
            if ($attr->attribute instanceof Attribute) {
                if (is_array($attr->content)) {
                    $attr->terms = $attr->attribute->terms()->whereIn('slug', $attr->content)->get();
                }
            }
            return $attr;
        }));
        return $this->withSuccess($product);
    }

    public function getVariantByAttrs(Request $request, Product $product)
    {
        if (!$product) return abort(404);
        $rules = $product->attrs()->pluck('content', 'name')->map(function ($content) {
            return 'required|in:' . implode(',', $content);
        })->toArray();
        /* All Attributes Are Required To Find Variant*/
        $request->validate($rules);
        $att = $request->all();
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
        return $this->withSuccess($variant);
    }

    public function shippingMethods(Request $request)
    {
        /* @var User $user */
        $user = Auth::guard('api')->user();
        $methods = ShippingMethod::all();
        /* @var Cart $cart */
        $cart = $user->carts()->where('type', 'cart')->first();
        if ($cart) {
            $methods->map(function ($method) use ($cart) {
                if ($cart->is_free_shipping($method)) {
                    $method->charge = 0;
                }
                return $method;
            });
        }
        return $this->withSuccess($methods);
    }

    public function paymentMethods(Request $request)
    {
        /* @var User $user */
        $user = Auth::guard('api')->user();
        /* @var Cart $cart */
        $cart = $user->carts()->where('type', 'cart')->first();
        $payable_total = $cart->gross_total;
        $methods = PaymentMethod::query()->where(function ($q1) use ($payable_total) {
            /* @var Builder $q1 */
            $q1->where('min', -1)->orWhere('min', '<=', $payable_total);
        })->where(function ($q2) use ($payable_total) {
            /* @var Builder $q2 */
            $q2->where('max', -1)->orWhere('max', '>=', $payable_total);
        })->get(['id', 'name', 'description']);
        return $this->withSuccess($methods);
    }
}
