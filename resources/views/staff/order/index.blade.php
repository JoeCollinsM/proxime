@extends('staff.layouts.app')

@section('page_title', 'All Orders')

@section('content')
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Categories</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">All Orders</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->


    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header"><i class="fa fa-table"></i> All Orders
                    <div class="btn-group float-sm-right">
                        <a class="btn btn-success btn-sm  waves-effect waves-light" href="{{ route('staff.catalog.order.create') }}"><i class="fa fa-plus mr-1"></i> Add new</a>
                    </div>  
                </div>
            
                <div class="card-body">
                    <table class="table table-bordered" id="data-table"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Shop</th>
                            <th>Order</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>

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
                    ajax: '{{ route('staff.catalog.order.index') }}',
                    columns: [
                        {data: 'id', name: 'id'},
                        {data: 'shop.name', name: 'shop.name', orderable: false},
                        {data: 'order', name: 'order', orderable: false},
                        {data: 'created_at', name: 'created_at'},
                        {data: 'status', name: 'status'},
                        {data: 'total', name: 'total', searchable: false}
                    ],
                    order: [[0, 'desc']]
                });
            })
        })(jQuery)
    </script>
@endsection
