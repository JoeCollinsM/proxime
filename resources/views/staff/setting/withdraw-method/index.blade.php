@extends('staff.layouts.app')

@section('page_title', 'All Withdraw Method')

@section('content')
    
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Withdraw settings</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Withdraw</li>
            <li class="breadcrumb-item active">Withdraw Method</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->

            
            <div class="card">
                <div class="card-header d-flex justify-content-between align-content-center">
                    <div class="pull-left">
                        All Methods
                    </div>
                    <a href="{{ route('staff.setting.withdraw-method.create') }}"
                       class="btn btn-success btn-sm"><i
                                class="fa fa-plus"></i> Add New Method
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered" id="data-table" style="width:100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Minimum</th>
                            <th>Maximum</th>
                            <th>Charge</th>
                            <th>Status</th>
                            <th>Actions</th>
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
                    ajax: '{{ route('staff.setting.withdraw-method.index') }}',
                    columns: [
                        {data: 'id', name: 'id'},
                        {data: 'name', name: 'name'},
                        {data: 'min', name: 'min', orderable: false, searchable: false},
                        {data: 'max', name: 'max', orderable: false, searchable: false},
                        {data: 'charge', name: 'charge', orderable: false, searchable: false},
                        {data: 'status', name: 'status'},
                        {data: 'actions', name: 'actions', orderable: false, searchable: false}
                    ]
                });
            })
        })(jQuery)
    </script>
@endsection
