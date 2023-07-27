@extends('staff.layouts.app')

@section('page_title', 'Staff Management')

@section('content')
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Staff Management</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Staff</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->
            
            <div class="card">
                <div
                    class="card-header  d-flex justify-content-between align-content-center">
                    <div class="pull-left">
                        All Staff
                    </div>
                    <button class="btn btn-success btn-sm " data-toggle="modal"
                            data-target="#new-modal"><i
                            class="fa fa-plus"></i> Add New Staff
                    </button>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($staffs as $staff)
                            <tr>
                                <td>{{ $staff->id }}</td>
                                <td>{{ $staff->name }}</td>
                                <td>{{ $staff->email }}</td>
                                <td>{{ $staff->phone }}</td>
                                <td>{{ optional($staff->role)->name }}</td>
                                <td>
                                    <div class="btn-group"> 
                                        <button class="btn btn-warning btn-edit btn-sm waves-effect waves-light" data-id="{{ $staff->id }}"
                                                data-name="{{ $staff->name }}" data-phone="{{ $staff->phone }}"
                                                data-email="{{ $staff->email }}" data-role_id="{{ $staff->role_id }}"><i
                                                class="fa fa-edit"></i></button>
                                        <button class="btn btn-danger btn-delete btn-sm waves-effect waves-light" data-id="{{ $staff->id }}"><i
                                                class="fa fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="text-center">
                        {{ $staffs->links() }}
                    </div>
                </div>
            </div>
        

    <div class="modal fade" id="new-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add New Staff</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                </div>
                <form action="{{ route('staff.staff.store') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Staff Name</label>
                                    <input type="text" name="name" class="form-control"
                                           value="{{ old('name') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Staff Email</label>
                                    <input type="email" name="email" class="form-control"
                                           value="{{ old('email') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Staff Phone (optional)</label>
                                    <input type="text" name="phone" class="form-control"
                                           value="{{ old('phone') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Role</label>
                                    <select name="role_id" class="form-control" required>
                                        @foreach($roles as $role)
                                            <option
                                                value="{{ $role->id }}" {{ $role->id == old('role_id')?'selected':'' }}>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Confirm Password</label>
                                    <input type="password" name="password_confirmation" class="form-control"
                                           required>
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
                    <h4 class="modal-title">Edit Staff</h4>
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
                                    <label class="control-label">Staff Name</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                           value="{{ old('name') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Staff Email</label>
                                    <input type="email" name="email" id="email" class="form-control"
                                           value="{{ old('email') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Staff Phone (optional)</label>
                                    <input type="text" name="phone" id="phone" class="form-control"
                                           value="{{ old('phone') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Role</label>
                                    <select name="role_id" id="role_id" class="form-control" required>
                                        @foreach($roles as $role)
                                            <option
                                                value="{{ $role->id }}" {{ $role->id == old('role_id')?'selected':'' }}>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Password</label>
                                    <input type="password" name="password" class="form-control"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Confirm Password</label>
                                    <input type="password" name="password_confirmation" class="form-control"
                                           autocomplete="off">
                                </div>
                            </div>
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
                    <h4 class="modal-title">Delete Staff</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                </div>
                <form method="post">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <h3 class="text-center">Are you sure to delete this staff?</h3>
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
                            var url = '{{ url('staff/staff') }}/' + data.value
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
                    var url = '{{ url('staff/staff') }}/' + id
                    $('#delete-modal form').prop('action', url)
                    $('#delete-modal').modal()
                })
            })
        })(jQuery)
    </script>
@endsection
