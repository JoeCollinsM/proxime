<?php

namespace App\Http\Controllers\Shop;

use App\Models\Category;
use App\Models\Currency;
use App\Exports\SalesByProductExport;
use App\Exports\SalesByDateExport;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTableAbstract;

class ReportController
{
    function salesByDate(Request $request, $download = null)
    {
        /* @var Shop $shop */
        $shop = $request->user('shop');
        $query = Order::query()->where('shop_id', $shop->id);
        if (file_exists(database_path('sqls/get-sales-by-date.sql'))) {
            $query->selectRaw(file_get_contents(database_path('sqls/get-sales-by-date.sql')));
        }

        $startDate = Carbon::today()->firstOfMonth();
        if ($request->start_date) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->start_date);
        }
        $query->whereDate('created_at', '>=', $startDate);

        $endDate = Carbon::today()->lastOfMonth();
        if ($request->end_date) {
            $endDate = Carbon::createFromFormat('d-m-Y', $request->end_date);
            if ($endDate instanceof Carbon) {
                $query->whereDate('created_at', '<=', $endDate);
            }
        }
        $query->whereDate('created_at', '<=', $endDate);

        $query->groupBy('day');
        if ($download) {
            return (new SalesByDateExport(clone $query))->download('sales-by-date.xlsx');
        }
        $currency = Currency::getDefaultCurrency();
        if ($request->ajax()) {
            /* @var DataTableAbstract $table */
            $table = datatables()->of($query);
            $table->editColumn('day', function ($row) {
                return Carbon::createFromFormat('Y-m-d', $row->day)->format('M d');
            });
            $table->editColumn('total_discount', function ($row) use ($currency) {
                return $currency->symbol . '' . $row->total_discount;
            });
            $table->editColumn('total_net_amount', function ($row) use ($currency) {
                return $currency->symbol . '' . $row->total_net_amount;
            });
            $table->editColumn('total_tax_amount', function ($row) use ($currency) {
                return $currency->symbol . '' . $row->total_tax_amount;
            });
            $table->editColumn('total_gross_amount', function ($row) use ($currency) {
                return $currency->symbol . '' . $row->total_gross_amount;
            });
            return $table->make(true);
        }
        $params = ['start_date' => $startDate->format('d-m-Y'), 'end_date' => $endDate->format('d-m-Y')];
        return view('shop.report.sales-by-date', compact('currency', 'params'));
    }

    function salesByProduct(Request $request, $download = null)
    {
        /* @var Shop $shop */
        $shop = $request->user('shop');
        $query = Product::query()->where('shop_id', $shop->id);
        if (is_numeric($request->variation_id)) {
            if (file_exists(database_path('sqls/get-sales-by-variation.sql'))) {
                $query->selectRaw(file_get_contents(database_path('sqls/get-sales-by-variation.sql')));
            }
            $query->where('id', $request->variation_id);
        } else {
            if (file_exists(database_path('sqls/get-sales-by-product.sql'))) {
                $query->selectRaw(file_get_contents(database_path('sqls/get-sales-by-product.sql')));
            }
            if (is_numeric($request->product_id)) {
                $query->where('id', $request->product_id);
            } else {
                $query->whereNull('parent_id')->whereHas('sales');
            }
        }
        if ($download) {
            return (new SalesByProductExport(clone $query))->download('sales-by-product.xlsx');
        }
        $currency = Currency::getDefaultCurrency();
        if ($request->ajax()) {
            /* @var DataTableAbstract $table */
            $table = datatables()->of($query);
            $table->editColumn('net_total', function ($row) use ($currency) {
                return $currency->symbol . '' . $row->net_total;
            });
            $table->editColumn('tax_total', function ($row) use ($currency) {
                return $currency->symbol . '' . $row->tax_total;
            });
            return $table->make(true);
        }
        $params = ['product_id' => $request->product_id, 'variation_id' => $request->variation_id];
        $products = Product::query()->where('shop_id', $shop->id)->whereNull('parent_id')->where('status', 1)->get();
        $variations = Product::query()->where('shop_id', $shop->id)->with('attrs')->whereNotNull('parent_id')->get()->map(function (Product $product) {
            $product->setRelation('attrs', $product->attrs->pluck('content', 'name'));
            return $product;
        });
        return view('shop.report.sales-by-product', compact('currency', 'params', 'products', 'variations'));
    }
}
