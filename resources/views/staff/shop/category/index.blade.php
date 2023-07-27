@extends('staff.layouts.app')

@section('page_title', 'Shop Categories')

@section('content')
     <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Shop Categories</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('staff.shop.index') }}">Shop</a></li>
            <li class="breadcrumb-item active">Category</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->

    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-content-center">
            <div class="pull-left">
                Shop Categories
            </div>
            <a href="{{ route('staff.shop-category.create') }}"
                class="btn btn-success btn-sm"><i
                    class="fa fa-plus"></i> Add New Category
            </a>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered" id="data-table" style="width:100%">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Parent</th>
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
                    <h4 class="modal-title">Delete Shop Category</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                </div>
                <form method="post">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <h4 class="text-center">Are you sure to delete this shop category?</h4>
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
                    ajax: '{{ route('staff.shop-category.index') }}',
                    columns: [
                        {data: 'id', name: 'id'},
                        {data: 'name', name: 'name'},
                        {data: 'parent.name', name: 'parent.name', orderable: false,},
                        {data: 'actions', name: 'actions', orderable: false, searchable: false}
                    ]
                });
            })

            $(document).on('click', '.btn-delete', function (e) {
                e.preventDefault();
                var id = $(this).data('id');
                var url = '{{ url('staff/shop-category') }}/' + id
                $('#delete-modal form').prop('action', url);
                $('#delete-modal').modal();
            })
        })(jQuery)
    </script>
@endsection
