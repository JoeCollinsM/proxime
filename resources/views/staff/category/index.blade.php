@extends('staff.layouts.app')

@section('page_title', 'Categories')

@section('css_libs')
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/select2/css/select2-bootstrap.css') }}">
@endsection

@section('content')

    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Categories</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Categories</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->


    <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header "><i class="fa fa-table"></i> Categories list
            <div class="btn-group float-sm-right">
            <a class="btn btn-success waves-effect waves-light" href="{{ route('staff.catalog.category.create') }}"><i class="fa fa-plus mr-1"></i> Add new</a>
            </div>  
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="data-table" style="width:100%" >
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
        </div>
    </div><!-- End Row-->

            <div class="modal fade" id="delete-modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Delete Category</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                        </div>
                        <form method="post">
                            @csrf
                            @method('DELETE')
                            <div class="modal-body">
                                <h3 class="text-center">Are you sure to delete this category?</h3>
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

        </div>
    </div>
@endsection

@section('js_libs')
    <script src="{{ asset('staff/admin/plugins/select2/js/select2.min.js') }}"></script>
@endsection

@section('js')
    <script>
        (function ($) {
            // $.fn.select2.defaults.set("theme", "bootstrap");
            $(document).ready(function () {
                $('#data-table').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('staff.catalog.category.index') }}',
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
                var url = '{{ url('staff/catalog/category') }}/' + id
                $('#delete-modal form').prop('action', url);
                $('#delete-modal').modal();
            })

         
        })(jQuery)
    </script>
@endsection
