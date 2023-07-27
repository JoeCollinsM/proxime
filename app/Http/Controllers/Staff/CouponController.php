<?php

namespace App\Http\Controllers\Staff;

use App\Models\Coupon;
use App\Models\Currency;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTableAbstract;

class CouponController
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
        $currency = Currency::getDefaultCurrency();
        if ($request->ajax()) {
            $query = Coupon::query();
            /* @var DataTableAbstract $table */
            $table = datatables()->of($query);
            $table->editColumn('start_at', function (Coupon $coupon) {
                return optional($coupon->start_at)->format('d M, Y');
            });
            $table->editColumn('expire_at', function (Coupon $coupon) {
                return optional($coupon->expire_at)->format('d M, Y');
            });
            $table->editColumn('min', function (Coupon $coupon) use ($currency) {
                if ($coupon->min == -1) {
                    return sprintf('<span class="badge badge-info">Any Amount</span>');
                }
                return sprintf('<span class="badge badge-info">%s %s</span>', $coupon->min, $currency->code);
            });
            $table->editColumn('amount', function (Coupon $coupon) use ($currency) {
                if ($coupon->discount_type == 1) {
                    $r = '<span class="badge badge-info">' . $coupon->amount . '%';
                    if ($coupon->upto != -1) {
                        $r .= ' upto ' . $coupon->upto . ' ' . $currency->code;
                    }
                    $r .= '</span>';
                    return $r;
                }
                return sprintf('<span class="badge badge-info">%s %s</span>', $coupon->amount, $currency->code);
            });
            $table->addColumn('status', function (Coupon $coupon) {
                return $coupon->status == 1 ? '<span class="badge badge-success">Active</span>' : ($coupon->status == 0 ? '<span class="badge badge-danger">Expired</span>' : '<span class="badge badge-warning">Upcoming</span>');
            });
            $table->addColumn('actions', function (Coupon $coupon) {
                return '<button
                            class="btn btn-warning btn-edit"
                            data-id="' . $coupon->id . '"
                            data-code="' . $coupon->code . '"
                            data-start_at="' . optional($coupon->start_at)->format('d-m-Y') . '"
                            data-expire_at="' . optional($coupon->expire_at)->format('d-m-Y') . '"
                            data-customers="' . $coupon->users()->pluck('users.id') . '"
                            data-products="' . $coupon->products()->pluck('products.id') . '"
                            data-min="' . $coupon->min . '"
                            data-upto="' . $coupon->upto . '"
                            data-discount_type="' . $coupon->discount_type . '"
                            data-maximum_use_limit="' . $coupon->maximum_use_limit . '"
                            data-amount="' . $coupon->amount . '">
                            <i class="fa fa-edit"></i>
                </button><button
                            class="btn btn-danger btn-delete" data-id="' . $coupon->id . '"><i class="fa fa-trash"></i></button>';
            });
            $table->rawColumns(['min', 'amount', 'status', 'actions']);
            return $table->make(true);
        }
        $customers = User::all();
        $products = Product::all();

        return view('staff.coupon.index', compact('customers', 'products', 'currency'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code',
            'start_at' => 'required|date_format:d-m-Y',
            'expire_at' => 'required|date_format:d-m-Y',
            'customers.*' => 'nullable|exists:users,id',
            'products.*' => 'nullable|exists:products,id',
            'min' => 'nullable|numeric',
            'maximum_use_limit' => 'nullable|numeric',
            'upto' => 'nullable|numeric',
            'discount_type' => 'required|in:1,2',
            'amount' => 'required|numeric',
        ]);

        /** @var Coupon $coupon */
        $data = $request->only(['code', 'start_at', 'expire_at', 'min', 'upto', 'discount_type', 'amount', 'maximum_use_limit']);

        if (!$request->min) {
            $data['min'] = -1;
        }
        if (!$request->maximum_use_limit) {
            $data['maximum_use_limit'] = -1;
        }
        if (!$request->upto) {
            $data['upto'] = -1;
        }

        DB::beginTransaction();
        try {
            /** @var Coupon $coupon */
            $coupon = Coupon::create($data);
            if (!$coupon) throw new \Exception('Unable to create coupon');
            if (is_array($request->customers)) {
                $coupon->users()->attach($request->customers);
            }
            if (is_array($request->products)) {
                $coupon->products()->attach($request->products);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors($exception->getMessage());
        }
        DB::commit();
        return redirect()->back()->withSuccess('Coupon added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Coupon $coupon
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function show(Coupon $coupon)
    {
        if (!$coupon) return abort(404);

        return view('staff.coupon.show', compact('coupon'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Coupon $coupon
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function update(Request $request, Coupon $coupon)
    {
        if (!$coupon instanceof Coupon) return abort(404);

        $request->validate([
            'code' => 'required|unique:coupons,code,' . $coupon->id,
            'start_at' => 'required|date_format:d-m-Y',
            'expire_at' => 'required|date_format:d-m-Y',
            'customers.*' => 'nullable|exists:users,id',
            'products.*' => 'nullable|exists:products,id',
            'min' => 'nullable|numeric',
            'maximum_use_limit' => 'nullable|numeric',
            'upto' => 'nullable|numeric',
            'discount_type' => 'required|in:1,2',
            'amount' => 'required|numeric',
        ]);

        /** @var Coupon $coupon */
        $data = $request->only(['code', 'start_at', 'expire_at', 'upto', 'min', 'discount_type', 'amount', 'maximum_use_limit']);

        if (!$request->min) {
            $data['min'] = -1;
        }
        if (!$request->maximum_use_limit) {
            $data['maximum_use_limit'] = -1;
        }
        if (!$request->upto) {
            $data['upto'] = -1;
        }

        DB::beginTransaction();
        try {
            $res = $coupon->update($data);
            if (!$res) throw new \Exception('Unable to update coupon');

            $coupon->users()->detach();
            if (is_array($request->customers)) {
                $coupon->users()->attach($request->customers);
            }
            $coupon->products()->detach();
            if (is_array($request->products)) {
                $coupon->products()->attach($request->products);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors($exception->getMessage());
        }
        DB::commit();
        return redirect()->back()->withSuccess('Coupon updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Coupon $coupon
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|void
     */
    public function destroy(Coupon $coupon)
    {
        if (!$coupon) return abort(404);
        try {
            $r = $coupon->delete();
            if (!$r) throw new \Exception('Unable to delete coupon');
        } catch (\Exception $exception) {
            return redirect()->back()->withInput()->withErrors($exception->getMessage());
        }
        return redirect()->back()->withSuccess('Coupon deleted successfully');
    }
}
