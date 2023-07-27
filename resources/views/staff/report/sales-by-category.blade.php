@extends('staff.layouts.app')

@section('page_title', 'Sales By Category')

@section('css_libs')
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/select2/css/select2-bootstrap.css') }}">
@endsection

@section('content')
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Reports</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Reports</li>
            <li class="breadcrumb-item active">Sales by category</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->

  
    
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('staff.report.sales-by-category') }}" method="get">
                <div class="row justify-content-end align-items-center">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select name="category_id" id="category_id" class="form-control select2">
                                <option value="*" {{ !count($params['category_id'])?'selected':'' }}>All Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ in_array($category->id, $params['category_id'])?'selected':'' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="filter" style="visibility: hidden">Filter</label>
                            <button class="btn btn-outline-info btn-block ">
                                Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-content-center">
            <div class="pull-left">
                All Sales
            </div>
            <a href="{{ route('staff.report.sales-by-category', \Illuminate\Support\Arr::add($params, 'download', 1)) }}"
                class="btn btn-success btn-sm"><i
                    class="fa fa-cloud-download"></i> Download
            </a>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered" id="data-table" style="width:100%">
                <thead>
                <tr>
                    <th>Category</th>
                    <th>Items</th>
                    <th>Net Amount</th>
                    <th>Tax</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
        
@endsection

@section('js_libs')
    <script src="{{ asset('staff/admin/plugins/select2/js/select2.min.js') }}"></script>
@endsection

@section('js')
    <script>
        (function ($) {
            $(document).ready(function () {
                $('.select2').select2({
                    theme: 'bootstrap'
                });

                $('#data-table').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('staff.report.sales-by-category', $params) }}',
                    columns: [
                        {data: 'name', name: 'name'},
                        {data: 'item_count', name: 'item_count', searchable: false},
                        {data: 'net_total', name: 'net_total', searchable: false},
                        {data: 'tax_total', name: 'tax_total', searchable: false}
                    ]
                });
            })
        })(jQuery)
    </script>
@endsection
