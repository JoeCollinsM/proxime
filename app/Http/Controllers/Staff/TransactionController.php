<?php

namespace App\Http\Controllers\Staff;

use App\Models\Currency;
use App\Models\DeliveryMan;
use App\Events\TransactionAdded;
use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\DataTableAbstract;

class TransactionController extends Controller
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
            $query = Transaction::query()->with(['user']);
            if (in_array($request->user_type, ['user', 'delivery_man', 'shop'])) {
                $user_type = User::class;
                if ($request->user_type == 'shop') {
                    $user_type = Shop::class;
                } elseif ($request->user_type == 'delivery_man') {
                    $user_type = DeliveryMan::class;
                }
                $query->where('user_type', $user_type);
            }
            if (is_numeric($request->user_id)) {
                $query->where('user_id', $request->user_id);
            }
            if (in_array($request->type, ['+', '-'])) {
                $query->where('type', $request->type);
            }
            if ($request->start_date && ($start_date = Carbon::createFromFormat('d-m-Y', $request->start_date))) {
                $query->whereDate('created_at', '>=', $start_date);
            }
            if ($request->end_date && ($end_date = Carbon::createFromFormat('d-m-Y', $request->end_date))) {
                $query->whereDate('created_at', '<=', $end_date);
            }
            /* @var DataTableAbstract $table */
            $table = datatables()->of($query);
            $table->editColumn('created_at', function (Transaction $transaction) {
                return $transaction->created_at->format('M d, Y h:i a');
            });
            $table->editColumn('user.username', function (Transaction $transaction) {
                if ($transaction->user instanceof DeliveryMan) return $transaction->user->username . ' (Delivery Man)';
                if ($transaction->user instanceof User) return $transaction->user->username . ' (Customer)';
                if ($transaction->user instanceof Shop) return $transaction->user->name . ' (Shop)';
            });
            $table->editColumn('amount', function (Transaction $transaction) {
                $currency = Currency::getDefaultCurrency();
                return sprintf('%s %s %s', $transaction->type, $transaction->amount, $currency->code);
            });
            return $table->make(true);
        }
        return view('staff.transaction.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $currency = Currency::getDefaultCurrency();
        $customers = User::query()->where('status', 1)->get();
        $delivery_men = DeliveryMan::query()->where('status', 1)->get();
        $shops = Shop::query()->where('status', 1)->get();
        return view('staff.transaction.create', compact('currency', 'customers', 'delivery_men', 'shops'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $t = 'users';
        if ($request->user_type == 'delivery_man') {
            $t = 'delivery_men';
        } elseif ($request->user_type == 'shop') {
            $t = 'shops';
        }
        $request->validate([
            'user_type' => 'required|in:user,delivery_man,shop',
            'user_id' => 'required|exists:' . $t . ',id',
            'title' => 'required|string|max:191',
            'type' => 'required|in:+,-',
            'amount' => 'required|numeric|min:0'
        ]);
        if ($request->user_type == 'delivery_man') {
            /* @var DeliveryMan $user */
            $user = DeliveryMan::find($request->user_id);
        } elseif ($request->user_type == 'shop') {
            /* @var Shop $user */
            $user = Shop::find($request->user_id);
        } else {
            /* @var User $user */
            $user = User::find($request->user_id);
        }
        if ($request->type == '-' && $user->balance < $request->amount) throw ValidationException::withMessages(['amount' => 'Not Enough Balance']);

        try {
            DB::beginTransaction();
            if ($request->type == '+') {
                $r = $user->update([
                    'balance' => ($user->balance + $request->amount)
                ]);
                if (!$r) throw new \Exception('Unable to update balance');
                /* @var Transaction $transaction */
                $transaction = Transaction::create([
                    'track' => Transaction::generateTrack(),
                    'title' => $request->title,
                    'user_type' => get_class($user),
                    'user_id' => $user->id,
                    'type' => $request->type,
                    'amount' => $request->amount,
                    'matter' => 'system'
                ]);
            } else {
                $r = $user->update([
                    'balance' => ($user->balance - $request->amount)
                ]);
                if (!$r) throw new \Exception('Unable to update balance');
                /* @var Transaction $transaction */
                $transaction = Transaction::create([
                    'track' => Transaction::generateTrack(),
                    'title' => $request->title,
                    'user_type' => get_class($user),
                    'user_id' => $user->id,
                    'type' => $request->type,
                    'amount' => $request->amount,
                    'matter' => 'system'
                ]);
            }
            if (!$transaction) throw new \Exception('Unable to add transaction');
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw ValidationException::withMessages(['title' => $exception->getMessage()]);
        }
        event(new TransactionAdded($transaction));
        return redirect()->route('staff.transaction.index')->withSuccess('Transaction added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
