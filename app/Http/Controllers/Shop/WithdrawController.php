<?php

namespace App\Http\Controllers\Shop;

use App\Models\Currency;
use App\Models\DeliveryMan;
use App\Events\TransactionAdded;
use App\Events\WithdrawAccepted;
use App\Models\Shop;
use App\Models\Transaction;
use App\Models\Withdraw;
use App\WModels\ithdrawMethod;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTableAbstract;

class WithdrawController
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        /* @var Shop $shop */
        $shop = $request->user('shop');
        if ($request->ajax()) {
            $query = $shop->withdraws()->with(['method']);
            if (is_numeric($request->status) && in_array($request->status, [0, 1, 2])) {
                $query->where('status', $request->status);
            }
            if ($request->start_date && ($start_date = Carbon::createFromFormat('d-m-Y', $request->start_date))) {
                $query->whereDate('created_at', '>=', $start_date);
            }
            if ($request->end_date && ($end_date = Carbon::createFromFormat('d-m-Y', $request->end_date))) {
                $query->whereDate('created_at', '<=', $end_date);
            }
            /* @var DataTableAbstract $table */
            $table = datatables()->of($query);
            $table->editColumn('created_at', function (Withdraw $withdraw) {
                return $withdraw->created_at->format('M d, Y h:i a');
            });
            $table->editColumn('amount', function (Withdraw $withdraw) {
                $currency = Currency::getDefaultCurrency();
                return sprintf('%s %s', $withdraw->amount, $currency->code);
            });
            $table->editColumn('charge', function (Withdraw $withdraw) {
                $currency = Currency::getDefaultCurrency();
                return sprintf('%s %s', $withdraw->charge, $currency->code);
            });
            $table->editColumn('status', function (Withdraw $withdraw) {
                if ($withdraw->status == 0) return '<span class="badge badge-warning">Pending</span>';
                if ($withdraw->status == 1) return '<span class="badge badge-success">Accepted</span>';
                if ($withdraw->status == 2) return '<span class="badge badge-danger">Rejected</span>';
            });
            $table->addColumn('action', function (Withdraw $withdraw) {
                $actions = sprintf('<button class="btn btn-outline-info btn-sm nimmu-btn nimmu-btn-outline-info btn-show" data-fields=\'%s\'><i class="fa fa-eye"></i></button>', json_encode($withdraw->fields));
                return $actions;
            });
            $table->rawColumns(['status', 'action']);
            return $table->make(true);
        }
        return view('shop.withdraw.index');
    }

    public function create(Request $request)
    {
        $currency = Currency::getDefaultCurrency();
        $methods = WithdrawMethod::query()->where('status', 1)->get();
        return view('shop.withdraw.create', compact('currency', 'methods'));
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
            'withdraw_method_id' => 'required|exists:withdraw_methods,id',
            'amount' => 'required|numeric|min:0'
        ]);
        /* @var Shop $user */
        $user = $request->user('shop');
        /* @var WithdrawMethod $withdrawMethod */
        $withdrawMethod = WithdrawMethod::query()->where('id', $request->withdraw_method_id)->where('status', 1)->firstOrFail();
        if ($request->amount == 0) throw ValidationException::withMessages(['amount' => 'Amount must be greater then 0']);
        if ($withdrawMethod->min != -1 && $request->amount < $withdrawMethod->min) throw ValidationException::withMessages(['amount' => 'Amount is less then method minimum amount']);
        if ($withdrawMethod->max != -1 && $request->amount > $withdrawMethod->max) throw ValidationException::withMessages(['amount' => 'Amount is greater then method maximum amount']);
        if ($request->amount > $user->balance) throw ValidationException::withMessages(['amount' => 'Not enough balance']);
        $charge = round(($withdrawMethod->fixed_charge + (($request->amount * $withdrawMethod->percent_charge) / 100)), 2);
        $gross = $request->amount+$charge;
        try {
            DB::beginTransaction();
            $r = $user->update([
                'balance' => ($user->balance-$gross)
            ]);
            if (!$r) throw new \Exception('Unable to update balance');
            $fields = $request->fields;
            if (!is_array($fields)) $fields = [];
            /* @var Withdraw $withdraw */
            $withdraw = $user->withdraws()->create([
                'withdraw_method_id' => $request->withdraw_method_id,
                'amount' => $request->amount,
                'charge' => $charge,
                'fields' => $fields
            ]);
            if (!$withdraw) throw new \Exception('Unable to create withdraw');
            /* @var Transaction $transaction */
            $transaction = $user->transactions()->create([
                'track' => Transaction::generateTrack(),
                'title' => 'Withdraw request',
                'ref_type' => get_class($withdraw),
                'ref_id' => $withdraw->id,
                'type' => '-',
                'amount' => $gross,
                'matter' => 'withdraw_request'
            ]);
            if (!$transaction) throw new \Exception('Unable to create transaction');
            event(new TransactionAdded($transaction));
            DB::commit();
            return redirect()->route('shop.withdraw.index')->withSuccess('Withdraw request created successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            throw ValidationException::withMessages(['amount' => $exception->getMessage()]);
        }
    }
}
