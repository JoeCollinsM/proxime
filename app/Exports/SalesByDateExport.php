<?php

namespace App\Exports;

use App\Models\Currency;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesByDateExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    private $query;

    function __construct($query)
    {
        $this->query = $query;
    }

    /**
     * @return Builder
     */
    public function query()
    {
        return $this->query;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return ['Day', 'Order Placed', 'Discount', 'Items', 'Net Amount', 'Tax', 'Gross Amount'];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        $currency = Currency::getDefaultCurrency();
        return [
            Carbon::createFromFormat('Y-m-d', $row->day)->format('M d'),
            $row->total_orders,
            $currency->symbol . '' . $row->total_discount,
            $row->total_item_count,
            $currency->symbol . '' . $row->total_net_amount,
            $currency->symbol . '' . $row->total_tax_amount,
            $currency->symbol . '' . $row->total_gross_amount
        ];
    }
}
