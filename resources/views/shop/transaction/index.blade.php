@extends('shop.layouts.app')

@section('page_title', 'Transactions')

@section('css_libs')
    <link rel="stylesheet" href="{{ asset('staff/css/daterangepicker.css') }}">
@endsection

@section('css')
    <style>
        #date_range {
            background: #fff;
            cursor: pointer;
            padding: 4px 10px;
            border: 1px solid #ccc;
            width: 100%
        }
    </style>
@endsection

@section('content')
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Transactions</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Transactions</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->
            
            <div class="card">
                <div class="card-header d-flex justify-content-between align-content-center">
                    <div class="pull-left">
                        All Transactions
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered" id="data-table" style="width:100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Amount</th>
                            <th>Created At</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>


    <div class="d-none" id="yh-filter-inputs">
        <div class="row justify-content-between align-items-center">
            <div class="col-md-3">
                <select id="type" class="custom-select">
                    <option value="*">@lang('All Type')</option>
                    <option value="+">+ in wallet</option>
                    <option value="-">- from wallet</option>
                </select>
            </div>
            <div class="col-md-6">
                <div id="date_range">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span> <i class="fa fa-caret-down"></i>
                </div>
                <input type="hidden" id="start_date"
                       value="{{ request()->start_date??'' }}">
                <input type="hidden" id="end_date"
                       value="{{ request()->end_date??'' }}">
            </div>
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
                var start = null;
            }
            if (end_date != '') {
                var end = moment(end_date, 'DD-MM-YYYY')
            } else {
                var end = null;
            }

            function cb(startDate, endDate) {
                if (startDate) {
                    $('#start_date').val(startDate.format('DD-MM-YYYY')).trigger('input')
                }
                if (endDate) {
                    $('#end_date').val(endDate.format('DD-MM-YYYY')).trigger('input')
                }
                if (startDate && endDate) {
                    $('#date_range span').html(startDate.format('MMMM D, YYYY') + ' - ' + endDate.format('MMMM D, YYYY'));
                }
            }

            $(document).on('input change', '#type, #start_date, #end_date', function () {
                try {
                    window.transactionTable.draw()
                } catch (e) {

                }
            })

            $(document).ready(function () {
                window.transactionTable = $('#data-table').DataTable({
                    // dom:"<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                    dom: "<'row justify-content-between align-items-center'<'col-sm-12 custom-yh-fields-left col-md-6'><'col-sm-12 custom-yh-fields-right col-md-4'f>><'row'<'col-sm-12'tr>><'row justify-content-between align-items-center'<'col-sm-12 col-md'l><'col-sm-12 col-md'i><'col-sm-12 col-md'p>>",
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('shop.transaction.index') }}',
                        data: function (d) {
                            return $.extend({}, d, {
                                type: ($('#type').val() || null),
                                start_date: ($('#start_date').val() || null),
                                end_date: ($('#end_date').val() || null)
                            });
                        }
                    },
                    columns: [
                        {data: 'id', name: 'id'},
                        {data: 'title', name: 'title'},
                        {data: 'amount', name: 'amount'},
                        {data: 'created_at', name: 'created_at', searchable: false}
                    ],
                    order: [[0, 'desc']]
                });
                $('.custom-yh-fields-left').html($('#yh-filter-inputs').html())
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
            })
        })(jQuery)
    </script>
@endsection
