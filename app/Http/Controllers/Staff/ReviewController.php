<?php

namespace App\Http\Controllers\Staff;

use App\Models\Consignment;
use App\Models\DeliveryMan;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTableAbstract;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|void
     */
    public function index(Request $request)
    {
        $class = 'App\Models\Order';
        $types = ['shop' => 'App\Models\Shop', 'product' => 'App\Models\Product', 'delivery_man' => 'App\Models\DeliveryMan', 'order' => 'App\Models\Order', 
            'consignment' => 'App\Models\Consignment'];
        if (in_array($request->type, array_keys($types))) {
            $class = $types[$request->type];
        }
        if (!is_numeric($request->type_id)) {
            return redirect()->back()->withErrors('Bad request');
        }
        /* @var Shop|Product|Order|Consignment $instance */
        $instance = $class::findOrFail($request->type_id);
        if ($request->ajax()) {
            $query = $instance->reviews()->with('user');
            /* @var DataTableAbstract $table */
            $table = datatables()->of($query);
            $table->editColumn('user.name', function (Review $review) {
                return '<a href="' . route('staff.catalog.user.edit', $review->user_id) . '">' . $review->user->name . '</a>';
            });
            $table->editColumn('created_at', function (Review $review) {
                return optional($review->created_at)->format('d M, Y h:i a');
            });
            $table->editColumn('rating', function (Review $review) {
                return $review->rating_html;
            });
            $table->addColumn('actions', function (Review $review) {
                return '<button class="btn btn-outline-primary nimmu-btn nimmu-btn-outline-primary btn-view" data-rating="#rating-' . $review->id . '" data-content="' . $review->content . '"><i class="fa fa-eye"></i></button>';
            });
            $table->rawColumns(['rating', 'actions', 'user.name']);
            return $table->make(true);
        }
        $title = 'All Reviews';
        if ($instance instanceof Shop) {
            $title .= ' Of ' . $instance->name;
        } elseif ($instance instanceof Product) {
            $title .= ' Of ' . $instance->title;
        } elseif ($instance instanceof DeliveryMan) {
            $title .= ' Of ' . $instance->name;
        } elseif ($instance instanceof Order) {
            $title .= ' Of ' . sprintf('#%s %s', $instance->id, optional($instance->user)->name);
        } elseif ($instance instanceof Consignment) {
            $title .= ' Of Consignment #' . $instance->id . ' Of Order ' . sprintf('#%s %s', $instance->order->id, optional($instance->order->user)->name);
        }
        return view('staff.review.index', compact('instance', 'title'));
    }
}
