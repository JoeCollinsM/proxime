@extends('staff.layouts.app')

@section('page_title', 'All Delivery Man')

@section('content')
     <!-- Breadcrumb-->
     <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Delivery Man</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Delivery Man</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->
            
            <div class="card">
                <div class="card-header d-flex justify-content-between align-content-center">
                    <div class="pull-left">
                        All Delivery Man
                    </div>
                    <a href="{{ route('staff.catalog.delivery-man.create') }}"
                       class="btn btn-success btn-sm"><i
                            class="fa fa-plus"></i> Add New Delivery Man
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered" id="data-table"  style="width:100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <div class="modal fade" id="delete-modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Delete Delivery Man</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                        </div>
                        <form method="post">
                            @csrf
                            @method('DELETE')
                            <div class="modal-body">
                                <h4 class="text-center">Are you sure to delete this delivery man?</h4>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default pull-left btn-round" data-dismiss="modal">Close
                                </button>
                                <button type="submit" class="btn btn-danger btn-round">Delete</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
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
                    ajax: '{{ route('staff.catalog.delivery-man.index') }}',
                    columns: [
                        {data: 'id', name: 'id'},
                        {data: 'username', name: 'username'},
                        {data: 'email', name: 'email'},
                        {data: 'phone', name: 'phone'},
                        {data: 'balance', name: 'balance'},
                        {data: 'status', name: 'status'},
                        {data: 'actions', name: 'actions', orderable: false, searchable: false}
                    ]
                });
            })

            $(document).on('click', '.btn-delete', function (e) {
                e.preventDefault();
                var id = $(this).data('id');
                var url = '{{ url('staff/catalog/delivery-man') }}/' + id
                $('#delete-modal form').prop('action', url);
                $('#delete-modal').modal();
            })
        })(jQuery)
    </script>
@endsection
