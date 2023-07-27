<?php

namespace App\Http\Controllers;

use App\Helpers\TemplateBuilder;
use App\Models\Order;
use Illuminate\Http\Request;

class MiscController
{
    public function red()
    {
        return redirect()->route('staff.login');
    }

    /**
     * Display the specified order invoice.
     *
     * @param \App\Order $order
     * @param string $type
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View|void
     */
    public function invoice(Order $order, $type = 'html')
    {
        if (!$order instanceof Order) return abort(404);
        if (!in_array($type, ['html', 'pdf'])) return abort(404);
        $params = $order->getInvoiceParams();
        $invoice = (new TemplateBuilder)->fetch('invoice')->parse($params);
        if ($type == 'pdf') {
            return $invoice->toPdf();
        }
        return $invoice->toView();
    }
}
