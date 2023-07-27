@extends('staff.layouts.app')

@section('page_title', 'Withdraws')

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
        table tr td:last-child {
            display: flex;
        }
    </style>
@endsection

@section('content')
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Withdraws</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Withdraws</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->


    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-content-center">
            <div class="pull-left">
                All Withdraws
            </div>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered" id="data-table" style="width:100%">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Method</th>
                    <th>Amount</th>
                    <th>Charge</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="show-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Withdraw Fields</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body fields">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left btn-round" data-dismiss="modal">Close
                    </button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="accept-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Accept Withdraw Request</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                </div>
                <form method="post">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="action" value="accept">
                    <div class="modal-body">
                        <h5 class="text-center">Are you sure to accept this request?</h5>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left btn-round" data-dismiss="modal">Close
                        </button>
                        <button type="submit" class="btn btn-primary btn-round">Accept</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="reject-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Reject Withdraw Request</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                </div>
                <form method="post">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="action" value="reject">
                    <div class="modal-body">
                        <h5 class="text-center">Are you sure to reject & refund this request?</h5>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left btn-round" data-dismiss="modal">Close
                        </button>
                        <button type="submit" class="btn btn-outline-danger btn-round">Reject</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="d-none" id="yh-filter-inputs">
        <div class="row justify-content-between align-items-center">
            <div class="col-md-3">
                <select id="user_type" class="custom-select">
                    <option value="*">All</option>
                    <option value="1">Delivery Man</option>
                    <option value="2">Shop/Vendor</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="status" class="custom-select">
                    <option value="*">All</option>
                    <option value="0">Pending</option>
                    <option value="1">Accepted</option>
                    <option value="2">Refunded</option>
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

            $(document).on('input change', '#user_type, #status, #start_date, #end_date', function () {
                try {
                    window.withdrawTable.draw()
                } catch (e) {

                }
            })

            $(document).on('click', '.btn-show', function (e) {
                e.preventDefault();
                let fields = $(this).data('fields');
                if (typeof fields == 'object') {
                    let html = ''
                    fields.forEach(field => {
                        html += '<div class="single-field border my-2 p-2"><p class="font-weight-bold">' + field.title + '</p><p>' + field.value + '</p></div>'
                    })
                    $('#show-modal .fields').html(html)
                }
                $('#show-modal').modal()
            })

            $(document).on('click', '.btn-approve', function (e) {
                e.preventDefault();
                var id = $(this).data('id');
                var url = '{{ url('staff/withdraw') }}/' + id
                $('#accept-modal form').prop('action', url);
                $('#accept-modal').modal();
            })

            $(document).on('click', '.btn-refund', function (e) {
                e.preventDefault();
                var id = $(this).data('id');
                var url = '{{ url('staff/withdraw') }}/' + id
                $('#reject-modal form').prop('action', url);
                $('#reject-modal').modal();
            })

            $(document).ready(function () {
                window.withdrawTable = $('#data-table').DataTable({
                    // dom:"<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                    dom: "<'row justify-content-between align-items-center'<'col-sm-12 custom-yh-fields-left col-md-6'><'col-sm-12 custom-yh-fields-right col-md-4'f>><'row'<'col-sm-12'tr>><'row justify-content-between align-items-center'<'col-sm-12 col-md'l><'col-sm-12 col-md'i><'col-sm-12 col-md'p>>",
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('staff.withdraw.index') }}',
                        data: function (d) {
                            return $.extend({}, d, {
                                user_type: ($('#user_type').val() || '*'),
                                status: ($('#status').val() || '*'),
                                start_date: ($('#start_date').val() || null),
                                end_date: ($('#end_date').val() || null)
                            });
                        }
                    },
                    columns: [
                        {data: 'id', name: 'id'},
                        {data: 'user.name', name: 'user.name', orderable: false},
                        {data: 'method.name', name: 'method.name', orderable: false},
                        {data: 'amount', name: 'amount'},
                        {data: 'charge', name: 'charge'},
                        {data: 'status', name: 'status'},
                        {data: 'created_at', name: 'created_at', searchable: false},
                        {data: 'action', name: 'action', orderable: false, searchable: false},
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
