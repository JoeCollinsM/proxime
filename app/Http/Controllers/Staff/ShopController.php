<?php

namespace App\Http\Controllers\Staff;

use App\Models\Currency;
use App\Models\Shop;
use App\Models\ShopCategory;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTableAbstract;

class ShopController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * @throws \Exception
     */
    public function index(Request $request)
    {
        $q = Shop::query()->with('category');
        if ($request->ajax()) {
            /* @var DataTableAbstract $table */
            $table = datatables()->of($q);
            $table->editColumn('logo', function (Shop $shop) {
                return '<img src="' . $shop->logo . '" style="width: 20px;height: 20px;">';
            });
            $table->editColumn('name', function (Shop $shop) {
                return $shop->name . '<br>' . $shop->rating_html . '<a href="' . route('staff.catalog.review.index', ['type' => 'shop', 'type_id' => $shop->id]) . '">More...</a>';
            });
            $table->editColumn('status', function (Shop $shop) {
                $t = '';
                if ($shop->opening_status) {
                    $t .= '<span class="badge badge-success mr-2">Open</span>';
                } else {
                    $t .= '<span class="badge badge-warning mr-2">Close</span>';
                }
                if ($shop->status == 1) {
                    $t .= '<span class="badge badge-success mr-2">Active</span>';
                } elseif ($shop->status == 2) {
                    $t .= '<span class="badge badge-danger mr-2">Deactive</span>';
                } else {
                    $t .= '<span class="badge badge-warning mr-2">Pending</span>';
                }
                return $t;
            });
            $table->addColumn('actions', function (Shop $shop) {
                return '<a href="' . route('staff.shop.edit', $shop->id) . '" class="btn btn-warning btn-edit nimmu-btn nimmu-btn-warning"><i class="fa fa-edit"></i></a><button class="btn btn-danger btn-delete nimmu-btn nimmu-btn-danger" data-id="' . $shop->id . '"><i class="fa fa-trash"></i></button>';
            });
            $table->rawColumns(['name', 'logo', 'status', 'actions']);
            $table->filterColumn('category.name', function ($query, $keyword) {
                /* @var \Illuminate\Database\Eloquent\Builder $query */
                $query->whereHas('category', function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%$keyword%");
                });
            });
            return $table->make(true);
        }
        return view('staff.shop.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $categories = ShopCategory::query()->where('status', 1)->get();
        $currency = Currency::getDefaultCurrency();
        return view('staff.shop.create', compact('categories', 'currency'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'shop_category_id' => 'required|exists:shop_categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|slug|max:255|unique:shops,slug',
            'logo' => 'required|max:255',
            'cover' => 'required|max:255',
            'address' => 'required|max:255',
            'latitude' => 'required|numeric|max:255',
            'longitude' => 'required|numeric|max:255',
            'opening_at' => 'required|max:255|date_format:"h:i a"',
            'closing_at' => 'required|max:255|date_format:"h:i a"',
            'details' => 'nullable',
            'vendor_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:shops,email',
            'phone' => 'nullable|numeric|unique:shops,phone',
            'password' => 'required|min:8|confirmed',
            'status' => 'required|in:0,1,2',
            'minimum_order' => 'required|numeric',
            'system_commission' => 'required|numeric|min:0|max:100'
        ], [
            'shop_category_id.*' => 'You must select a valid shop category',
            'status' => 'Please select a valid shop status'
        ]);
        $params = $request->only(['shop_category_id', 'name', 'slug', 'logo', 'cover', 'address', 'latitude',
                                'longitude', 'opening_at', 'closing_at', 'details', 'vendor_name', 'email',
                                'phone', 'status', 'minimum_order', 'system_commission']);
        if ($request->minimum_order < 0) {
            $params['minimum_order'] = -1;
        }
        $params['password'] = Hash::make($request->password);

        DB::beginTransaction();
        try {
            $shop = Shop::create($params);
            if (!$shop) throw new \Exception('Unable to create shop');
            if (is_array($request->meta)) {
                $metas = [];
                foreach ($request->meta as $name => $content) {
                    $metas[] = ['name' => $name, 'content' => $content];
                }
                $shop->metas()->createMany($metas);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors('Unable to create new shop');
        }
        DB::commit();
        return redirect()->route('staff.shop.index')->withSuccess('Shop created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Shop $shop
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function edit(Shop $shop)
    {
        if (!$shop) return abort(404);
        $meta = $shop->metas()->pluck('content', 'name');
        $categories = ShopCategory::query()->where('status', 1)->get();
        $currency = Currency::getDefaultCurrency();
        return view('staff.shop.edit', compact('categories', 'shop', 'meta', 'currency'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Shop $shop
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|void
     */
    public function update(Request $request, Shop $shop)
    {
        if (!$shop) return abort(404);
        $request->validate([
            'shop_category_id' => 'required|exists:shop_categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|slug|max:255|unique:shops,slug,' . $shop->id,
            'logo' => 'required|max:255',
            'cover' => 'required|max:255',
            'address' => 'required|max:255',
            'latitude' => 'required|numeric|max:255',
            'longitude' => 'required|numeric|max:255',
            'opening_at' => 'required|max:255|date_format:"h:i a"',
            'closing_at' => 'required|max:255|date_format:"h:i a"',
            'details' => 'nullable',
            'vendor_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:shops,email,' . $shop->id,
            'phone' => 'nullable|numeric|unique:shops,phone,' . $shop->id,
            'password' => 'nullable|min:8|confirmed',
            'status' => 'required|in:0,1,2',
            'minimum_order' => 'required|numeric',
            'system_commission' => 'required|numeric|min:0|max:100'
        ], [
            'shop_category_id.*' => 'You must select a valid shop category',
            'status' => 'Please select a valid shop status'
        ]);
        $params = $request->only(['shop_category_id', 'name', 'slug', 'logo', 'cover', 'address', 'latitude', 'longitude', 'opening_at', 'closing_at', 'details', 'vendor_name', 'email', 'phone', 'status', 'minimum_order', 'system_commission']);
        if ($request->minimum_order < 0) {
            $params['minimum_order'] = -1;
        }
        if ($request->password) $params['password'] = Hash::make($request->password);

        DB::beginTransaction();
        try {
            $res = $shop->update($params);
            if (!$res) throw new \Exception('Unable to update shop');
            if (is_array($request->meta)) {
                $shop->metas()->delete();
                $metas = [];
                foreach ($request->meta as $name => $content) {
                    $metas[] = ['name' => $name, 'content' => $content];
                }
                $shop->metas()->createMany($metas);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors('Unable to update shop');
        }
        DB::commit();
        return redirect()->back()->withSuccess('Shop updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Shop $shop
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|void
     */
    public function destroy(Shop $shop)
    {
        if (!$shop) return abort(404);
        DB::beginTransaction();
        try {
            $shop->metas()->delete();
            $res = $shop->delete();
            if (!$res) throw new \Exception();
        } catch (QueryException $queryException) {
            DB::rollBack();
            return redirect()->back()->withErrors('Unable to delete the shop because of other relational data');
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors('Unable to delete the shop');
        }
        DB::commit();
        return redirect()->route('staff.shop.index')->withSuccess('Shop deleted successfully');
    }
}
