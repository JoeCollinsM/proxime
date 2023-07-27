@extends('shop.layouts.app')

@section('page_title', 'All Orders')

@section('content')
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Orders</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('shop.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Orders</li>
            </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->)
            <div class=" card">
                <div class=" card-header d-flex justify-content-between align-content-center">
                    <div class="pull-left">
                        <h3>All Orders</h3>
                    </div>
                </div>
                <div class=" card-body">
                    <table class="table table-striped table-bordered" id="data-table" style="width:100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Order</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>

@endsection

@section('js')
    <script>
        (function ($) {
            $(document).ready(function () {
                $('#data-table').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('shop.catalog.order.index') }}',
                    columns: [
                        {data: 'id', name: 'id'},
                        {data: 'order', name: 'order', orderable: false},
                        {data: 'created_at', name: 'created_at'},
                        {data: 'status', name: 'status'},
                        {data: 'total', name: 'total', searchable: false}
                    ],
                    order: [[ 0, 'desc' ]]
                });
            })
        })(jQuery)
    </script>
@endsection
