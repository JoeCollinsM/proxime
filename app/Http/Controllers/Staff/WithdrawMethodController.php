<?php

namespace App\Http\Controllers\Staff;

use App\Models\Currency;
use App\Models\WithdrawMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTableAbstract;

class WithdrawMethodController
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
        $query = WithdrawMethod::query();
        if ($request->ajax()) {
            /* @var DataTableAbstract $table */
            $table = datatables()->of($query);
            $table->editColumn('min', function (WithdrawMethod $withdrawMethod) use ($currency) {
                return $withdrawMethod->min == -1 ? '<span class="badge badge-info">no limit</span>' : '<span class="badge badge-info">' . $withdrawMethod->min . ' ' . $currency->code . '</span>';
            });
            $table->editColumn('max', function (WithdrawMethod $withdrawMethod) use ($currency) {
                return $withdrawMethod->max == -1 ? '<span class="badge badge-info">no limit</span>' : '<span class="badge badge-info">' . $withdrawMethod->max . ' ' . $currency->code . '</span>';
            });
            $table->addColumn('charge', function (WithdrawMethod $withdrawMethod) use ($currency) {
                return sprintf('<span class="badge badge-info">%s%% + %s %s</span>', $withdrawMethod->percent_charge, $withdrawMethod->fixed_charge, $currency->code);
            });
            $table->editColumn('status', function (WithdrawMethod $withdrawMethod) use ($currency) {
                return $withdrawMethod->status == 1 ? '<span class="badge badge-success">Enabled</span>' : '<span class="badge badge-danger">Disabled</span>';
            });
            $table->addColumn('actions', function (WithdrawMethod $withdrawMethod) {
                return '<a href="' . route('staff.setting.withdraw-method.edit', $withdrawMethod->id) . '" class="btn btn-warning btn-edit nimmu-btn nimmu-btn-warning"><i class="fa fa-edit"></i></a>';
            });
            $table->rawColumns(['min', 'max', 'charge', 'status', 'actions']);
            return $table->make(true);
        }
        return view('staff.setting.withdraw-method.index', compact('currency'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $currency = Currency::getDefaultCurrency();
        return view('staff.setting.withdraw-method.create', compact('currency'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'min' => 'nullable|numeric',
            'max' => 'nullable|numeric',
            'percent_charge' => 'nullable|numeric',
            'fixed_charge' => 'nullable|numeric',
            'fields' => 'nullable|array'
        ]);
        $params = $request->only(['name', 'description', 'min', 'max', 'percent_charge', 'fixed_charge', 'fields']);
        $params['status'] = $request->status?1:0;
        if (!$request->min) {
            $params['min'] = -1;
        }
        if (!$request->max) {
            $params['max'] = -1;
        }
        if (!$request->percent_charge) {
            $params['percent_charge'] = 0;
        }
        if (!$request->fixed_charge) {
            $params['fixed_charge'] = 0;
        }
        try {
            DB::beginTransaction();
            $withdrawMethod = WithdrawMethod::create($params);
            if (!$withdrawMethod) throw new \Exception('Unable to save method');
            DB::commit();
            return redirect()->route('staff.setting.withdraw-method.edit', $withdrawMethod->id)->withSuccess('Withdraw method created successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            throw ValidationException::withMessages(['name' => $exception->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\WithdrawMethod  $withdrawMethod
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function edit(WithdrawMethod $withdrawMethod)
    {
        if (!$withdrawMethod) return abort(404);
        $currency = Currency::getDefaultCurrency();
        return view('staff.setting.withdraw-method.edit', compact('currency', 'withdrawMethod'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\WithdrawMethod  $withdrawMethod
     * @return void
     */
    public function update(Request $request, WithdrawMethod $withdrawMethod)
    {
        if (!$withdrawMethod) return abort(404);
        $request->validate([
            'name' => 'required|string|max:191',
            'min' => 'nullable|numeric',
            'max' => 'nullable|numeric',
            'percent_charge' => 'nullable|numeric',
            'fixed_charge' => 'nullable|numeric',
            'fields' => 'nullable|array'
        ]);
        $params = $request->only(['name', 'description', 'min', 'max', 'percent_charge', 'fixed_charge', 'fields']);
        $params['status'] = $request->status?1:0;
        if (!$request->min) {
            $params['min'] = -1;
        }
        if (!$request->max) {
            $params['max'] = -1;
        }
        if (!$request->percent_charge) {
            $params['percent_charge'] = 0;
        }
        if (!$request->fixed_charge) {
            $params['fixed_charge'] = 0;
        }
        try {
            DB::beginTransaction();
            $r = $withdrawMethod->update($params);
            if (!$r) throw new \Exception('Unable to update method');
            DB::commit();
            return redirect()->back()->withSuccess('Withdraw method created successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            throw ValidationException::withMessages(['name' => $exception->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\WithdrawMethod  $withdrawMethod
     * @return \Illuminate\Http\Response
     */
    public function destroy(WithdrawMethod $withdrawMethod)
    {
        //
    }
}
