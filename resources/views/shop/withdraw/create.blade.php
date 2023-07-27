@extends('shop.layouts.app')

@section('page_title', 'Add New Withdraw')

@section('css_libs')
    <link href="{{ asset('staff/admin/plugins/select2/css/select2.min.css') }}"
          rel="stylesheet">
    <link href="{{ asset('staff/admin/plugins/select2/css/select2-bootstrap.css') }}"
          rel="stylesheet">
@endsection

@section('css')
    <style>
        .toggle.btn {
            width: 100% !important;
        }

        [v-cloak] {
            display: none;
        }
    </style>
@endsection

@section('content')
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Withdraws</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Withdraws</li>
            <li class="breadcrumb-item active">Withdraws</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->

    <div id="app">
        <form action="{{ route('shop.withdraw.store') }}" method="post">
            @csrf
            
                <div class="row">
                    <div class="col-lg-6 col-md-8">
                        <div class="card">
                            <div class=" card-header d-flex justify-content-between align-content-center">
                                <div class="pull-left">
                                    <h3>Withdraw Details</h3>
                                </div>
                            </div>
                            <div class="card-body" v-cloak>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="withdraw_method_id">Withdraw Method</label>
                                            <select id="withdraw_method_id" name="withdraw_method_id"
                                                    v-model="withdraw_method_id"
                                                    class="custom-select @error('withdraw_method_id') is-invalid @enderror"
                                                    required>
                                                <option v-for="(met, metID) in methods" :key="metID" :value="met.id">@{{
                                                    met.name }}
                                                </option>
                                            </select>
                                            @error('withdraw_method_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="amount">Amount</label>
                                            <div class="input-group">
                                                <input type="number" v-model="amount" name="amount" min="0" class="form-control" step="any">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">{{ $currency->code }}</span>
                                                </div>
                                            </div>
                                            <span>Total Charge @{{ charge }} {{ $currency->code }}</span>
                                            @error('amount')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row" v-if="method && method.fields">
                                    <div v-for="(field, fieldIndex) in method.fields" :key="fieldIndex"
                                         class="col-md-6">
                                        <div class="form-group">
                                            <label :for="'field-' + fieldIndex" class="d-block">@{{ field.title
                                                }}</label>
                                            <input type="hidden" :name="'fields[' + fieldIndex + '][title]'"
                                                   v-model="field.title">
                                            <input class="form-control" v-if="field.input_type == 'text'" type="text"
                                                   :name="'fields[' + fieldIndex + '][value]'"
                                                   :required="field.is_required == '1'?true:false"
                                                   :placeholder="field.placeholder">
                                            <textarea class="form-control" v-if="field.input_type == 'textarea'"
                                                      :name="'fields[' + fieldIndex + '][value]'"
                                                      :required="field.is_required == '1'?true:false"
                                                      :placeholder="field.placeholder"></textarea>
                                            <select class="form-control" v-if="field.input_type == 'select'"
                                                    :name="'fields[' + fieldIndex + '][value]'"
                                                    :required="field.is_required == '1'?true:false">
                                                <option v-if="field.options"
                                                        v-for="(option, optionIndex) in field.options"
                                                        :key="optionIndex" :value="option.title">@{{ option.title }}
                                                </option>
                                            </select>
                                            <select class="form-control" v-if="field.input_type == 'multiple'"
                                                    :name="'fields[' + fieldIndex + '][value][]'"
                                                    :required="field.is_required == '1'?true:false" multiple>
                                                <option v-if="field.options"
                                                        v-for="(option, optionIndex) in field.options"
                                                        :key="optionIndex" :value="option.title">@{{ option.title }}
                                                </option>
                                            </select>
                                            <div v-else-if="field.input_type == 'radio' && field.options"
                                                 v-for="(option, optionIndex) in field.options" :key="optionIndex"
                                                 class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                       :id="'field-option-' + fieldIndex + '-' + optionIndex"
                                                       :name="'fields[' + fieldIndex + '][value]'"
                                                       :value="option.title">
                                                <label class="form-check-label"
                                                       :for="'field-option-' + fieldIndex + '-' + optionIndex">@{{
                                                    option.title }}</label>
                                            </div>
                                            <div v-else-if="field.input_type == 'checkbox' && field.options"
                                                 v-for="(option, optionIndex) in field.options" :key="optionIndex"
                                                 class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox"
                                                       :id="'field-option-' + fieldIndex + '-' + optionIndex"
                                                       :name="'fields[' + fieldIndex + '][value][]'"
                                                       :value="option.title">
                                                <label class="form-check-label"
                                                       :for="'field-option-' + fieldIndex + '-' + optionIndex">@{{
                                                    option.title }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary btn-block btn-lg"
                                            type="submit"
                                            onclick="return confirm('Are you sure to request for new withdraw? You can\'t revert this')">
                                        Request
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
           
        </form>
    </div>
@endsection

@section('js_libs')
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
                withdraw_method_id: @json(old('withdraw_method_id')??null),
                methods: @json($methods),
                amount: 0,
                fields: @json(old('fields')??[])
            },
            beforeMount() {
                if (!this.withdraw_method_id) {
                    if (this.methods.length) {
                        this.withdraw_method_id = this.methods[0].id
                    }
                }
            },
            components: {
                select2: window.select2Vue
            },
            computed: {
                method() {
                    var method = null
                    const filter = this.methods.filter(method => {
                        return method.id === this.withdraw_method_id
                    });
                    if (filter.length) {
                        method = filter[0]
                        if (method.min !== -1 && this.amount < method.min) {
                            this.amount = method.min
                        }
                        if (method.max !== -1 && this.amount > method.max) {
                            this.amount = method.max
                        }
                    }
                    return method
                },
                charge() {
                    if (!this.method) return 0;
                    var percentCharge = (this.amount * this.method.percent_charge) / 100
                    return (percentCharge + this.method.fixed_charge).toFixed({{ config('proxime.decimals') }})
                },
                isValid() {

                }
            },
            watch: {
                // withdraw_method_id(newVal, oldVal) {
                //     if (this[this.user_type].length) {
                //         this[this.user_type + '_id'] = this[this.user_type][0].id
                //     }
                // }
            }
        })
    </script>
@endsection
