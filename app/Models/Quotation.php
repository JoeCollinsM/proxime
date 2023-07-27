<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Quotation extends Model
{
    protected $fillable = [
        'date',
        'reference_no',
        'customer_id',
        'customer_email',
        'quotation_note',
        'created_by',
    ];

    public function customer()
    {
        return $this->hasOne('App\Models\Customer', 'id', 'customer_id');
    }

    public function items()
    {
        return $this->hasMany('App\Models\QuotationItems', 'quotation_id', 'id');
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
}
