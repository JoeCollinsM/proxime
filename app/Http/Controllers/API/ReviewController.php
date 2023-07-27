<?php

namespace App\Http\Controllers\API;

use App\Models\DeliveryMan;
use App\Helpers\API\Formatter;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController
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
        $query = $user->reviews();

        if ($request->reviewable_type == 'order') {
            /* @var Order $reviewable */
            $reviewable = Order::query()->where('user_id', $user->id)->where('id', $request->reviewable_id)->firstOrFail();
            $query->where('reviewable_type', Order::class)->where('reviewable_id', $reviewable->id);
        } elseif ($request->reviewable_type == 'shop') {
            /* @var Shop $reviewable */
            $reviewable = Shop::query()->findOrFail($request->reviewable_id);
            $query = $reviewable->reviews();
        } elseif ($request->reviewable_type == 'product') {
            /* @var Product $reviewable */
            $reviewable = Product::query()->findOrFail($request->reviewable_id);
            $query = $reviewable->reviews();
        } elseif ($request->reviewable_type == 'delivery_man') {
            /* @var DeliveryMan $reviewable */
            $reviewable = DeliveryMan::query()->findOrFail($request->reviewable_id);
            $query = $reviewable->reviews();
        }

        $query->with(['user' => function ($q) {
            $q->select(['id', 'name', 'avatar']);
        }]);

        if (is_numeric($request->star)) {
            $query->where('reviews.rating', $request->star);
        }

        if ($request->order_by && $request->order) {
            $query->orderBy($request->order_by, $request->order);
        }

        if (is_numeric($request->get('paginate')) && $request->get('paginate') == 0) {
            if ($request->limit) {
                $query->take($request->limit);
            }
            $reviews = $query->get();
        } else {
            $reviews = $query->paginate($request->limit);
        }

        return $this->withSuccess($reviews);
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
        $request->validate([
            'reviewable_type' => 'required|in:order,shop,product,delivery_man',
            'reviewable_id' => 'required|numeric',
            'rating' => 'required|numeric|min:0|max:5',
            'content' => 'nullable',
        ]);
        if ($request->reviewable_type == 'order') {
            /* @var Order $reviewable */
            $reviewable = Order::query()->where('user_id', $user->id)->where('id', $request->reviewable_id)->firstOrFail();
            $query = $reviewable->reviews();
        } elseif ($request->reviewable_type == 'shop') {
            /* @var Shop $reviewable */
            $reviewable = Shop::query()->findOrFail($request->reviewable_id);
            $query = $reviewable->reviews();
        } elseif ($request->reviewable_type == 'product') {
            /* @var Product $reviewable */
            $reviewable = Product::query()->findOrFail($request->reviewable_id);
            $query = $reviewable->reviews();
        } elseif ($request->reviewable_type == 'delivery_man') {
            /* @var DeliveryMan $reviewable */
            $reviewable = DeliveryMan::query()->findOrFail($request->reviewable_id);
            $query = $reviewable->reviews();
        }
        try {
            /* @var Review $review */
            $review = $query->create([
                'user_id' => $user->id,
                'rating' => $request->rating,
                'content' => $request->input('content')
            ]);
            if (!$review) throw new \Exception('Unable to create review');
        } catch (\Exception $exception) {
            return $this->withErrors($exception->getMessage());
        }
        $review->refresh();
        return $this->withSuccess($review);
    }
}
