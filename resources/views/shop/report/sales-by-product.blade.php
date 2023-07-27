@extends('shop.layouts.app')

@section('page_title', 'Sales By Product')

@section('css_libs')
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/select2/css/select2-bootstrap.css') }}">
@endsection

@section('css')
    <style>
        [v-cloak] {
            display: none;
        }
    </style>
@endsection

@section('content')
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Reports</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Sales by Product</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->

            
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('shop.report.sales-by-product') }}" method="get">
                        <div class="row justify-content-end align-items-center" id="app" v-cloak>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="product_id">Product</label>
                                    <select name="product_id" id="product_id" class="form-control select2">
                                        <option value="*" {{ $params['product_id'] == '*'?'selected':'' }}>All Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ $params['product_id'] == $product->id?'selected':'' }}>{{ $product->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="variation_id">Variant</label>
                                    <select name="variation_id" id="variation_id" class="form-control" v-model="variation_id">
                                        <option v-for="(variant, index) in filteredVariations" :key="index" :value="variant.id">@{{ variant.title }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="filter" style="visibility: hidden">Filter</label>
                                    <button class="btn btn-outline-info btn-block">
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
                    <a href="{{ route('shop.report.sales-by-product', \Illuminate\Support\Arr::add($params, 'download', 1)) }}"
                       class="btn btn-success btn-sm"><i
                            class="fa fa-cloud-download"></i> Download
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered" id="data-table" style="width:100%">
                        <thead>
                        <tr>
                            <th>Product/Variant</th>
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
    <script src="{{ asset('staff/js/vue.js') }}"></script>
@endsection

@section('js')
    <script>
        (function ($) {
            function adjustVariations() {
                var val = $('select[name="product_id"]').val();
                window.app.filteredVariations = window.app.variations.filter(variant => {
                    return parseInt(variant.parent_id) === parseInt(val) || variant.id === '';
                })
            }
            $(document).on('change', 'select[name="product_id"]', function (e) {
                adjustVariations()
            })
            $(document).ready(function () {
                $('.select2').select2({
                    theme: 'bootstrap'
                });
                adjustVariations()
                $('#data-table').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('shop.report.sales-by-product', $params) }}',
                    columns: [
                        {data: 'title', name: 'title'},
                        {data: 'item_count', name: 'item_count', searchable: false},
                        {data: 'net_total', name: 'net_total', searchable: false},
                        {data: 'tax_total', name: 'tax_total', searchable: false}
                    ]
                });
            })
        })(jQuery)
    </script>
    <script>
        window.app = new Vue({
            el: '#app',
            data: {
                variations: @json($variations),
                filteredVariations: [],
                variation_id: '{{ $params['variation_id']??'*' }}'
            },
            mounted() {
                this.variations.push({
                    id: '',
                    title: 'N/A'
                })
            }
        })
    </script>
@endsection
