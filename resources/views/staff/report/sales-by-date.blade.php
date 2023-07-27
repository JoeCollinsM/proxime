@extends('staff.layouts.app')

@section('page_title', 'Sales By Date')

@section('css_libs')
    <link rel="stylesheet" href="{{ asset('staff/css/daterangepicker.css') }}">
@endsection

@section('content')
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Reports</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Reports</li>
            <li class="breadcrumb-item active">Sales by date</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->
    
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('staff.report.sales-by-date') }}" method="get">
                <div class="row justify-content-end align-items-center">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="date_range">Date Range</label>
                            <div id="date_range"
                                    style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                <i class="fa fa-calendar"></i>&nbsp;
                                <span></span> <i class="fa fa-caret-down"></i>
                            </div>
                            <input type="hidden" name="start_date" id="start_date"
                                    value="{{ request()->start_date??'' }}">
                            <input type="hidden" name="end_date" id="end_date"
                                    value="{{ request()->end_date??'' }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="filter" style="visibility: hidden">Filter</label>
                            <button class="btn btn-outline-info btn-block">
                                Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-content-center">
            <div class="pull-left">
                All Sales
            </div>
            <a href="{{ route('staff.report.sales-by-date', \Illuminate\Support\Arr::add($params, 'download', 1)) }}"
                class="btn btn-success btn-sm"><i
                    class="fa fa-cloud-download"></i> Download
            </a>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered" id="data-table" style="width:100%">
                <thead>
                <tr>
                    <th>Day</th>
                    <th>Order Placed</th>
                    <th>Discount</th>
                    <th>Items</th>
                    <th>Net Amount</th>
                    <th>Tax</th>
                    <th>Gross Amount</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection

@section('js_libs')
    <script src="{{ asset('staff/js/moment.min.js') }}"></script>
    <script src="{{ asset('staff/js/daterangepicker.min.js') }}"></script>
@endsection

@section('js')
    <script>
        (function ($) {
            var start_date = $('#start_date').val()
            var end_date = $('#end_date').val()
            if (start_date != '') {
                var start = moment(start_date, 'DD-MM-YYYY')
            } else {
                var start = moment().startOf('month');
            }
            if (end_date != '') {
                var end = moment(end_date, 'DD-MM-YYYY')
            } else {
                var end = moment().endOf('month');
            }

            function cb(startDate, endDate) {
                $('#start_date').val(startDate.format('DD-MM-YYYY'))
                $('#end_date').val(endDate.format('DD-MM-YYYY'))
                $('#date_range span').html(startDate.format('MMMM D, YYYY') + ' - ' + endDate.format('MMMM D, YYYY'));
            }

            $(document).ready(function () {
                $('#date_range').daterangepicker({
                    startDate: start,
                    endDate: end,
                    showDropdowns: true,
                    format: 'DD-MM-YYYY',
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                        'This Year': [moment().startOf('year'), moment().endOf('year')],
                        'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
                    }
                }, cb);
                cb(start, end);

                $('#data-table').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('staff.report.sales-by-date', $params) }}',
                    columns: [
                        {data: 'day', name: 'day', searchable: false},
                        {data: 'total_orders', name: 'total_orders', searchable: false},
                        {data: 'total_discount', name: 'total_discount', searchable: false},
                        {data: 'total_item_count', name: 'total_item_count', searchable: false},
                        {data: 'total_net_amount', name: 'total_net_amount', searchable: false},
                        {data: 'total_tax_amount', name: 'total_tax_amount', searchable: false},
                        {data: 'total_gross_amount', name: 'total_gross_amount', searchable: false}
                    ]
                });
            })
        })(jQuery)
    </script>
@endsection
