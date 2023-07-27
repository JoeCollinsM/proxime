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

class SalesByProductExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
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
        return ['Product/Variation', 'Items', 'Net Amount', 'Tax'];
    }

    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        $currency = Currency::getDefaultCurrency();
        return [
            $row->title,
            $row->item_count,
            $currency->symbol . '' . $row->net_total,
            $currency->symbol . '' . $row->tax_total
        ];
    }
}
