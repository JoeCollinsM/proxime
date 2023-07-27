@extends('staff.layouts.app')

@section('page_title', 'Coupon Manager')

@section('css_libs')
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/bootstrap-datepicker/css/bootstrap-datepicker.standalone.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/select2/css/select2-bootstrap.css') }}">
@endsection

@section('content')

    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Coupons</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Coupons</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->

    
    <div class="card">
        <div class="card-header">
            All Coupons
            <div class="btn-group float-sm-right">
            <button class="btn btn-success btn-sm  waves-effect waves-light" data-toggle="modal"
                    data-target="#new-modal"><i
                    class="fa fa-plus"></i> Add New Coupon
            </button>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered" id="data-table" style="width:100%">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>CODE</th>
                    <th>Start At</th>
                    <th>Expire At</th>
                    <th>Min</th>
                    <th>Dis.</th>
                    <th>Use Limit</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
       

    <div class="modal fade" id="new-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add New Coupon</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                </div>
                <form action="{{ route('staff.catalog.coupon.store') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label">Coupon Code</label>
                            <input type="text" name="code" value="{{ old('code') }}" class="form-control">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Start At</label>
                                    <input type="text" name="start_at" value="{{ old('start_at') }}"
                                           class="form-control date-picker">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Expire At</label>
                                    <input type="text" name="expire_at" value="{{ old('expire_at') }}"
                                           class="form-control date-picker">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Select Customers</label>
                            <select name="customers[]" class="form-control select2" multiple>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                            @if(is_array(old('customers')) && in_array($customer->id, old('customers'))) selected @endif>{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Select Products</label>
                            <select name="products[]" class="form-control select2" multiple>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}"
                                            @if(is_array(old('products')) && in_array($product->id, old('products'))) selected @endif>{{ $product->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Discount Type</label>
                                    <select name="discount_type" class="form-control">
                                        <option value="1" @if(old('discount_type') == 1) selected @endif>Percent
                                        </option>
                                        <option value="2" @if(old('discount_type') == 2) selected @endif>Fixed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Discount Amount</label>
                                    <input type="number" step="0.01" name="amount" value="{{ old('amount') }}"
                                           class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md">
                                <div class="form-group">
                                    <label class="control-label">Upto</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" name="upto" value="{{ old('upto') }}"
                                               class="form-control"
                                               placeholder="10">
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{ $currency->code }}</span>
                                        </div>
                                    </div>
                                    <p class="help-block text-info">-1 for no upto, only applicable for percent
                                        discount</p>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group">
                                    <label class="control-label">Use Limit</label>
                                    <input type="number" step="0.01" name="maximum_use_limit"
                                           value="{{ old('maximum_use_limit') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group">
                                    <label class="control-label">Minimum Cart Amount</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" name="min" value="{{ old('min') }}"
                                               class="form-control"
                                               placeholder="10">
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{ $currency->code }}</span>
                                        </div>
                                    </div>
                                    <p class="help-block text-info">-1 for no restriction</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-round pull-left " data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-round">Save</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="edit-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Coupon</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                </div>
                <form method="post">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label">Coupon Code</label>
                            <input type="text" name="code" id="code" value="{{ old('code') }}" class="form-control">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Start At</label>
                                    <input type="text" name="start_at" id="start_at" value="{{ old('start_at') }}"
                                           class="form-control date-picker">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Expire At</label>
                                    <input type="text" name="expire_at" id="expire_at" value="{{ old('expire_at') }}"
                                           class="form-control date-picker">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Select Customers</label>
                            <select name="customers[]" id="customers" class="form-control select2" multiple>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                            @if(is_array(old('customers')) && in_array($customer->id, old('customers'))) selected @endif>{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Select Products</label>
                            <select name="products[]" id="products" class="form-control select2" multiple>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}"
                                            @if(is_array(old('products')) && in_array($product->id, old('products'))) selected @endif>{{ $product->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Discount Type</label>
                                    <select name="discount_type" id="discount_type" class="form-control">
                                        <option value="1" @if(old('discount_type') == 1) selected @endif>Percent
                                        </option>
                                        <option value="2" @if(old('discount_type') == 2) selected @endif>Fixed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Discount Amount</label>
                                    <input type="number" step="0.01" name="amount" id="amount"
                                           value="{{ old('amount') }}" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md">
                                <div class="form-group">
                                    <label class="control-label">Upto</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" name="upto" id="upto" value="{{ old('upto') }}"
                                               class="form-control"
                                               placeholder="10">
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{ $currency->code }}</span>
                                        </div>
                                    </div>
                                    <p class="help-block text-info">-1 for no upto, only applicable for percent
                                        discount</p>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group">
                                    <label class="control-label">Use Limit</label>
                                    <input type="number" step="0.01" name="maximum_use_limit" id="maximum_use_limit"
                                           value="{{ old('maximum_use_limit') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group">
                                    <label class="control-label">Minimum Cart Amount</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" name="min" id="min" value="{{ old('min') }}"
                                               class="form-control"
                                               placeholder="10">
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{ $currency->code }}</span>
                                        </div>
                                    </div>
                                    <p class="help-block text-info">-1 for no restriction</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-round pull-left" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-round">Update</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="delete-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Delete Coupon</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                </div>
                <form method="post">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <h4 class="text-center">Are you sure to delete this coupon?</h4>
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

