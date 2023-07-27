<?php

namespace App\Http\Controllers\Staff;

use App\Models\Consignment;
use App\Models\Currency;
use App\Models\DeliveryMan;
use App\Events\AssignedOrderToDeliveryMan;
use App\Events\TransactionAdded;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsignmentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'delivery_man_id' => 'required|exists:delivery_men,id',
            'start_on' => 'required|date_format:d-m-Y'
        ]);
        /* @var Order $order */
        $order = Order::find($request->order_id);
        $params = $request->only(['order_id', 'delivery_man_id', 'start_on', 'images', 'notes']);
        $params['track'] = Consignment::generateTrack();
        if (config('proxime.delivery.type') == 'fixed') {
            $params['commission'] = round((config('proxime.delivery.custom_percentage', 0) * $order->gross_total) / 100, 2);
        } else {
            $request->validate([
                'commission' => 'required|numeric'
            ]);
            $params['commission'] = $request->commission;
        }
        try {
            $consignment = Consignment::create($params);
            if (!$consignment) throw new \Exception('Unable to create shipment');
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors($exception->getMessage());
        }
        event(new AssignedOrderToDeliveryMan($consignment));
        return redirect()->back()->withSuccess('Shipment added successfully');
    }

    public function commission(Consignment $consignment)
    {
        if ($consignment->commissionExist()) throw new AuthorizationException;
        if (!$consignment->delivery_man instanceof DeliveryMan) return abort(404);
        $currency = Currency::getDefaultCurrency();
        try {
            DB::beginTransaction();
            $r = $consignment->delivery_man->update([
                'balance' => $consignment->delivery_man->balance + $consignment->commission
            ]);
            if (!$r) throw new \Exception('Unable to update delivery man balance');
            $transaction = $consignment->delivery_man->transactions()->create([
                'track' => Transaction::generateTrack(),
                'title' => sprintf('Received shipment commission %s %s for order #%s', $consignment->commission, $currency->code, $consignment->id),
                'ref_type' => get_class($consignment),
                'ref_id' => $consignment->id,
                'type' => '+',
                'amount' => $consignment->commission,
                'matter' => 'commission'
            ]);
            if (!$transaction) throw new \Exception('Unable to add transaction');
            event(new TransactionAdded($transaction));
            DB::commit();
            return redirect()->back()->withSuccess('Commission paid successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->withErrors($exception->getMessage());
        }
    }
}
