@extends('staff.layouts.app')

@section('page_title', 'Payment Methods')

@section('content')
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Payment settings</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Settings</li>
            <li class="breadcrumb-item active">Payment Methods</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->
            
            <div class="card">
                <div class="card-header d-flex justify-content-between align-content-center">
                    <div class="pull-left">
                        All Payment Methods
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered" id="data-table"
                           style="width:100%">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Minimum</th>
                            <th>Maximum</th>
                            <th>Charge</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <div class="modal fade" id="edit-modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Edit Payment Method</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                        </div>
                        <form method="post">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="control-label">Name</label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                                           class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Description</label>
                                    <textarea name="description" id="description" class="form-control"
                                              required>{{ old('description') }}</textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Minimum</label>
                                            <input type="number" min="-1" step="0.01" name="min" id="min"
                                                   value="{{ old('min')??-1 }}"
                                                   class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Maximum</label>
                                            <input type="number" min="-1" step="0.01" name="max" id="max"
                                                   value="{{ old('max')??-1 }}"
                                                   class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Percent Charge</label>
                                            <div class="input-group">
                                                <input type="number" min="0" step="0.01" name="percent_charge"
                                                       id="percent_charge"
                                                       value="{{ old('percent_charge')??0 }}"
                                                       class="form-control" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Fixed Charge</label>
                                            <div class="input-group">
                                                <input type="number" min="0" step="0.01" name="fixed_charge"
                                                       id="fixed_charge"
                                                       value="{{ old('fixed_charge')??0 }}"
                                                       class="form-control" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">{{ $currency->code }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group form-group-cred1">
                                    <label class="control-label" id="control-label-cred1"></label>
                                    <input type="text" name="cred1" id="cred1" value="{{ old('cred1') }}"
                                           class="form-control">
                                </div>
                                <div class="form-group form-group-cred2">
                                    <label class="control-label" id="control-label-cred2"></label>
                                    <input type="text" name="cred2" id="cred2" value="{{ old('cred2') }}"
                                           class="form-control">
                                </div>
                                <div class="custom-control custom-checkbox mr-sm-2">
                                    <input type="checkbox" class="custom-control-input"
                                           id="status" name="status" value="1">
                                    <label class="custom-control-label" for="status">Status
                                        (Enabled/Disabled)</label>
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

      
@endsection

@section('js')
    <script>
        (function ($) {
            $(document).ready(function () {
                $('#data-table').DataTable({
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('staff.setting.payment-method.index') }}',
                    columns: [
                        {data: 'id', name: 'id'},
                        {data: 'name', name: 'name'},
                        {data: 'min', name: 'min', orderable: false, searchable: false},
                        {data: 'max', name: 'max', orderable: false, searchable: false},
                        {data: 'charge', name: 'charge', orderable: false, searchable: false},
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
                        var url = '{{ url('staff/setting/payment-method') }}/' + data.value;
                        $('.form-group-cred1').show();
                        $('.form-group-cred2').show();
                        switch (parseInt(data.value)) {
                            case 1:
                                // It is paypal
                                $('.form-group-cred2').hide();
                                $('#control-label-cred1').text('Paypal Email');
                                break;
                            case 2:
                                $('#control-label-cred1').text('Member ID');
                                $('#control-label-cred2').text('Passphrase');
                                break;
                            case 3:
                                $('#control-label-cred1').text('Publishable Key');
                                $('#control-label-cred2').text('Secret Key');
                                break;
                            case 4:
                                $('#control-label-cred1').text('Email');
                                $('#control-label-cred2').text('Secret Key');
                                break;
                            case 5:
                                $('.form-group-cred1').hide();
                                $('.form-group-cred2').hide();
                                break;
                            case 6:
                                $('#control-label-cred1').text('Key ID');
                                $('#control-label-cred2').text('Key Secret');
                                break;
                        }
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
