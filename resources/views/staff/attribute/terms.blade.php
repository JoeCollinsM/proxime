@extends('staff.layouts.app')

@section('page_title', $attribute->name)

@section('css')
    <style>
        .cursor-pointer {
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Categories</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('staff.catalog.attribute.index') }}">Attributes</a></li>
            <li class="breadcrumb-item"><li>{{ $attribute->name }}</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->
        
            <div class="row">
                <div class="col-md-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>@lang('New Term')</h4>
                    </div>
                    <form id="attribute-form" action="{{ route('staff.catalog.attribute.term.store') }}" method="post">
                        @csrf
                        @method('POST')
                        <input type="hidden" name="attribute_id" value="{{ $attribute->id }}">
                        
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="name">@lang('Name') <i class="fa fa-question-circle"
                                                                       data-toggle="tooltip"
                                                                       title="@lang('The name is how it appears on your site.')"></i></label>
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
                                                                       title="@lang('The “slug” is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.')"></i></label>
                                    <input id="slug" value="{{ old('slug') }}" name="slug"
                                           class="form-control input-lg @error('slug') is-invalid @enderror"
                                           type="text" required>
                                    @error('slug')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                @if($attribute->type == 'color')
                                    <div class="form-group">
                                        <label for="data" class="d-block">@lang('Color')</label>
                                        <input id="data" value="{{ old('data') }}" name="data"
                                               class="color-picker @error('data') is-invalid @enderror mr-2"
                                               type="color" required>
                                        @error('data')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                @elseif($attribute->type == 'image')
                                    <div class="form-group">
                                        <img class="image-preview cursor-pointer"
                                             style="width: 100px;height: 100px"
                                             id="data-preview"
                                             src="{{ old('data')??'https://via.placeholder.com/100?text=Select' }}"
                                             alt="data">
                                        <input
                                                value="{{ old('data')??'https://via.placeholder.com/100?text=Select' }}"
                                                class="form-control input-lg @error('data') is-invalid @enderror"
                                                type="hidden" name="data" id="data">
                                        @error('data')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                @endif
                                <div class="form-group">
                                    <button class="btn btn-success" type="submit">@lang('Save')</button>
                                    <button class="btn btn-outline-danger" type="reset">@lang('Reset')</button>
                                </div>
                            </div>
                    </form>
                </div>
                </div>
                <div class="col-md-8">
                    <div class="card border">
                        <div class="card-header">
                            <h4>@lang('Term List')</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered dataTable"y id="data-table"
                                   style="width:100%">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Slug')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


    <div class="modal fade" id="delete-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('Delete Term')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span></button>
                </div>
                <form method="post">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p class="text-center">@lang('Are you sure to delete this term?')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-round pull-left" data-dismiss="modal">@lang('Close')
                        </button>
                        <button type="submit" class="btn btn-danger btn-round">@lang('Delete')</button>
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
                ajax: '{{ route('staff.catalog.attribute.term.index', ['attribute_id' => $attribute->id]) }}',
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'slug', name: 'slug'},
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
            form.find('.card-header h4').text('@lang('Edit Term')')
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
                    var url = '{{ url('staff/catalog/attribute/term') }}/' + data.value;
                    form.attr('action', url);
                } else {
                    @if($attribute->type == 'image')
                    $('#data-preview').attr('src', data.value)
                    @endif
                    $('#' + data.name).val(data.value).trigger('change');
                }
            })

        })
        $(document).on('reset', '#attribute-form', function (e) {
            var form = $(this)
            form.find('.card-header h4').text('@lang('New Term')')
            form.find('[name="_method"]').val('POST')
            form.find('[type="submit"]').text('@lang('Save')')
            form.attr('action', '{{ route('staff.catalog.attribute.term.store') }}')
        })
        $(document).on('click', '.btn-delete', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            var url = '{{ url('staff/catalog/attribute/term') }}/' + id
            $('#delete-modal form').prop('action', url);
            $('#delete-modal').modal();
        })
        $(document).on('click', '#data-preview', function (e) {
            e.preventDefault()
            openMediaManager(items => {
                var image = items[0]
                if (image.hasOwnProperty('thumb_url')) {
                    $(this).attr('src', image.thumb_url)
                    $('#data').val(image.thumb_url)
                }
            }, 'image', 'Select Image')
        })
    </script>
@endsection


