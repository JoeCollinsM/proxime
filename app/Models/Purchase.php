<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Purchase extends Model
{
    protected $fillable = [
        'invoice_id',
        'supplier_id',
        'branch_id',
        'cash_register_id',
        'created_by',
    ];

    public function items()
    {
        return $this->hasMany('App\Models\PurchasedItems', 'purchase_id', 'id');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'created_by');
    }

    public function supplier()
    {
        return $this->hasOne('App\Models\Supplier', 'id', 'supplier_id');
    }

    public function getTotal()
    {
        $subtotals = 0;
        foreach ($this->items as $item) {
            $subtotal = $item->price * $item->quantity;
            $tax      = ($subtotal * $item->tax) / 100;

            $subtotals += $subtotal + $tax;
        }

        return $subtotals;
    }

    public function itemsArray()
    {
        $total = 0;
        $items = [];
        if (\Auth::check()) {

            $user = \Auth::user();
        } else {
            $user = User::where('id', $this->created_by)->first();
        }
        foreach ($this->items as $key => $item) {
            $subtotal = $item->price * $item->quantity;
            $tax      = ($subtotal * $item->tax) / 100;
            
            $product_id = !empty($item->product) ? $item->product->id : 0;

            if ($item->product == null) {
                $product_id = 0;
            } else {
                $items['data'][$key]['id'] = $item->product->id;
            }
            $items['data'][$key]['name']            =  !empty($item->product) ? $item->product->name : '-';
            $items['data'][$key]['quantity']        = $item->quantity;
            $items['data'][$key]['orgprice']        = $item->price;
            $items['data'][$key]['price']           = $user->priceFormat($item->price);
            $items['data'][$key]['tax']             = $item->tax . '%';
            $items['data'][$key]['only_tax']        = $item->tax;
            $items['data'][$key]['tax_amount']      = $user->priceFormat($tax);
            $items['data'][$key]['only_tax_amount'] = $tax;
            $items['data'][$key]['only_subtotal']   = $subtotal + $tax;
            $items['data'][$key]['subtotal']        = $user->priceFormat($subtotal + $tax);
            $total                                  += $subtotal + $tax;
        }

        $items['total'] = $user->priceFormat($total);

        return $items;
    }

    public static function totalPurchasedAmount($month = false)
    {
        $purchased = new Purchase();

        $purchased = $purchased->where('created_by', '=', Auth::user()->getCreatedBy());

        if ($month) {
            $purchased = $purchased->whereRaw('MONTH(created_at) = ?', [date('m')]);
        }

        $purchasedAmount = 0;
        foreach ($purchased->get() as $key => $purchase) {
            $purchasedAmount += $purchase->getTotal();
        }

        return Auth::user()->priceFormat($purchasedAmount);
    }

    public static function getPurchaseReportChart()
    {
        $purchases = Purchase::whereDate('created_at', '>', Carbon::now()->subDays(10))->where('created_by', '=', Auth::user()->getCreatedBy())->orderBy('created_at')->get()->groupBy(
            function ($val) {
                return Carbon::parse($val->created_at)->format('dm');
            }
        );

        $total = [];
        if (!empty($purchases) && count($purchases) > 0) {
            foreach ($purchases as $day => $onepurchase) {
                $totals = 0;
                foreach ($onepurchase as $purchase) {
                    $totals += $purchase->getTotal();
                }
                $total[$day] = $totals;
            }
        }
        $d = date("d");
        $m = date("m");
        $y = date("Y");

        for ($i = 0; $i <= 9; $i++) {
            $date                      = date('Y-m-d', mktime(0, 0, 0, $m, ($d - $i), $y));
            $purchasesArray['label'][] = $date;
            $date                      = date('dm', strtotime($date));
            $purchasesArray['value'][] = array_key_exists($date, $total) ? $total[$date] : 0;;
        }

        return $purchasesArray;
    }

    public static function getPurchaseReportDailyChart($data = [])
    {
        $purchases = Purchase::whereDate('created_at', '>=', $data['start_date'])->whereDate('created_at', '<=', $data['end_date'])->where('created_by', '=', Auth::user()->getCreatedBy())->orderBy('created_at');

        if ($data['branch_id'] != '-1') {
            $purchases = $purchases->where('branch_id', $data['branch_id']);
        }

        if ($data['cash_register_id'] != '-1') {
            $purchases = $purchases->where('cash_register_id', $data['cash_register_id']);
        }

        $purchases = $purchases->get()->groupBy(
            function ($val) {
                return Carbon::parse($val->created_at)->format('dm');
            }
        );

        $total = [];
        if (!empty($purchases) && count($purchases) > 0) {
            foreach ($purchases as $day => $onepurchase) {
                $totals = 0;
                foreach ($onepurchase as $purchase) {
                    $totals += $purchase->getTotal();
                }
                $total[$day] = $totals;
            }
        }

        $period = CarbonPeriod::create($data['start_date'], $data['end_date']);

        foreach ($period as $dateobj) {
            $date                      = $dateobj->format('Y-m-d');
            $purchasesArray['label'][] = $date;
            $date                      = date('dm', strtotime($date));
            $purchasesArray['value'][] = array_key_exists($date, $total) ? $total[$date] : 0;;
        }

        return $purchasesArray;
    }

    public static function getPurchaseReportMonthlyChart($data = [])
    {
        $purchases = Purchase::whereMonth('created_at', '>=', Carbon::now()->subMonth(12))->where('created_by', '=', Auth::user()->getCreatedBy())->orderBy('created_at');

        if ($data['branch_id'] != '-1') {
            $purchases = $purchases->where('branch_id', $data['branch_id']);
        }

        if ($data['cash_register_id'] != '-1') {
            $purchases = $purchases->where('cash_register_id', $data['cash_register_id']);
        }

        $purchases = $purchases->get()->groupBy(
            function ($val) {
                return Carbon::parse($val->created_at)->format('my');
            }
        );

        $total = [];
        if (!empty($purchases) && count($purchases) > 0) {
            foreach ($purchases as $day => $onepurchase) {
                $totals = 0;
                foreach ($onepurchase as $purchase) {
                    $totals += $purchase->getTotal();
                }
                $total[$day] = $totals;
            }
        }

        for ($i = 0; $i < 12; $i++) {
            $monthsLabel[]             = date("F - Y", strtotime(date('Y-m-01') . " -$i months"));
            $month                     = date("my", strtotime(date('Y-m-01') . " -$i months"));
            $purchasesArray['value'][] = array_key_exists($month, $total) ? $total[$month] : 0;
        }

        $purchasesArray['value'] = array_reverse($purchasesArray['value']);
        $purchasesArray['label'] = array_reverse($monthsLabel);

        return $purchasesArray;
    }
    public static function branchname($branch)
    {
        $categoryArr  = explode(',', $branch);
        $unitRate = 0;
        foreach ($categoryArr as $branch) {
            $branch          = Branch::find($branch);
            $unitRate        = (!empty($branch->name) ? $branch->name : '');
        }

        return $unitRate;
    }
    public static function cashregister($cashregister)
    {
        $categoryArr  = explode(',', $cashregister);
        $unitRate = 0;
        foreach ($categoryArr as $cashregister) {
            $cashregister    = CashRegister::find($cashregister);
            $unitRate        = (!empty($cashregister->name) ? $cashregister->name : '');
        }

        return $unitRate;
    }

    public static function suppliers($venders)
    {
        $categoryArr  = explode(',', $venders);
        $unitRate = 0;
        foreach ($categoryArr as $venders) {
            if ($venders == 0) {
                $unitRate = '';
            } else {
                $venders        = Supplier::find($venders);
                $unitRate        = (!empty($venders->name) ? $venders->name : '');
            }
        }

        return $unitRate;
    }
}
