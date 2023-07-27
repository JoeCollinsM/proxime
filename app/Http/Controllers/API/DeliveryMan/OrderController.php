<?php

namespace App\Http\Controllers\API\DeliveryMan;

use App\Models\Consignment;
use App\Models\DeliveryMan;
use App\Events\OrderStatusUpdated;
use App\Helpers\API\Formatter;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController
{
    use Formatter;

    function index(Request $request)
    {
        /* @var DeliveryMan $user */
        $user = Auth::guard('delivery_man')->user();
        $q = $user->consignments()->with(['address' => function ($q) {
            $q->where('type', 'shipping');
        }, 'items' => function ($q) {
            $q->with(['product' => function ($q2) {
                $q2->select('id', 'image');
            }]);
        }, 'order' => function ($q) {
            $q->with(['payments', 'shop']);
        }]);
        if ($request->status && in_array($request->status, [0, 1, 3, 4])) {
            $q->where('status', $request->status);
        } else {
            $q->whereIn('status', [0, 1, 3, 4]);
        }
        if ($request->start_date && $request->end_date && $start_date = Carbon::createFromFormat('d-m-Y', $request->start_date) && $end_date = Carbon::createFromFormat('d-m-Y', $request->end_date)) {
            $q->where(function ($q2) use ($start_date, $end_date) {
                /* @var Builder $q2 */
                $q2->whereDate('start_on', '>=', $start_date)->whereDate('start_on', '<=', $end_date);
            });
            $q->orWhere(function ($q2) use ($start_date, $end_date) {
                /* @var Builder $q2 */
                $q2->whereDate('resolved_on', '>=', $start_date)->whereDate('resolved_on', '<=', $end_date);
            });
        }
        return $this->withSuccess($q->orderByDesc('id')->paginate());
    }

    function show(Consignment $consignment)
    {
        /* @var DeliveryMan $user */
        $user = Auth::guard('delivery_man')->user();
        if ($consignment == null) return abort(404);
        if ($user->id != $consignment->delivery_man_id) throw new AuthorizationException;
        $consignment->load(['address' => function ($q) {
            $q->where('type', 'shipping');
        }]);
        $consignment->load(['items' => function ($q) {
            $q->with(['product' => function ($q2) {
                $q2->select('id', 'image');
            }]);
        }]);
        $consignment->load(['order' => function ($q) {
            $q->with(['payments', 'shop']);
        }]);
        return $this->withSuccess($consignment);
    }

    function update(Request $request, Consignment $consignment)
    {
        /* @var DeliveryMan $user */
        $user = Auth::guard('delivery_man')->user();
        if ($consignment == null) return abort(404);
        if ($user->id != $consignment->delivery_man_id) throw new AuthorizationException;
        $request->validate([
            'action' => 'required|in:accept,reject,pickup,shipped,hold'
        ]);
        DB::beginTransaction();
        try {
            if ($request->action == 'accept') {
                if ($consignment->status != 0) return abort(404);
                $consignment->update([
                    'status' => 1
                ]);
            } elseif ($request->action == 'reject') {
                if ($consignment->status != 0) return abort(404);
                $consignment->update([
                    'status' => 2,
                    'resolved_on' => now(),
                    'rejection_cause' => $request->cause
                ]);
            } elseif ($request->action == 'pickup') {
                if ($consignment->status != 1) return abort(404);
                $r = $consignment->update([
                    'status' => 3
                ]);
                if (!$r) throw new \Exception('Unable to update consignment');
                /* @var Order $o */
                $o = $consignment->order;
                $fr = $o->status;
                $r2 = $o->update([
                    'status' => 2
                ]);
                if ($r2) {
                    $o->refresh();
                    event(new OrderStatusUpdated($o, $fr, 2));
                }
            } elseif ($request->action == 'shipped') {
                if ($consignment->status != 3) return abort(404);
                $r = $consignment->update([
                    'status' => 4,
                    'resolved_on' => now()
                ]);
                if (!$r) throw new \Exception('Unable to update consignment');
                /* @var Order $o */
                $o = $consignment->order;
                $fr = $o->status;
                $r2 = $o->update([
                    'status' => 3
                ]);
                if ($r2) {
                    $o->refresh();
                    event(new OrderStatusUpdated($o, $fr, 3));
                }
            } elseif ($request->action == 'hold') {
                if ($consignment->status != 1) return abort(404);
                $consignment->update([
                    'status' => 5,
                    'resolved_on' => now(),
                    'rejection_cause' => $request->cause
                ]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
        }
        DB::commit();
        return $this->withSuccess('Updated successfully');
    }
}
