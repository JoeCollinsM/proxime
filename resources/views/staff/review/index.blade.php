@extends('staff.layouts.app')

@section('page_title', $title)

@section('content')

    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Reviews</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Reviews</li>
            <li class="breadcrumb-item active">{{ $title }}</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->
    
            <div class="card">
                <div class="card-header justify-content-between align-content-center">
                    <div class="pull-left">
                        <h3>{{ $title }}</h3>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-striped table-bordered" id="data-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Rating</th>
                            <th>At</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>

    <div class="modal fade" id="view-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <p class="text-black-50">
                        Rating: <span id="modal-rating"></span>
                    </p>
                    <p class="text-black-50">
                        Review: <span id="modal-content"></span>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

@endsection

@section('js')
    <script>
        (function ($) {
            $(document).on('click', '.btn-view', function (e) {
                e.preventDefault()
                const rating = $($(this).data('rating')).html();
                const content = $(this).data('content');
                $('#modal-rating').html(rating)
                $('#modal-content').html(content)
                $('#view-modal').modal('show')
            })
            $(document).ready(function () {
                $('#data-table').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('staff.catalog.review.index', request()->only(['type', 'type_id'])) }}',
                    columns: [
                        {data: 'id', name: 'id'},
                        {data: 'user.name', name: 'user.name'},
                        {data: 'rating', name: 'rating'},
                        {data: 'created_at', name: 'created_at'},
                        {data: 'actions', name: 'actions', searchable: false, orderable: false}
                    ]
                });
            })
        })(jQuery)
    </script>
@endsection
