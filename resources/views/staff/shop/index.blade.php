@extends('staff.layouts.app')

@section('page_title', 'All Shops')

@section('content')

 <!-- Breadcrumb-->
 <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Shop</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Shop</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->

    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-content-center">
            <div class="pull-left">
                All Shops
            </div>
            <a href="{{ route('staff.shop.create') }}"
                class="btn btn-success btn-sm"><i
                    class="fa fa-plus"></i> Add New Shop
            </a>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered" id="data-table" style="width:100%">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Logo</th>
                    <th>Title</th>
                    <th>Category</th>
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
                    <h4 class="modal-title">Delete Shop</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                </div>
                <form method="post">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <h4 class="text-center">Are you sure to delete this shop?</h4>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-round pull-left" data-dismiss="modal">Close
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
                    ajax: '{{ route('staff.shop.index') }}',
                    columns: [
                        {data: 'id', name: 'id'},
                        {data: 'logo', name: 'logo', orderable: false, searchable: false},
                        {data: 'name', name: 'name'},
                        {data: 'category.name', name: 'category.name', orderable: false},
                        {data: 'status', name: 'status'},
                        {data: 'actions', name: 'actions', orderable: false, searchable: false}
                    ]
                });
            })

            $(document).on('click', '.btn-delete', function (e) {
                e.preventDefault();
                var id = $(this).data('id');
                var url = '{{ url('staff/shop') }}/' + id
                $('#delete-modal form').prop('action', url);
                $('#delete-modal').modal();
            })
        })(jQuery)
    </script>
@endsection
