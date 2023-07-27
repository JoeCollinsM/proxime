@extends('shop.layouts.app')

@section('page_title', 'All Products')

@section('content')
     <!-- Breadcrumb-->
     <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Products</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">All Products</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->
            
            <div class="card">
                <div class="card-header d-flex justify-content-between align-content-center">
                    <div class="pull-left">
                        All Products
                    </div>
                    <div class="float-sm-right btn-group">
                        <a href="{{ route('shop.catalog.product.create') }}"
                           class="btn btn-success btn-sm"><i
                                    class="fa fa-plus"></i> Add New
                        </a>
                        <button id="import" class="btn btn-info btn-sm"><i
                                    class="fa fa-cloud-upload"></i> Import
                        </button>
                        <a href="{{ asset('staff/proxime-product-import-template-for-shop.xlsx') }}" id="import" class="btn btn-primary btn-sm" download><i
                                    class="fa fa-cloud-download"></i> Import Template
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered" id="data-table"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
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
                            <h4 class="modal-title">Delete Product</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                        </div>
                        <form method="post">
                            @csrf
                            @method('DELETE')
                            <div class="modal-body">
                                <h4 class="text-center">Are you sure to delete this product?</h4>
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


    <form id="import-form" action="{{ route('shop.catalog.product.import') }}" class="d-none" method="POST">
        @csrf
        <input type="hidden" name="path" value="">
    </form>
@endsection

@section('js')
    <script>
        (function ($) {
            $(document).ready(function () {
                $('#data-table').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('shop.catalog.product.index') }}',
                    columns: [
                        {data: 'id', name: 'id'},
                        {data: 'title', name: 'title'},
                        {data: 'category.name', name: 'category.name', orderable: false},
                        {data: 'sale_price', name: 'sale_price', searchable: false},
                        {data: 'stock', name: 'stock', orderable: false},
                        {data: 'status', name: 'status'},
                        {data: 'actions', name: 'actions', orderable: false, searchable: false}
                    ]
                });
            })

            $(document).on('click', '#import', function (e) {
                e.preventDefault()
                openMediaManager(items => {
                    if (items.length) {
                        let item = items[0]
                        if (item.hasOwnProperty('path')) {
                            $('#import-form [name="path"]').val(item.path)
                            $('#import-form').submit()
                        }
                    }
                }, 'excel', 'Select Excel File')
            })

            $(document).on('click', '.btn-delete', function (e) {
                e.preventDefault();
                var id = $(this).data('id');
                var url = '{{ url('shop/catalog/product') }}/' + id
                $('#delete-modal form').prop('action', url);
                $('#delete-modal').modal();
            })
        })(jQuery)
    </script>
@endsection
