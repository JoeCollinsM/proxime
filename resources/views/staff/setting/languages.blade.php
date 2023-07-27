@extends('staff.layouts.app')

@section('page_title', 'Languages')

@section('content')
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Language settings</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Settings</li>
            <li class="breadcrumb-item active">Languages</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->
            
            <div class="card">
                <div class="card-header d-flex justify-content-between align-content-center">
                    <div class="pull-left">
                        All Languages
                    </div>
                    <button class="btn btn-success btn-sm" data-toggle="modal"
                            data-target="#new-modal"><i
                            class="fa fa-plus"></i> Add New Language
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered" id="data-table" style="width:100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Code</th>
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
                            <h4 class="modal-title">Add New Language</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                        </div>
                        <form action="{{ route('staff.setting.language.store') }}" method="post">
                            @csrf
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="control-label">Language Name</label>
                                    <input type="text" name="name" value="{{ old('name') }}"
                                           class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Language Code</label>
                                    <input type="text" name="code" value="{{ old('code') }}"
                                           class="form-control" placeholder="en" required>
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
                            <h4 class="modal-title">Edit Language</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                        </div>
                        <form method="post">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="control-label">Language Name</label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                                           class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Language Code</label>
                                    <input type="text" name="code" id="code" value="{{ old('code') }}"
                                           class="form-control" required>
                                </div>
                                <div class="custom-control custom-checkbox mr-sm-2">
                                    <input type="checkbox" class="custom-control-input"
                                           id="status" name="status" value="1">
                                    <label class="custom-control-label" for="status">Status
                                        (Enabled/Disabled)</label>
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
                            <h4 class="modal-title">Delete Language</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                        </div>
                        <form method="post">
                            @csrf
                            @method('DELETE')
                            <div class="modal-body">
                                <h3 class="text-center">Are you sure to delete this language?</h3>
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
                    ajax: '{{ route('staff.setting.language.index') }}',
                    columns: [
                        {data: 'id', name: 'id'},
                        {data: 'name', name: 'name'},
                        {data: 'code', name: 'code'},
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
                        var url = '{{ url('staff/setting/language') }}/' + data.value;
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
                var url = '{{ url('staff/setting/language') }}/' + id
                $('#delete-modal form').prop('action', url);
                $('#delete-modal').modal();
            })
        })(jQuery)
    </script>
@endsection
