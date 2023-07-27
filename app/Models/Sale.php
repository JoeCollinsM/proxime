<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Sale extends Model
{
    protected $fillable = [
        'invoice_id',
        'customer_id',
        'branch_id',
        'cash_register_id',
        'created_by',
    ];

    public function items()
    {
        return $this->hasMany('App\Models\SelledItems', 'sell_id', 'id');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'created_by');
    }

    public function customer()
    {
        return $this->hasOne('App\Models\Customer', 'id', 'customer_id');
    }

    public function getTotal()
    {
        $subtotals = 0;
        foreach($this->items as $item)
        {
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
        if(\Auth::check())
        {
            
            $user=\Auth::user();
        }
        else{
            
           $user=User::where('id',$this->created_by)->first();
        }
        foreach($this->items as $key => $item)
        {
            $subtotal = $item->price * $item->quantity;
            $tax      = ($subtotal * $item->tax) / 100;

            $items['data'][$key]['id']              = !empty($item->product) ? $item->product->id :0;
            $items['data'][$key]['name']            = !empty($item->product) ? $item->product->name :'-';
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

    public static function monthlySelledAmount()
    {
        $monthSelled = Sale::where('created_by', '=', Auth::user()->getCreatedBy())->whereRaw('MONTH(created_at) = ?', [date('m')])->get();

        $monthSelledAmount = 0;
        foreach($monthSelled as $key => $sale)
        {
            $monthSelledAmount += $sale->getTotal();
        }

        return Auth::user()->priceFormat($monthSelledAmount);
    }

    public static function totalSelledAmount($month = false)
    {
        $sells = new Sale();

        $sells = $sells->where('created_by', '=', Auth::user()->getCreatedBy());

        if($month)
        {
            $sells = $sells->whereRaw('MONTH(created_at) = ?', [date('m')]);
        }

        $selledAmount = 0;
        foreach($sells->get() as $key => $sell)
        {
            $selledAmount += $sell->getTotal();
        }

        return Auth::user()->priceFormat($selledAmount);
    }

    public static function getSalesReportChart()
    {
        $sales = Sale::whereDate('created_at', '>', Carbon::now()->subDays(10))->where('created_by', '=', Auth::user()->getCreatedBy())->orderBy('created_at')->get()->groupBy(
                function ($val){
                    return Carbon::parse($val->created_at)->format('dm');
                }
            );
        $total = [];
        if(!empty($sales) && count($sales) > 0)
        {
            foreach($sales as $day => $onesale)
            {
                $totals = 0;
                foreach($onesale as $sale)
                {
                    $totals += $sale->getTotal();
                }
                $total[$day] = $totals;
            }
        }
        $m = date("m");
        $d = date("d");
        $y = date("Y");
        for($i = 0; $i <= 9; $i++)
        {
            $date                  = date('Y-m-d', mktime(0, 0, 0, $m, ($d - $i), $y));
            $salesArray['label'][] = $date;
            $date                  = date('dm', strtotime($date));
            $salesArray['value'][] = array_key_exists($date, $total) ? $total[$date] : 0;;
        }

        return $salesArray;
    }

    public static function getSaleReportDailyChart($data = [])
    {
        $sales = Sale::whereDate('created_at', '>=', $data['start_date'])->whereDate('created_at', '<=', $data['end_date'])->where('created_by', '=', Auth::user()->getCreatedBy())->orderBy('created_at');

        if($data['branch_id'] != '-1')
        {
            $sales = $sales->where('branch_id', $data['branch_id']);
        }

        if($data['cash_register_id'] != '-1')
        {
            $sales = $sales->where('cash_register_id', $data['cash_register_id']);
        }

        $sales = $sales->get()->groupBy(
                function ($val){
                    return Carbon::parse($val->created_at)->format('dm');
                }
            );

        $total = [];
        if(!empty($sales) && count($sales) > 0)
        {
            foreach($sales as $day => $onesale)
            {
                $totals = 0;
                foreach($onesale as $sale)
                {
                    $totals += $sale->getTotal();
                }
                $total[$day] = $totals;
            }
        }

        $period = CarbonPeriod::create($data['start_date'], $data['end_date']);

        foreach($period as $dateobj)
        {
            $date                  = $dateobj->format('Y-m-d');
            $salesArray['label'][] = $date;
            $date                  = date('dm', strtotime($date));
            $salesArray['value'][] = array_key_exists($date, $total) ? $total[$date] : 0;;
        }

        return $salesArray;
    }

    public static function getSaleReportMonthlyChart($data = [])
    {
        $sales = Sale::whereMonth('created_at', '>=', Carbon::now()->subMonth(12))->where('created_by', '=', Auth::user()->getCreatedBy())->orderBy('created_at');

        if($data['branch_id'] != '-1')
        {
            $sales = $sales->where('branch_id', $data['branch_id']);
        }

        if($data['cash_register_id'] != '-1')
        {
            $sales = $sales->where('cash_register_id', $data['cash_register_id']);
        }

        $sales = $sales->get()->groupBy(
                function ($val){
                    return Carbon::parse($val->created_at)->format('my');
                }
            );

        $total = [];
        if(!empty($sales) && count($sales) > 0)
        {
            foreach($sales as $day => $onesale)
            {
                $totals = 0;
                foreach($onesale as $sale)
                {
                    $totals += $sale->getTotal();
                }
                $total[$day] = $totals;
            }
        }

        for($i = 0; $i < 12; $i++)
        {
            $monthsLabel[]         = date("F - Y", strtotime(date('Y-m-01') . " -$i months"));
            $month                 = date("my", strtotime(date('Y-m-01') . " -$i months"));
            $salesArray['value'][] = array_key_exists($month, $total) ? $total[$month] : 0;
        }

        $salesArray['value'] = array_reverse($salesArray['value']);
        $salesArray['label'] = array_reverse($monthsLabel);

        return $salesArray;
    }


    public static function customers($customer)
    {   
        
        $categoryArr  = explode(',', $customer);
        $unitRate = 0;
        foreach($categoryArr as $customer)
        {
            if($customer == 0){
                $unitRate = '';
            }
            else{
                $customer        = Customer::find($customer);
                $unitRate        = (!empty($customer->name) ? $customer->name :'');
            }
            
        }

        return $unitRate;
    }

    public static function branchname($branch)
    {
        $categoryArr  = explode(',', $branch);
        $unitRate = 0;
        foreach($categoryArr as $branch)
        {
            $branch          = Branch::find($branch);
            $unitRate        = (!empty($branch->name) ? $branch->name :'');
            
        }

        return $unitRate;
    }

    public static function cashregister($cashregister)
    {
        $categoryArr  = explode(',', $cashregister);
        $unitRate = 0;
        foreach($categoryArr as $cashregister)
        {
            $cashregister    = CashRegister::find($cashregister);
            $unitRate        = (!empty($cashregister->name) ? $cashregister->name : '');
            
        }

        return $unitRate;
    }
}
