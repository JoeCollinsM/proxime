<?php

namespace App\Http\Controllers\API\DeliveryMan;

use App\Models\DeliveryMan;
use App\Events\TransactionAdded;
use App\Helpers\API\Formatter;
use App\Models\Transaction;
use App\Models\Withdraw;
use App\Models\WithdrawMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WithdrawController
{
    use Formatter;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /* @var DeliveryMan $user */
        $user = $request->user('delivery_man');
        $query = $user->withdraws();
        if (is_numeric($request->status) && in_array($request->status, [0, 1, 2])) {
            $query->where('status', $request->status);
        }
        if ($request->order_by && in_array($request->order, ['asc', 'desc'])) {
            $query->orderBy($request->order_by, $request->order);
        } else {
            $query->orderByDesc('id');
        }
        if ($request->paginate) {
            return $this->withSuccess($query->paginate($request->perpage));
        } else {
            if ($request->limit) {
                $query->take($request->limit);
            }
        }
        return $this->withSuccess($query->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $methods = WithdrawMethod::query()->where('status', 1)->get()->map(function (WithdrawMethod $withdrawMethod) {
            if (is_array($withdrawMethod->fields)) {
                $withdrawMethod->fields = array_map(function ($field) {
                    $value = '';
                    if ($field['input_type'] == 'radio') $value = 1;
                    if ($field['input_type'] == 'multiple' || $field['input_type'] == 'checkbox') $value = false;
                    $field['value'] = $value;
                    if (isset($field['options']) && is_array($field['options'])) {
                        $field['options'] = array_map(function ($option) use ($value) {
                            $option['value'] = $value;
                            return $option;
                        }, $field['options']);
                    }
                    return $field;
                }, $withdrawMethod->fields);
            }
            return $withdrawMethod;
        });
        return $this->withSuccess($methods);
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
        /* @var DeliveryMan $user */
        $user = $request->user('delivery_man');
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
            $fields = json_decode($request->fields, true);
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
            return $this->withCreated($withdraw);
        } catch (\Exception $exception) {
            DB::rollBack();
            throw ValidationException::withMessages(['amount' => $exception->getMessage()]);
        }
    }
}
