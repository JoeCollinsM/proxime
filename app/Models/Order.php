<?php

namespace App\Models;

use App\Casts\CurrencyCast;
use App\Helpers\Reviewable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{

    use Reviewable;

    protected $guarded = ['id'];
    protected $appends = ['status_string'];
    protected $casts = [
        'discount' => CurrencyCast::class,
        'shipping_charge' => CurrencyCast::class,
        'shop_commission' => CurrencyCast::class
    ];

    static function generateTrack()
    {
        $t = rand(100000, 999999);
        $exists = self::query()->where('track', $t)->count();
        while ($exists) {
            $t = rand(100000, 999999);
            $exists = self::query()->where('track', $t)->count();
        }
        return $t;
    }

    function refundExist()
    {
        /* @var User $user */
        $user = $this->user;
        return $user->transactions()->where('ref_type', self::class)->where('ref_id', $this->id)->where('matter', 'order_refund')->exists();
    }

    function shopCommissionExist()
    {
        /* @var Shop $shop */
        $shop = $this->shop;
        return $shop->transactions()->where('ref_type', self::class)->where('ref_id', $this->id)->where('matter', 'shop_commission')->exists();
    }

    function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'id')->withoutGlobalScope('security');
    }

    function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id', 'id');
    }

    function shipping_method()
    {
        return $this->belongsTo(ShippingMethod::class, 'shipping_method_id', 'id');
    }

    function items()
    {
        return $this->hasMany(LineItem::class, 'order_id', 'id');
    }

    function payments()
    {
        return $this->hasMany(Payment::class, 'order_id', 'id');
    }

    function consignments()
    {
        return $this->hasMany(Consignment::class, 'order_id', 'id');
    }

    function addresses()
    {
        return $this->hasMany(OrderAddress::class, 'order_id', 'id');
    }

    function notes()
    {
        return $this->hasMany(OrderNote::class, 'order_id', 'id');
    }

    public function reduceStocks()
    {
        foreach ($this->items as $item) {
            /* @var LineItem $item */
            /* @var Product $product */
            $product = $item->variant ?? $item->product;
            if ($product->stock == -1) {
                continue;
            }
            $reducible = $item->quantity * $product->per;
            $updatedStock = $product->stock - $reducible;
            $updatedStock = $updatedStock < 0 ? 0 : $updatedStock;
            $product->update([
                'stock' => $updatedStock
            ]);
        }
    }

    function addNote($content, $context = 1)
    {
        return $this->notes()->create([
            'content' => $content,
            'context' => $context
        ]);
    }

    public function getPaymentStatusAttribute($v)
    {
        if ($this->payments()->where('status', 1)->count()) return 1;
        if ($this->payments()->where('status', 2)->count()) return 2;
        if ($this->payments()->where('status', 3)->count()) return 3;
        return 0;
    }

    public function getDiscountAttribute($v)
    {
        return round($v, 2);
    }

    public function getShippingChargeAttribute($v)
    {
        return round($v, 2);
    }

    public function getNetTotalAttribute($v)
    {
        return round($this->items()->selectRaw('price*quantity as t')->get()->sum('t'), 2);
    }

    public function getTaxTotalAttribute($v)
    {
        return round($this->items()->selectRaw('tax*quantity as t')->get()->sum('t'), 2);
    }

    public function getGrossTotalAttribute($v)
    {
        return round($this->items()->selectRaw('(price+tax)*quantity as t')->get()->sum('t'), 2);
    }

    public function getStatusStringAttribute()
    {
        switch ($this->status) {
            case 0:
                return 'pending';
                break;
            case 1:
                return 'processing';
                break;
            case 2:
                return 'on the way';
                break;
            case 3:
                return 'completed';
                break;
            case 4:
                return 'hold';
                break;
            case 5:
                return 'canceled';
                break;
            default:
                return '';
                break;
        }
    }

    public function getInvoiceParams()
    {
        $d = $this->toArray();
        switch ($d['status']) {
            case 0:
                $d['status'] = 'pending';
                break;
            case 1:
                $d['status'] = 'processing';
                break;
            case 2:
                $d['status'] = 'on the way';
                break;
            case 3:
                $d['status'] = 'completed';
                break;
            case 4:
                $d['status'] = 'hold';
                break;
            case 5:
                $d['status'] = 'canceled';
                break;
            default:
                $d['status'] = '';
                break;
        }
        $d['created_at'] = $d['created_at'] instanceof Carbon ? $d['created_at']->format('M d, Y h:i a') : Carbon::parse($d['created_at'])->format('M d, Y h:i a');
        $d['currency'] = Currency::getDefaultCurrency()->toArray();
        $d['items_subtotal'] = $this->net_total;
        $d['total_tax'] = $this->tax_total;
        $d['gross_total'] = $this->gross_total + $this->shipping_charge - $this->discount;
        $payment = optional($this->payments()->where('status', 1)->first());
        $d['payment_trx'] = $payment->transaction_id;
        $d['payment_status'] = $payment->status == 1 ? 'completed' : '';
        $d['payment_method'] = $payment->payment_method_name;
        $d['billing'] = $this->addresses()->where('type', 'billing')->first()->toArray();
        $d['shop'] = $this->shop->toArray();
        $d['customer'] = $this->user->toArray();
        $d['shipping'] = $this->addresses()->where('type', 'shipping')->first()->toArray();
        $d['download_link'] = route('order.invoice', [$this->id, 'pdf']);
        $d['preview_link'] = route('order.invoice', [$this->id, 'html']);
        $d['items'] = '<table class="table" cellspacing="0" cellpadding="0"> <tr> <th colspan="2"></th> <th>Qty</th> <th class="text-right">Price</th> </tr>';
        /* @var LineItem $item */
        foreach ($this->items as $item) {
            $prp = optional($item->variant ?? $item->product);
            $attP = '';
            if (is_array($item->attrs)) {
                foreach ($item->attrs as $k => $v) {
                    $attP = $k . '=>' . $v . ', ';
                }
            }
            $p = $d['currency']['symbol'] . ($item->quantity * $item->price);
            $d['items'] .= '<tr> <td class="pr-0"><img src="' . $prp->image . '" class=" rounded" width="64" height="64" alt="" /> </td> <td class="pl-md w-100p"> <strong>' . $item->product_title . '</strong><br /> <span class="text-muted">' . $attP . '</span> </td> <td class="text-center">' . $item->quantity . '</td> <td class="text-right">' . $p . '</td> </tr>';
        }
        $d['items'] .= '</table>';
        return $d;
    }
}
