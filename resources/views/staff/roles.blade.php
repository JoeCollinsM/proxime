@extends('staff.layouts.app')

@section('page_title', 'Role Management')

@section('content')
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Roles</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Roles</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->
    
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-content-center">
            <div class="pull-left">
                All Roles
            </div>
            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#new-modal"><i
                    class="fa fa-plus"></i> Add New Role
            </button>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Capabilities</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($roles as $role)
                    <tr>
                        <td>{{ $role->id }}</td>
                        <td>{{ $role->name }}</td>
                        <td>
                            @foreach($role->caps as $key)
                                {{ config('caps.' . $key) }},
                            @endforeach
                        </td>
                        <td>
                            <div class="btn-group"> 
                                <button class="btn btn-warning btn-edit btn-sm waves-effect waves-light" data-id="{{ $role->id }}"
                                        data-name="{{ $role->name }}" data-caps="{{ json_encode($role->caps) }}"><i
                                        class="fa fa-edit"></i></button>
                                <button class="btn btn-danger btn-delete waves-effect waves-light" data-id="{{ $role->id }}"><i
                                        class="fa fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="text-center">
                {{ $roles->links() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="new-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add New Role</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                </div>
                <form action="{{ route('staff.role.store') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label">Role Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                        </div>
                        <div class="form-group row">
                            @foreach(array_chunk(config('caps'), 3, true) as $caps)
                                <div class="col-md-4">
                                    @foreach($caps as $key => $value)
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" class="flat-red" name="caps[]"
                                                        value="{{ $key }}" {{ old('caps') && in_array($key, old('caps'))?'checked':'' }}> {{ $value }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
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
                    <h4 class="modal-title">Edit Role</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                </div>
                <form method="post">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label">Role Name</label>
                            <input type="text" name="name" id="name" class="form-control">
                        </div>
                        <div class="form-group row">
                            @foreach(array_chunk(config('caps'), 3, true) as $caps)
                                <div class="col-md-4">
                                    @foreach($caps as $key => $value)
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" class="flat-red cap-checkbox"
                                                        id="{{ $key }}"
                                                        name="caps[]" value="{{ $key }}"> {{ $value }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close
                        </button>
                        <button type="submit" class="btn btn-primary">Update</button>
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
                    <h4 class="modal-title">Delete Role</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                </div>
                <form method="post">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <h3 class="text-center">Are you sure to delete this role?</h3>
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
            $(document).on('click', '.btn-edit', function (e) {
                e.preventDefault()

                var datas = [];
                [].forEach.call(this.attributes, function (attr) {
                    if (/^data-/.test(attr.name)) {
                        var camelCaseName = attr.name.substr(5).replace(/-(.)/g, function ($0, $1) {
                            return $1.toUpperCase()
                        })
                        datas.push({
                            name: camelCaseName,
                            value: attr.value
                        })
                    }
                })
                datas.forEach(function (data) {
                    if (data.name == 'id') {
                        var url = '{{ url('staff/role') }}/' + data.value
                        $('#edit-modal form').prop('action', url)
                    } else if (data.name == 'caps') {
                        $('.cap-checkbox').prop('checked', false)
                        JSON.parse(data.value).forEach((cap) => {
                            $('#' + cap).prop('checked', true)
                        })
                    }
                    $('#' + data.name).val(data.value)
                })

                $('#edit-modal').modal('show')
            })
            $(document).on('click', '.btn-delete', function (e) {
                e.preventDefault()
                var id = $(this).data('id')
                var url = '{{ url('staff/role') }}/' + id
                $('#delete-modal form').prop('action', url)
                $('#delete-modal').modal()
            })
        })(jQuery)
    </script>
@endsection
