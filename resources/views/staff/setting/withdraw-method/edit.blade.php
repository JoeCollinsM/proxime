@extends('staff.layouts.app')

@section('page_title', 'Edit Withdraw Method')

@section('css_libs')
    <link href="{{ asset('staff/css/bootstrap4-toggle.min.css') }}"
          rel="stylesheet">
    <link href="{{ asset('staff/admin/plugins/select2/css/select2.min.css') }}"
          rel="stylesheet">
    <link href="{{ asset('staff/admin/plugins/select2/css/select2-bootstrap.css') }}"
          rel="stylesheet">
@endsection

@section('css')
    <style>
        [v-cloak] {
            display: none;
        }

        .toggle.btn {
            width: 100% !important;
        }
    </style>
@endsection

@section('content')

    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Withdraw settings</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Withdraw</li>
            <li class="breadcrumb-item">Withdraw Method</li>
            <li class="breadcrumb-item active">Edit</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->

    <div id="app">
        <form action="{{ route('staff.setting.withdraw-method.update', $withdrawMethod->id) }}" method="post">
            @csrf
            @method('PUT')
            
                <div class="row">
                    <div class="col-lg-8 col-md-8">
                        <div class="card">
                            <div
                                    class="card-header d-flex justify-content-between align-content-center">
                                <div class="pull-left">
                                    <h3>Method Details</h3>
                                </div>
                            </div>
                            <div class="card-body" v-cloak>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input id="name" type="text"
                                                   class="form-control @error('name') is-invalid @enderror" name="name"
                                                   value="{{ $withdrawMethod->name }}" required>
                                            @error('name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <input id="status" name="status" type="checkbox"
                                                   {{ $withdrawMethod->status?'checked':'' }} data-toggle="toggle"
                                                   data-on="Enabled" data-off="Disabled" data-onstyle="success"
                                                   data-offstyle="danger">
                                            @error('status')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description"
                                              class="form-control @error('description') is-inavlid @enderror">{{ $withdrawMethod->description }}</textarea>
                                    @error('description')
                                    <div class="inavlid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="min">Minimum amount (-1 for not limit)</label>
                                            <div class="input-group">
                                                <input id="min" type="number" step="0.01"
                                                       class="form-control @error('min') is-invalid @enderror"
                                                       name="min" value="{{ $withdrawMethod->min }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">{{ $currency->code }}</span>
                                                </div>
                                            </div>
                                            @error('min')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="max">Maximum amount (-1 for not limit)</label>
                                            <div class="input-group">
                                                <input id="max" type="number" step="0.01"
                                                       class="form-control @error('max') is-invalid @enderror"
                                                       name="max" value="{{ $withdrawMethod->max }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">{{ $currency->code }}</span>
                                                </div>
                                            </div>
                                            @error('max')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="percent_charge">Percent Charge</label>
                                            <div class="input-group">
                                                <input id="percent_charge" type="number" step="0.01"
                                                       class="form-control @error('percent_charge') is-invalid @enderror"
                                                       name="percent_charge"
                                                       value="{{ $withdrawMethod->percent_charge }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                            @error('percent_charge')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="fixed_charge">Fixed Charge</label>
                                            <div class="input-group">
                                                <input id="fixed_charge" type="number" step="0.01"
                                                       class="form-control @error('fixed_charge') is-invalid @enderror"
                                                       name="fixed_charge" value="{{ $withdrawMethod->fixed_charge }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">{{ $currency->code }}</span>
                                                </div>
                                            </div>
                                            @error('fixed_charge')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mt-5" v-for="(field, index) in fields" :key="index" v-cloak>
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>@lang('Field') - @{{ index+1 }} (@{{ field.title }})</h4>
                                <button class="btn btn-danger btn-sm"
                                        @click.prevent="removeField(index)"><i
                                            class="fa fa-times"></i> @lang('Remove')</button>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-between align-items-center">
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label :for="'title-' + index">@lang('Title') <span
                                                        class="text-danger">*</span></label>
                                            <input :id="'title-' + index" type="text" v-model="field.title"
                                                   :name="'fields['+index+'][title]'"
                                                   class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label :for="'input_type-' + index">@lang('Input Type') <span
                                                        class="text-danger">*</span></label>
                                            <select :id="'input_type-' + index" v-model="field.input_type"
                                                    :name="'fields['+index+'][input_type]'"
                                                    class="form-control" required>
                                                <option value="text">Text</option>
                                                <option value="textarea">Textarea</option>
                                                <option value="select">Select</option>
                                                <option value="multiple">Multiple Select</option>
                                                <option value="radio">Radio</option>
                                                <option value="checkbox">Checkbox</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md"
                                         v-if="field.input_type == 'text' || field.input_type == 'textarea'">
                                        <div class="form-group">
                                            <label :for="'placeholder-' + index">@lang('Placeholder')</label>
                                            <input :id="'placeholder-' + index" type="text" v-model="field.placeholder"
                                                   :name="'fields['+index+'][placeholder]'"
                                                   class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label>@lang('Is Required?') <span class="text-danger">*</span></label>
                                            <br>
                                            <div class="radio form-check-inline mr-4">
                                                <input type="radio" :id="'is-required-yes-' + index" value="1"
                                                       :name="'fields['+index+'][is_required]'"
                                                       v-model="field.is_required">
                                                <label :for="'is-required-yes-' + index"> @lang('YES') </label>
                                            </div>
                                            <div class="radio form-check-inline">
                                                <input type="radio" id="'is-required-no-' + index" value="0"
                                                       :name="'fields['+index+'][is_required]'"
                                                       v-model="field.is_required">
                                                <label :for="'is-required-no-' + index"> @lang('NO') </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="form-group">
                                            <button v-if="field.input_type != 'text' && field.input_type != 'textarea'"
                                                    class="btn btn-success mt-3" @click.prevent="addOption(index)"><i
                                                        class="fa fa-plus"></i> @lang('Add Option')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer"
                                 v-if="field.input_type != 'text' && field.input_type != 'textarea' && field.options.length">
                                <div class="row justify-content-between align-items-center"
                                     v-for="(option, index2) in field.options" :key="index2" v-cloak>
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label :for="'field-' + index + '-title-' + index2">@lang('Title')</label>
                                            <input :id="'field-' + index + '-title-' + index2" type="text"
                                                   v-model="field.options[index2].title"
                                                   :name="'fields['+index+'][options]['+index2+'][title]'"
                                                   class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="form-group">
                                            <button class="btn btn-danger mt-3"
                                                    @click.prevent="removeOption(index, index2)"><i
                                                        class="fa fa-times"></i> @lang('Remove')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group my-3 text-md-right text-center">
                            <button class="btn btn-lg btn-outline-primary"
                                    @click.prevent="addField">
                                <i class="fa fa-plus"></i> Add Field
                            </button>
                        </div>

                        <div class="form-group my-3">
                            <button class="btn btn-lg btn-outline-success btn-block" type="submit">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js_libs')
    <script src="{{ asset('staff/js/bootstrap4-toggle.min.js') }}"></script>
    <script src="{{ asset('staff/admin/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('staff/js/vue.js') }}"></script>
    <script src="{{ asset('staff/js/select2.vue.js') }}"></script>
@endsection

@section('js')
    <script>
        $.fn.select2.defaults.set("theme", "bootstrap");
        window.app = new Vue({
            el: '#app',
            data: {
                fields: @json($withdrawMethod->fields??[])
            },
            beforeMount() {
                this.fields = this.fields.map(field => {
                    if (!field.hasOwnProperty('options')) {
                        field.options = []
                    }
                    return field;
                })
            },
            methods: {
                addField() {
                    this.fields.push({
                        title: null,
                        input_type: 'text',
                        placeholder: null,
                        options: [],
                        is_required: 0
                    })
                },
                removeField(index) {
                    this.fields.splice(index, 1)
                },
                addOption(index) {
                    this.fields[index].options.push({
                        title: null
                    })
                },
                removeOption(index, index2) {
                    this.fields[index].options.splice(index2, 1)
                }
            }
        })
    </script>
@endsection