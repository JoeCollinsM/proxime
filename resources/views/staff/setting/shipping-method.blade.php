@extends('staff.layouts.app')

@section('page_title', 'Shipping Methods')

@section('content')
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Shipping settings</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Settings</li>
            <li class="breadcrumb-item active">Shipping Methods</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->
            
            <div class="card">
                <div class="card-header d-flex justify-content-between align-content-center">
                    <div class="pull-left">
                        All Shipping Methods
                    </div>
                    <button class="btn btn-success btn-sm" data-toggle="modal"
                            data-target="#new-modal"><i
                            class="fa fa-plus"></i> Add New Method
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered" id="data-table" style="width:100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Charge</th>
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
                            <h4 class="modal-title">Add New Method</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                        </div>
                        <form action="{{ route('staff.setting.shipping-method.store') }}" method="post">
                            @csrf
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="control-label">Method Name</label>
                                    <input type="text" name="name" value="{{ old('name') }}"
                                           class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Method Description</label>
                                    <textarea class="form-control" name="description"
                                              required>{{ old('description') }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Charge</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" name="charge"
                                               value="{{ old('charge') }}"
                                               class="form-control" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{ $currency->code }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox mr-sm-2">
                                        <input type="checkbox" class="custom-control-input"
                                               id="add_status" name="status" value="1">
                                        <label class="custom-control-label" for="add_status">Status
                                            (Enabled/Disabled)</label>
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
                            <h4 class="modal-title">Edit Method</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                        </div>
                        <form method="post">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="control-label">Method Name</label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                                           class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Method Description</label>
                                    <textarea class="form-control" name="description" id="description"
                                              required>{{ old('description') }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Charge</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" name="charge" id="charge"
                                               value="{{ old('charge') }}"
                                               class="form-control" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{ $currency->code }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox mr-sm-2">
                                        <input type="checkbox" class="custom-control-input"
                                               id="status" name="status" value="1">
                                        <label class="custom-control-label" for="status">Status
                                            (Enabled/Disabled)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default btn-round pull-left" data-dismiss="modal">Close
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
                            <h4 class="modal-title">Delete Method</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                        </div>
                        <form method="post">
                            @csrf
                            @method('DELETE')
                            <div class="modal-body">
                                <h3 class="text-center">Are you sure to delete this shipping method?</h3>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close
                                </button>
                                <button type="submit" class="btn btn-danger">Delete</button>
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
                    ajax: '{{ route('staff.setting.shipping-method.index') }}',
                    columns: [
                        {data: 'id', name: 'id'},
                        {data: 'name', name: 'name'},
                        {data: 'charge', name: 'charge'},
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
                        var url = '{{ url('staff/setting/shipping-method') }}/' + data.value;
                        $('#edit-modal form').prop('action', url);
                    } else if (data.name == 'status') {
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
                var url = '{{ url('staff/setting/shipping-method') }}/' + id
                $('#delete-modal form').prop('action', url);
                $('#delete-modal').modal();
            })
        })(jQuery)
    </script>
@endsection
