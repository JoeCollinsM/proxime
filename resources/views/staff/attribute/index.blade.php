@extends('staff.layouts.app')

@section('page_title', __('Attributes'))

@section('content')
      <!-- Breadcrumb-->
      <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Categories</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Attributes</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->
            
            <div class="row">
                <div class="col-md-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>@lang('New Attribute')</h4>
                    </div>
                    <form id="attribute-form" action="{{ route('staff.catalog.attribute.store') }}" method="post">
                        @csrf
                        @method('POST')
                        
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">@lang('Name') <i class="fa fa-question-circle"
                                                                    data-toggle="tooltip"
                                                                    title="@lang('Name for the attribute (shown on the front-end).')"></i></label>
                                <input id="name" value="{{ old('name') }}" name="name"
                                        class="form-control input-lg @error('name') is-invalid @enderror"
                                        type="text" required>
                                @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="slug">@lang('Slug') <i class="fa fa-question-circle"
                                                                    data-toggle="tooltip"
                                                                    title="@lang('Unique slug/reference for the attribute')"></i></label>
                                <input id="slug" value="{{ old('slug') }}" name="slug"
                                        class="form-control input-lg @error('slug') is-invalid @enderror"
                                        type="text" required>
                                @error('slug')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="type">@lang('Type') <i
                                            class="fa fa-question-circle" data-toggle="tooltip"
                                            title="@lang('Determines how this attribute\'s values are displayed.')"></i></label>
                                <select id="type" name="type"
                                        class="form-control input-lg @error('type') is-invalid @enderror" required>
                                    <option value="dropdown"
                                            @if(old('type') == 'dropdown') selected @endif>@lang('Dropdown')</option>
                                    <option value="button"
                                            @if(old('type') == 'button') selected @endif>@lang('Button')</option>
                                    <option value="color"
                                            @if(old('type') == 'color') selected @endif>@lang('Color')</option>
                                    <option value="image"
                                            @if(old('type') == 'image') selected @endif>@lang('Image')</option>
                                </select>
                                @error('type')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary btn-round " type="submit">@lang('Save')</button>
                                <button class="btn btn-round btn-outline-danger" type="reset">@lang('Reset')</button>
                            </div>
                        </div>
                    </form>
                </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            @lang('Attributes List')
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered dataTable" role="grid" aria-describedby="default-datatable_info" id="data-table"
                                    style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>@lang('Name')</th>
                                        <th>@lang('Slug')</th>
                                        <th>@lang('Type')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


    <div class="modal fade" id="delete-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Delete Attribute')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                </div>
                <form method="post">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p class="text-center">@lang('Are you sure to delete this attribute?')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">@lang('Close')
                        </button>
                        <button type="submit" class="btn btn-danger">@lang('Delete')</button>
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
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip()
            $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('staff.catalog.attribute.index') }}',
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'slug', name: 'slug'},
                    {data: 'type', name: 'type', searchable: false, orderable: false},
                    {data: 'action', name: 'action', searchable: false, orderable: false}
                ]
            });
        })
        $(document).on('input', '#name', function (e) {
            $('#slug').val($(this).val().toString().toLowerCase().replace(/\s+/g, '-').replace(/[^\w\-]+/g, '').replace(/\-\-+/g, '-').replace(/^-+/, '').replace(/-+$/, ''))
        })
        $(document).on('click', '.btn-edit', function (e) {
            e.preventDefault()
            var form = $('#attribute-form')
            form.find('.card-header h4').text('@lang('Edit Attribute')')
            form.find('[name="_method"]').val('PUT')
            form.find('[type="submit"]').text('@lang('Update')')
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
            datas.forEach(data => {
                if (data.name == 'id') {
                    var url = '{{ url('staff/catalog/attribute') }}/' + data.value;
                    form.attr('action', url);
                } else {
                    $('#' + data.name).val(data.value);
                }
            })

        })
        $(document).on('reset', '#attribute-form', function (e) {
            var form = $(this)
            form.find('.card-header h4').text('@lang('New Attribute')')
            form.find('[name="_method"]').val('POST')
            form.find('[type="submit"]').text('@lang('Save')')
            form.attr('action', '{{ route('staff.catalog.attribute.store') }}')
        })
        $(document).on('click', '.btn-delete', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            var url = '{{ url('staff/catalog/attribute') }}/' + id
            $('#delete-modal form').prop('action', url);
            $('#delete-modal').modal();
        })
    </script>
@endsection