@section('js_libs')
    <script src="{{ asset('staff/admin/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('staff/admin/plugins/select2/js/select2.min.js') }}"></script>
@endsection

@section('js')
    <script>
        (function ($) {
            $.fn.select2.defaults.set("theme", "bootstrap");
            $(document).ready(function () {
                $('#new-modal .select2').select2()
                $('.date-picker').datepicker({
                    format: "dd-mm-yyyy"
                })
                $('#data-table').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('staff.catalog.coupon.index') }}',
                    columns: [
                        {data: 'id', name: 'id'},
                        {data: 'code', name: 'code'},
                        {data: 'start_at', name: 'start_at'},
                        {data: 'expire_at', name: 'expire_at'},
                        {data: 'min', name: 'min'},
                        {data: 'amount', name: 'amount'},
                        {data: 'maximum_use_limit', name: 'maximum_use_limit'},
                        {data: 'status', name: 'status', orderable: false, searchable: false},
                        {data: 'actions', name: 'actions', orderable: false, searchable: false}
                    ]
                });
            })
            $(document).on('click', '.btn-edit', function (e) {
                e.preventDefault();

                var datas = [];
                [].forEach.call(this.attributes, function (attr) {
                    if (/^data-/.test(attr.name)) {
                        var camelCaseName = attr.name.substr(5).replace(/-(.)/g, function ($0, $1) {
                            return $1.toUpperCase();
                        });
                        datas.push({
                            name: camelCaseName,
                            value: attr.value
                        });
                    }
                });
                datas.forEach(function (data) {
                    if (data.name == 'id') {
                        var url = '{{ url('staff/catalog/coupon') }}/' + data.value;
                        $('#edit-modal form').prop('action', url);
                    } else if (data.name === 'customers') {
                        var customer_ids = JSON.parse(data.value)
                        $('#customers').val(customer_ids)
                    } else if (data.name === 'products') {
                        var product_ids = JSON.parse(data.value)
                        $('#products').val(product_ids)
                    } else {
                        $('#' + data.name).val(data.value);
                    }
                })
                $("#edit-modal select.select2-hidden-accessible").select2('destroy');
                $('#edit-modal .select2').select2()

                $('#edit-modal').modal('show');
            })
            $(document).on('click', '.btn-delete', function (e) {
                e.preventDefault();
                var id = $(this).data('id');
                var url = '{{ url('staff/catalog/coupon') }}/' + id
                $('#delete-modal form').prop('action', url);
                $('#delete-modal').modal();
            })
        })(jQuery)
    </script>
@endsection
