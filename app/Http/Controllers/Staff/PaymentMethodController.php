<?php

namespace App\Http\Controllers\Staff;

use App\Models\Currency;
use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTableAbstract;

class PaymentMethodController extends Controller
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
        $query = PaymentMethod::query();
        if (!(clone $query)->count()) {
            // Insert Into table
            PaymentMethod::generateMethods();
        }
        if ($request->ajax()) {
            /* @var DataTableAbstract $table */
            $table = datatables()->of($query);
            $table->editColumn('min', function (PaymentMethod $paymentMethod) use ($currency) {
                return $paymentMethod->min == -1 ? '<span class="badge badge-info">no limit</span>' : '<span class="badge badge-info">' . $paymentMethod->min . ' ' . $currency->code . '</span>';
            });
            $table->editColumn('max', function (PaymentMethod $paymentMethod) use ($currency) {
                return $paymentMethod->max == -1 ? '<span class="badge badge-info">no limit</span>' : '<span class="badge badge-info">' . $paymentMethod->max . ' ' . $currency->code . '</span>';
            });
            $table->addColumn('charge', function (PaymentMethod $paymentMethod) use ($currency) {
                return sprintf('<span class="badge badge-info">%s%% + %s %s</span>', $paymentMethod->percent_charge, $paymentMethod->fixed_charge, $currency->code);
            });
            $table->editColumn('status', function (PaymentMethod $paymentMethod) use ($currency) {
                return $paymentMethod->status == 1 ? '<span class="badge badge-success">Enabled</span>' : '<span class="badge badge-danger">Disabled</span>';
            });
            $table->addColumn('actions', function (PaymentMethod $paymentMethod) {
                return '<button class="btn btn-warning btn-edit nimmu-btn nimmu-btn-warning"
                                                            data-id="' . $paymentMethod->id . '"
                                                            data-name="' . $paymentMethod->name . '"
                                                            data-description="' . $paymentMethod->description . '"
                                                            data-min="' . $paymentMethod->min . '"
                                                            data-max="' . $paymentMethod->max . '"
                                                            data-cred1="' . $paymentMethod->cred1 . '"
                                                            data-cred2="' . $paymentMethod->cred2 . '"
                                                            data-percent_charge="' . $paymentMethod->percent_charge . '"
                                                            data-fixed_charge="' . $paymentMethod->fixed_charge . '"
                                                            data-status="' . $paymentMethod->status . '"><i
                                                            class="fa fa-edit"></i></button>';
            });
            $table->rawColumns(['min', 'max', 'charge', 'status', 'actions']);
            return $table->make(true);
        }
        return view('staff.setting.payment-method', compact('currency'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\PaymentMethod $paymentMethod
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|void
     */
    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        if (!$paymentMethod) return abort(404);
        $v = [
            'name' => 'required|max:191',
            'description' => 'required',
            'min' => 'required|numeric|min:-1',
            'max' => 'required|numeric|min:-1',
            'cred1' => 'required',
            'cred2' => 'nullable',
            'percent_charge' => 'required|numeric|min:0',
            'fixed_charge' => 'required|numeric|min:0',
            'status' => 'nullable|in:0,1',
        ];
        if ($paymentMethod->id == 5) {
            $v['cred1'] = 'nullable';
        }
        $request->validate($v);
        $p = $request->only(['name', 'description', 'min', 'max', 'cred1', 'cred2', 'percent_charge', 'fixed_charge', 'status']);
        if ($request->status != 1) {
            $p['status'] = 0;
        }
        $res = $paymentMethod->update($p);
        if (!$res) return redirect()->back()->withErrors('Unable to update payment method');
        return redirect()->back()->withSuccess('Payment Method updated successfully');
    }
}
