<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Supplier extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','email','phone_number','address','city','country','is_active','created_by',];

    public static function Supplier_id($Supplier_name){
        $Suppliers = DB::select(
            DB::raw("SELECT IFNULL( (SELECT id from Suppliers where name = :name and created_by = :created_by limit 1), '0') as Supplier_id"), ['name' => $Supplier_name, 'created_by' => Auth::user()->getCreatedBy(),]
        );

        return $Suppliers[0]->Supplier_id;
    }

    public static function getSupplierPurchasedAnalysis(array $data){
        $authuser = Auth::user();

        $Suppliers = Supplier::where('created_by', $authuser->getCreatedBy());
        $purchased = Purchase::where('created_by', $authuser->getCreatedBy());

        if ($data['Supplier_id'] != '-1')
        {
            $purchased = $purchased->where('Supplier_id', $data['Supplier_id']);
            $Suppliers = $Suppliers->where('id', $data['Supplier_id']);
        }

        if ($data['branch_id'] != '-1')
        {
            $purchased = $purchased->where('branch_id', $data['branch_id']);
        }

        if ($data['cash_register_id'] != '-1')
        {
            $purchased = $purchased->where('cash_register_id', $data['cash_register_id']);
        }

        if($data['start_date'] != '' && $data['end_date'] != '')
        {
            $purchased = $purchased->whereDate('created_at', '>=', $data['start_date'])->whereDate('created_at', '<=', $data['end_date']);
        }
        else if($data['start_date'] != '' || $data['end_date'] != '')
        {
            $date     = $data['start_date'] == '' ? ($data['end_date'] == '' ? '' : $data['end_date']) : $data['start_date'];
            $purchased = $purchased->whereDate('created_at', '=', $date);
        }

        $walk_in_Supplier_array = [];
        
        if ($data['Supplier_id'] == '-1' || $data['Supplier_id'] == '0')
        {
            $count_walk_in_Supplier = Purchase::where('created_by', $authuser->getCreatedBy())->where('Supplier_id', 0)->count();
            
            if($count_walk_in_Supplier > 0) {

                $walk_in_Supplier_array = [
                                    '0' => [
                                            'id' => 0, 
                                            'name' => 'Walk-in Suppliers',
                                            'phone_number' => '',
                                            'email' => '',
                                          ]
                                    ];
            }
        }

        $Suppliers = array_merge($walk_in_Supplier_array, $Suppliers->get()->toArray());

        $productSupplier = [];

        $total_purchased_quantity = $total_purchased_price = 0;

        foreach($Suppliers as $counter => $Supplier) {

            $purchased_quantity = $purchased_price = 0;

            $purchasedCollection = clone $purchased;
            $purchasedCollection = $purchasedCollection->where('Supplier_id', $Supplier['id'])->get();

            foreach ($purchasedCollection as $sc) {

                $purchasedItemsArray = $sc->itemsArray();

                foreach ($purchasedItemsArray['data'] as $itemvalue) {

                    $purchased_quantity += $itemvalue['quantity'];
                    $purchased_price += $itemvalue['only_subtotal'];
                }
            }

            $total_purchased_quantity += $purchased_quantity;
            $total_purchased_price += $purchased_price;

            $productSupplier[$counter]['name'] = $Supplier['name'];
            $productSupplier[$counter]['phone_number'] = $Supplier['phone_number'];
            $productSupplier[$counter]['email_address'] = $Supplier['email'];
            $productSupplier[$counter]['total_sales'] = $purchased_quantity;
            $productSupplier[$counter]['total_amount'] = Auth::user()->priceFormat($purchased_price);
        }

        $data['draw']            = 1;
        $data['recordsTotal']    = count($productSupplier);
        $data['recordsFiltered'] = count($productSupplier);
        $data['totalPurchasedQuantity'] = $total_purchased_quantity;
        $data['totalPurchasedPrice'] = Auth::user()->priceFormat($total_purchased_price);
        $data['data']            = $productSupplier;

        return $data;
    }
}
