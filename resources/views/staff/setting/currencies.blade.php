@extends('staff.layouts.app')

@section('page_title', 'Currencies')

@section('content')
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Currency settings</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Settings</li>
            <li class="breadcrumb-item active">Currencies</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->
            
            <div class="card">
                <div class="card-header d-flex justify-content-between align-content-center">
                    <div class="pull-left">
                        All Currencies
                    </div>
                    <div>
                        <button class="btn btn-primary btn-sm"
                                onclick="event.preventDefault();document.getElementById('refresh-form').submit();"><i
                                class="fa fa-refresh"></i>
                        </button>
                        <button class="btn btn-success btn-sm" data-toggle="modal"
                                data-target="#new-modal"><i
                                class="fa fa-plus"></i> Add New Currency
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('staff.setting.currency.refresh') }}" method="post" id="refresh-form"
                          class="d-none">
                        @csrf
                        @method('PUT')
                    </form>
                    <table class="table table-striped table-bordered" id="data-table" style="width:100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Rate</th>
                            <th>Default</th>
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
                            <h4 class="modal-title">Add New Currency</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                        </div>
                        <form action="{{ route('staff.setting.currency.store') }}" method="post">
                            @csrf
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Currency Name</label>
                                            <input type="text" name="name" value="{{ old('name') }}"
                                                   class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Currency Code</label>
                                            <input type="text" name="code" value="{{ old('code') }}"
                                                   class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Currency Symbol</label>
                                            <input type="text" name="symbol" value="{{ old('symbol') }}"
                                                   class="form-control" placeholder="$">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Currency Rate</label>
                                            <input type="number" step="0.01" name="rate" value="{{ old('rate') }}"
                                                   class="form-control" required>
                                            <p class="font-weight-light text-info">For default currency, rate is 1</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input"
                                                       id="add_is_default" name="is_default" value="1">
                                                <label class="custom-control-label" for="add_is_default">Is
                                                    Default</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input"
                                                       id="add_status" name="status" value="1">
                                                <label class="custom-control-label" for="add_status">Status
                                                    (Enabled/Disabled)</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default btn-round pull-left" data-dismiss="modal">Close
                                </button>
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
                            <h4 class="modal-title">Edit Currency</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                        </div>
                        <form method="post">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Currency Name</label>
                                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                                   class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Currency Code</label>
                                            <input type="text" name="code" id="code" value="{{ old('code') }}"
                                                   class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Currency Symbol</label>
                                            <input type="text" name="symbol" id="symbol" value="{{ old('symbol') }}"
                                                   class="form-control" placeholder="$">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Currency Rate</label>
                                            <input type="text" name="rate" id="rate" value="{{ old('rate') }}"
                                                   class="form-control" required>
                                            <p class="font-weight-light text-info">For default currency, rate is 1</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input"
                                                       id="is_default" name="is_default" value="1">
                                                <label class="custom-control-label" for="is_default">Is
                                                    Default</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input"
                                                       id="status" name="status" value="1">
                                                <label class="custom-control-label" for="status">Status
                                                    (Enabled/Disabled)</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default  btn-round pull-left" data-dismiss="modal">Close
                                </button>
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
                            <h4 class="modal-title">Delete Currency</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                        </div>
                        <form method="post">
                            @csrf
                            @method('DELETE')
                            <div class="modal-body">
                                <h3 class="text-center">Are you sure to delete this currency?</h3>
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
                    ajax: '{{ route('staff.setting.currency.index') }}',
                    columns: [
                        {data: 'id', name: 'id'},
                        {data: 'name', name: 'name'},
                        {data: 'code', name: 'code'},
                        {data: 'rate', name: 'rate'},
                        {data: 'is_default', name: 'is_default'},
                        {data: 'status', name: 'status'},
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
                        var url = '{{ url('staff/setting/currency') }}/' + data.value;
                        $('#edit-modal form').prop('action', url);
                    } else if (data.name == 'is_default' || data.name == 'status') {
                        if (parseInt(data.value) == 1) {
                            $('#' + data.name).prop('checked', true)
                        } else {
                            $('#' + data.name).prop('checked', false)
                        }
                    } else {
                        $('#' + data.name).val(data.value);
                    }
                })
                $('#edit-modal').modal('show');
            })
            $(document).on('click', '.btn-delete', function (e) {
                e.preventDefault();
                var id = $(this).data('id');
                var url = '{{ url('staff/setting/currency') }}/' + id
                $('#delete-modal form').prop('action', url);
                $('#delete-modal').modal();
            })
        })(jQuery)
    </script>
@endsection
