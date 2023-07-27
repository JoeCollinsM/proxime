<?php

namespace App\Http\Controllers\Staff;

use App\Models\Currency;
use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTableAbstract;

class ShippingMethodController extends Controller
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
            $query = ShippingMethod::query();
            /* @var DataTableAbstract $table */
            $table = datatables()->of($query);
            $table->addColumn('charge', function (ShippingMethod $shippingMethod) use ($currency) {
                return sprintf('<span class="badge badge-info">%s %s</span>', $shippingMethod->charge, $currency->code);
            });
            $table->editColumn('status', function (ShippingMethod $shippingMethod) use ($currency) {
                return $shippingMethod->status == 1 ? '<span class="badge badge-success">Enabled</span>' : '<span class="badge badge-danger">Disabled</span>';
            });
            $table->addColumn('actions', function (ShippingMethod $shippingMethod) {
                return '<button class="btn btn-warning btn-edit nimmu-btn nimmu-btn-warning"
                                                            data-id="' . $shippingMethod->id . '"
                                                            data-name="' . $shippingMethod->name . '"
                                                            data-description="' . $shippingMethod->description . '"
                                                            data-charge="' . $shippingMethod->charge . '"
                                                            data-status="' . $shippingMethod->status . '"><i
                                                            class="fa fa-edit"></i></button>';
            });
            $table->rawColumns(['charge', 'status', 'actions']);
            return $table->make(true);
        }
        return view('staff.setting.shipping-method', compact('currency'));
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
            'name' => 'required|max:191',
            'description' => 'required',
            'charge' => 'required|numeric|min:0',
            'status' => 'nullable|in:0,1',
        ]);
        $p = $request->only(['name', 'description', 'charge', 'status']);
        if ($request->status != 1) {
            $p['status'] = 0;
        }
        $shippingMethod = ShippingMethod::create($p);
        if (!$shippingMethod) return redirect()->back()->withInput()->withErrors('Unable to create shipping method');
        return redirect()->back()->withSuccess('Shipping method created successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\ShippingMethod $shippingMethod
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ShippingMethod $shippingMethod)
    {
        if (!$shippingMethod) return abort(404);
        $request->validate([
            'name' => 'required|max:191',
            'description' => 'required',
            'charge' => 'required|numeric|min:0',
            'status' => 'nullable|in:0,1',
        ]);
        $p = $request->only(['name', 'description', 'charge', 'status']);
        if ($request->status != 1) {
            $p['status'] = 0;
        }
        $res = $shippingMethod->update($p);
        if (!$res) return redirect()->back()->withErrors('Unable to update shipping method');
        return redirect()->back()->withSuccess('Shipping method updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\ShippingMethod $shippingMethod
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(ShippingMethod $shippingMethod)
    {
        if (!$shippingMethod) return abort(404);
        $res = $shippingMethod->delete();
        if (!$res) return redirect()->back()->withErrors('Unable to delete shipping method');
        return redirect()->back()->withSuccess('Shipping method deleted successfully');
    }
}
