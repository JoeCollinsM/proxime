@extends('staff.layouts.app')

@section('page_title', 'Add New Transaction')

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
		    <h4 class="page-title">Transactions</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('staff.transaction.index') }}">Transactions</a></li>
            <li class="breadcrumb-item active">add New</li>
         </ol>
	   </div>
     </div>

    <div id="app">
    
        <form action="{{ route('staff.transaction.store') }}" method="post">
            @csrf
                
                <div class="row">
                    <div class="col-lg-6 col-md-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-content-center">
                                <div class="pull-left">
                                    <h3>Transaction Details</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user_type">User Type</label>
                                            <select id="user_type" name="user_type" v-model="user_type" class="custom-select @error('user_type') is-invalid @enderror">
                                                <option value="user">@lang('Customer/User')</option>
                                                <option value="delivery_man">@lang('Delivery Man')</option>
                                                <option value="shop">@lang('Shop/Vendor')</option>
                                            </select>
                                            @error('user_type')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group" v-if="user_type === 'user'">
                                            <label for="customer_id">Customers</label>
                                            <select2 id="customer_id" name="user_id" v-model="user_id" key="select2-1" class="form-control customers @error('user_id') is-invalid @enderror">
                                                @foreach($customers as $customer)
                                                    <option value="{{ $customer->id }}">{{ $customer->username }} - ({{ $customer->balance }} {{ $currency->code }})</option>
                                                @endforeach
                                            </select2>
                                            @error('user_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="form-group" v-else-if="user_type === 'shop'">
                                            <label for="shop_id">Shops/Vendors</label>
                                            <select2 id="shop_id" name="user_id" v-model="shop_id" key="select2-3" class="form-control shops @error('user_id') is-invalid @enderror">
                                                @foreach($shops as $shop)
                                                    <option value="{{ $shop->id }}">{{ $shop->name }} - ({{ $shop->balance }} {{ $currency->code }})</option>
                                                @endforeach
                                            </select2>
                                            @error('user_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="form-group" v-else>
                                            <label for="delivery_man_id">Delivery Man</label>
                                            <select2 id="delivery_man_id" name="user_id" v-model="delivery_man_id" key="select2-2" class="form-control delivery-men @error('user_id') is-invalid @enderror">
                                                @foreach($delivery_men as $delivery_m)
                                                    <option value="{{ $delivery_m->id }}">{{ $delivery_m->username }} - ({{ $delivery_m->balance }} {{ $currency->code }})</option>
                                                @endforeach
                                            </select2>
                                            @error('user_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type">Type</label>
                                            <select id="type" name="type" class="custom-select @error('type') is-invalid @enderror">
                                                <option value="+">+ in wallet</option>
                                                <option value="-">- from wallet</option>
                                            </select>
                                            @error('type')
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
                                                <input id="amount" type="number" step="any" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount') }}" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">{{ $currency->code }}</span>
                                                </div>
                                            </div>
                                            @error('amount')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary btn-block btn-lg" type="submit" onclick="return confirm('Are you sure to add this transaction? You can\'t revert this')">Save</button>
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
                user_type: 'user',
                user: @json($customers),
                delivery_man: @json($delivery_men),
                shop: @json($shops),
                user_id: @json(old('user_id')??null),
                delivery_man_id: @json(old('user_id')??null),
                shop_id: @json(old('user_id')??null),
            },
            beforeMount() {
                if (!this.user_id) {
                    if (this[this.user_type].length) {
                        this[this.user_type + '_id'] = this[this.user_type][0].id
                    }
                }
            },
            components: {
                select2: window.select2Vue
            },
            watch: {
                user_type(newVal, oldVal) {
                    if (this[this.user_type].length) {
                        this[this.user_type + '_id'] = this[this.user_type][0].id
                    }
                }
            }
        })
    </script>
@endsection
