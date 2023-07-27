@extends('staff.layouts.app')

@section('page_title', ucfirst($type) . ' Templates')

@section('content')

    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Settings</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Settings</li>
            <li class="breadcrumb-item active">{{ ucfirst($type) }} Templates</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->

    @include('layouts.notify')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-content-center">
            <div class="pull-left">
                <h3>All {{ ucfirst($type) }} Templates</h3>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered" id="data-table" style="width:100%">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>{{ $type == 'email'?'Subject':'Title' }}</th>
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
                    ajax: '{{ route('staff.setting.template.index', $type) }}',
                    columns: [
                        {data: 'id', name: 'id'},
                        {data: 'name', name: 'name'},
                        {data: 'title', name: 'title'},
                        {data: 'actions', name: 'actions', orderable: false, searchable: false}
                    ]
                });
            })
        })(jQuery)
    </script>
@endsection
