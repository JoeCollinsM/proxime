<?php

namespace App\Http\Controllers\Shop;

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
        /* @var Shop $shop */
        $shop = $request->user('shop');
        if ($request->ajax()) {
            $query = $shop->transactions();
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
            $table->editColumn('amount', function (Transaction $transaction) {
                $currency = Currency::getDefaultCurrency();
                return sprintf('%s %s %s', $transaction->type, $transaction->amount, $currency->code);
            });
            return $table->make(true);
        }
        return view('shop.transaction.index');
    }
}
