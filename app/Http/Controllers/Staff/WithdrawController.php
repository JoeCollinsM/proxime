<?php

namespace App\Http\Controllers\Staff;

use App\Models\Currency;
use App\Models\DeliveryMan;
use App\Events\TransactionAdded;
use App\Events\WithdrawAccepted;
use App\Models\Shop;
use App\Models\Transaction;
use App\Models\Withdraw;
use App\Http\Controllers\Mpesa\MPESAController;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        if ($request->ajax()) {
            $query = Withdraw::query()->with(['user', 'method']);
            if (is_numeric($request->user_type) && in_array($request->user_type, [1, 2])) {
                $query->where('user_type', ($request->user_type == 1?DeliveryMan::class:Shop::class));
            }
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
                if ($withdraw->status == 0) {
                    $actions .= sprintf('<button class="btn btn-primary btn-sm nimmu-btn nimmu-btn-primary btn-approve" data-id="%s"><i class="fa fa-check-circle"></i></button>', $withdraw->id);
                    $actions .= sprintf('<button class="btn btn-outline-danger btn-sm nimmu-btn nimmu-btn-outline-danger btn-refund" data-id="%s"><i class="fa fa-times"></i></button>', $withdraw->id);
                }
                return $actions;
            });
            $table->rawColumns(['status', 'action']);
            return $table->make(true);
        }
        return view('staff.withdraw.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Withdraw $withdraw
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|void
     */
    public function update(Request $request, Withdraw $withdraw)
    {
        if (!$withdraw) return abort(404);
        if ($withdraw->status != 0) throw new AuthorizationException;
        $request->validate([
            'action' => 'required|in:accept,reject'
        ]);
        if ($request->action == 'accept') {
            try {
                if($withdraw->withdraw_method_id == 1){
                    $withdralRemarks = "Proxime Account withdrawal";
                    $shopinfo = Shop::find($withdraw->user_id);
                    $shopphone = $shopinfo->phone;
                    (new MPESAController)->proximeBusinessToclient($withdraw->amount, $shopphone, $withdralRemarks, "Withdrawal");
                }
                DB::beginTransaction();
                $r = $withdraw->update([
                    'status' => 1
                ]);
                if (!$r) throw new \Exception('Unable to update withdraw request');
                DB::commit();
                event(new WithdrawAccepted($withdraw));
                
                return redirect()->back()->withSuccess('Withdraw request accepted successfully'.$shopphone);
            } catch (\Exception $exception) {
                DB::rollBack();
                return redirect()->back()->withErrors($exception->getMessage());
            }
        } else {
            /* @var DeliveryMan|Shop $user */
            $user = $withdraw->user;
            try {
                DB::beginTransaction();
                $r = $withdraw->update([
                    'status' => 2
                ]);
                if (!$r) throw new \Exception('Unable to update withdraw request');
                $r2 = $user->update([
                    'balance' => ($user->balance + $withdraw->amount + $withdraw->charge)
                ]);
                if (!$r2) throw new \Exception('Unable to update user balance');
                /* @var Transaction $transaction */
                $transaction = $user->transactions()->create([
                    'track' => Transaction::generateTrack(),
                    'title' => 'Withdraw Refund',
                    'ref_type' => get_class($withdraw),
                    'ref_id' => $withdraw->id,
                    'type' => '+',
                    'amount' => ($withdraw->amount + $withdraw->charge),
                    'matter' => 'withdraw_refund'
                ]);
                if (!$transaction) throw new \Exception('Unable to create transaction');
                DB::commit();
                event(new TransactionAdded($transaction));
                return redirect()->back()->withSuccess('Withdraw request refunded successfully');
            } catch (\Exception $exception) {
                DB::rollBack();
                return redirect()->back()->withErrors($exception->getMessage());
            }
        }
    }
}
