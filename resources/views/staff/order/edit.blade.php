@extends('staff.layouts.app')

@section('page_title', 'Edit Order #' . $order->id)

@section('css_libs')
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/select2/css/select2-bootstrap.css') }}">
@endsection

@section('css')
    <style>
        [v-cloak] {
            display: none;
        }

        .vw-100 {
            width: 100vw !important;
        }

        .ovx {
            overflow-x: scroll;
        }

        .cursor-pointer {
            cursor: pointer;
            float: right;
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
            <li class="breadcrumb-item"><a href="{{ route('staff.catalog.order.index') }}">All Orders</a></li>
            <li class="breadcrumb-item active">Edit Order</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->

    <div id="app">

        
        <form action="{{ route('staff.catalog.order.update', $order->id) }}" method="post">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-content-center">
                            <div class="pull-left">
                                <h3>Order Details</h3>
                            </div>
                            <div class="pull-right">
                                <button type="submit"
                                        class="btn btn-lg btn-block btn-primary btn-round">
                                    Update Order
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <h5 class="mb-3">General</h5>
                                    <div class="form-group">
                                        <label class="text-black-50">Status</label>
                                        <select2 name="status" v-model="status" class="form-control select2">
                                            <option value="0">Pending</option>
                                            <option value="1">Processing</option>
                                            <option value="2">On The Way</option>
                                            <option value="3">Completed</option>
                                            <option value="4">Hold</option>
                                            <option value="5">Canceled</option>
                                        </select2>
                                    </div>
                                    <div class="form-group">
                                        <label class="text-black-50 d-block">Customer <a
                                                    href="{{ route('staff.catalog.user.create') }}"
                                                    class="float-right">Add
                                                New</a></label>
                                        <select2 name="customer" class="form-control" v-model="customer">
                                            @foreach($customers as $customer)
                                                <option
                                                        value="{{ $customer->id }}" {{ old('customer') == $customer->id?'selected':'' }}>{{ $customer->name }}
                                                    ({{ $customer->email }})
                                                </option>
                                            @endforeach
                                        </select2>
                                    </div>
                                    <div class="form-group">
                                        <label class="text-black-50 d-block">Shop <a
                                                    href="{{ route('staff.shop.create') }}" class="float-right">Add
                                                New</a></label>
                                        <input v-if="items.length" type="hidden" name="shop" v-model="shop_id">
                                        <select2 name="shop" v-model="shop_id" class="form-control" :disabled="items.length?true:false">
                                            <option v-for="(shop, shopIndex) in shops" :key="shopIndex"
                                                    :value="shop.id">@{{ shop.name }}
                                            </option>
                                        </select2>
                                    </div>
                                </div>
                                <div class="col-md-4 billing">
                                    <h5 class="mb-3">
                                        Billing
                                        <i class="fa fa-exchange float-right cursor-pointer text-info"
                                            id="billing-to-shipping-btn" data-toggle="tooltip"
                                            title="Copy To Shipping"></i>
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-black-50">Name</label>
                                                <input type="text" class="form-control" name="billing[name]"
                                                        v-model="billing.name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-black-50">Email</label>
                                                <input type="email" class="form-control" name="billing[email]"
                                                        v-model="billing.email" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-black-50">Phone</label>
                                                <input type="text" class="form-control" name="billing[phone]"
                                                        v-model="billing.phone" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-black-50">City</label>
                                                <input type="text" class="form-control" name="billing[city]"
                                                        v-model="billing.city" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-black-50">Address Line 1</label>
                                                <input type="text" class="form-control"
                                                        name="billing[street_address_1]"
                                                        v-model="billing.street_address_1" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-black-50">Address Line 2</label>
                                                <input type="text" class="form-control"
                                                        name="billing[street_address_2]"
                                                        v-model="billing.street_address_2">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-black-50">State (Optional)</label>
                                                <input type="text" class="form-control" name="billing[state]"
                                                        v-model="billing.state">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group billing_country">
                                                <label class="text-black-50">Country</label>
                                                <select2 class="form-control" v-model="billing.country"
                                                            name="billing[country]"
                                                            required>
                                                    @foreach(config('countries') as $code => $name)
                                                        <option value="{{ $code }}"
                                                                @if(is_array(old('billing')) && isset(old('billing')['country']) && old('billing')['country'] == $code) selected @endif>{{ $name }}</option>
                                                    @endforeach
                                                </select2>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-black-50">Latitude</label>
                                                <input type="number" step="any" class="form-control"
                                                        name="billing[latitude]"
                                                        v-model="billing.latitude" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-black-50">Longitude</label>
                                                <input type="number" step="any" class="form-control"
                                                        name="billing[longitude]"
                                                        v-model="billing.longitude" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 shipping">
                                    <h5 class="mb-3">Shipping</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-black-50">Name</label>
                                                <input type="text" class="form-control" name="shipping[name]"
                                                        v-model="shipping.name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-black-50">Email</label>
                                                <input type="email" class="form-control" name="shipping[email]"
                                                        v-model="shipping.email" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-black-50">Phone</label>
                                                <input type="text" class="form-control" name="shipping[phone]"
                                                        v-model="shipping.phone" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-black-50">City</label>
                                                <input type="text" class="form-control" name="shipping[city]"
                                                        v-model="shipping.city" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-black-50">Address Line 1</label>
                                                <input type="text" class="form-control"
                                                        name="shipping[street_address_1]"
                                                        v-model="shipping.street_address_1" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-black-50">Address Line 2</label>
                                                <input type="text" class="form-control"
                                                        name="shipping[street_address_2]"
                                                        v-model="shipping.street_address_2">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-black-50">State (Optional)</label>
                                                <input type="text" class="form-control" name="shipping[state]"
                                                        v-model="shipping.state">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group billing_country">
                                                <label class="text-black-50">Country</label>
                                                <select2 class="form-control" id="shipping-country"
                                                            name="shipping[country]" v-model="shipping.country"
                                                            required>
                                                    @foreach(config('countries') as $code => $name)
                                                        <option value="{{ $code }}"
                                                                @if(is_array(old('shipping')) && isset(old('shipping')['country']) && old('shipping')['country'] == $code) selected @endif>{{ $name }}</option>
                                                    @endforeach
                                                </select2>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-black-50">Latitude</label>
                                                <input type="number" step="any" class="form-control"
                                                        name="shipping[latitude]"
                                                        v-model="shipping.latitude" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-black-50">Longitude</label>
                                                <input type="number" step="any" class="form-control"
                                                        name="shipping[longitude]"
                                                        v-model="shipping.longitude" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card  mt-5">
                        <div class="card-body table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <td>Item</td>
                                    <td>Cost</td>
                                    <td>Qty</td>
                                    <td>Total</td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(item, index) in items" :key="index">
                                    <td>
                                        <div class="d-flex">
                                            <div
                                                    class="image mr-2 d-flex align-items-center justify-content-between">
                                                <i class="fa fa-times text-danger cursor-pointer mr-2"
                                                    @click.prevent="dropItem(index)"></i>
                                                <img class="img-thumbnail" style="width: 50px;height: 50px;"
                                                        :src="(item.variant || item.product).image"
                                                        :alt="item.product_title">
                                            </div>
                                            <div class="meta">
                                                <p>
                                                    <a :href="'{{ url("staff/catalog/product") }}/' + item.product_id + '/edit'">@{{
                                                        item.product_title }}</a>
                                                </p>

                                                <input type="hidden" :name="'items[' + index + '][id]'"
                                                        v-model="item.id">
                                                <input type="hidden" :name="'items[' + index + '][product_id]'"
                                                        v-model="item.product_id">
                                                <input type="hidden" :name="'items[' + index + '][variation_id]'"
                                                        v-model="item.variation_id">
                                                <input type="hidden" :name="'items[' + index + '][product_title]'"
                                                        v-model="item.product_title">
                                                <input type="hidden" :name="'items[' + index + '][quantity]'"
                                                        v-model="item.quantity">
                                                <input type="hidden" :name="'items[' + index + '][price]'"
                                                        v-model="item.price">
                                                <input type="hidden" :name="'items[' + index + '][tax]'"
                                                        v-model="item.tax">

                                                <p class="text-black-50" v-if="item.attrs"
                                                    v-for="(value, key) in item.attrs" :key="key">
                                                    @{{ key }}: @{{ value }}<br>
                                                    <input type="hidden"
                                                            :name="'items[' + index + '][attrs][' + key + ']'"
                                                            :value="value">
                                                </p>

                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $currency->symbol }}@{{ item.price+item.tax }}</td>
                                    <td>x@{{ item.quantity }}</td>
                                    <td>{{ $currency->symbol }}@{{ (item.price+item.tax)*item.quantity }}</td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="3" class="text-right">Items Subtotal</td>
                                    <td>{{ $currency->symbol }}@{{ subtotal }}</td>
                                </tr>
                                <tr v-if="shipping_method">
                                    <td colspan="3" class="text-right">Shipping Method</td>
                                    <td>@{{ shipping_method.name }}</td>
                                    <input type="hidden" name="shipping_method_id" v-model="shipping_method.id">
                                    <input type="hidden" name="shipping_method_name" v-model="shipping_method.name">
                                </tr>
                                <tr v-if="shipping_method">
                                    <td colspan="3" class="text-right">Shipping Charge</td>
                                    <td>{{ $currency->symbol }}@{{ shipping_method.charge }}</td>
                                    <input type="hidden" name="shipping_charge" v-model="shipping_method.charge">
                                </tr>
                                <tr v-if="coupon">
                                    <td colspan="3" class="text-right">Coupon Code</td>
                                    <td>@{{ coupon.code }}</td>
                                    <input type="hidden" name="coupon_id" v-model="coupon.id">
                                    <input type="hidden" name="coupon_code" v-model="coupon.code">
                                </tr>
                                <tr v-if="coupon">
                                    <td colspan="3" class="text-right">Coupon Discount</td>
                                    <td>{{ $currency->symbol }}@{{ coupon.discount }}</td>
                                    <input type="hidden" name="discount" v-model="coupon.discount">
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right">Total</td>
                                    <td>{{ $currency->symbol }}@{{ total }}</td>
                                </tr>
                                </tfoot>
                            </table>
                            <div class="btn-group">
                                <button class="btn btn-outline-info"
                                        @click.prevent="openModal('#add-product-modal')">Add Product
                                </button>
                                <button class="btn btn-outline-info"
                                        @click.prevent="openModal('#add-shipping-modal')">Add Shipping
                                </button>
                                <button class="btn btn-outline-info"
                                        @click.prevent="openModal('#add-coupon-modal')">Add Coupon
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="modal fade" id="add-product-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add New Product</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span></button>
                    </div>
                    <form action="#" @submit.prevent="addProduct">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="control-label">Select Product</label>
                                <select2 name="product_id" v-model="product_id" key="product-selector"
                                         class="form-control">
                                    <option value="">No Product</option>
                                    <option v-for="option in filteredProducts" :key="option.id" :value="option.id">@{{
                                        option.text }}
                                    </option>
                                </select2>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Select Variation</label>
                                <select2 name="variant_id" class="form-control">
                                    <option v-for="(v, i) in filteredVariations" :key="i" :value="v.id">@{{ v.title }}
                                    </option>
                                </select2>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Quantity</label>
                                <input type="number" min="1" name="quantity" value="1" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Add Product</button>
                        </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <div class="modal fade" id="add-shipping-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Shipping Method</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span></button>
                    </div>
                    <form action="#" @submit.prevent="addShipping">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="control-label">Select Method</label>
                                <select2 name="shipping_method_id" v-model="shipping_method_id" class="form-control">
                                    @foreach($shipping_methods as $method)
                                        <option value="{{ $method->id }}">{{ $method->name }}</option>
                                    @endforeach
                                </select2>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Add Shipping</button>
                        </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <div class="modal fade" id="add-coupon-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Coupon</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span></button>
                    </div>
                    <form action="#" @submit.prevent="addCoupon">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="control-label">Select Coupon</label>
                                <select2 name="coupon_id" class="form-control" v-model="coupon_id">
                                    <option value="">No Coupon</option>
                                    <option v-for="(coupon, i) in filteredCoupons" :key="i" :value="coupon.id">@{{
                                        coupon.code }}
                                    </option>
                                </select2>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Add Coupon</button>
                        </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
@endsection

@section('js_libs')
    <script src="{{ asset('staff/js/vue.js') }}"></script>
    <script src="{{ asset('staff/admin/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $.fn.select2.defaults.set("theme", "bootstrap");
    </script>
    <script src="{{ asset('staff/js/select2.vue.js') }}"></script>
@endsection

@section('js')
    <script>
        (function ($) {
            $(document).ready(function () {
                $('[data-toggle="tooltip"]').tooltip()
            })
            $(document).on('click', '#billing-to-shipping-btn', function (e) {
                e.preventDefault()
                window.app.shipping = Object.assign({}, window.app.billing)
            })
        })(jQuery)
        window.app = new Vue({
            el: '#app',
            components: {
                select2: window.select2Vue
            },
            data: {
                shipping_methods: @json($shipping_methods),
                customers: @json($customers),
                shops: @json($shops),
                products: @json($products),
                coupons: @json($coupons),
                variations: @json($variations),
                status: @json($order->status??0),
                customer: @json($order->customer_id??null),
                product_id: @json(old('product_id')??null),
                shop_id: @json(old('shop_id')??null),
                shipping_method_id: @json(old('shipping_method_id')??null),
                billing: @json($billing??null),
                shipping: @json($shipping??null),
                items: @json($order->items??[]),
                shipping_method: @json($order->shipping_method??null),
                coupon_id: @json($order->coupon_id),
                coupon: @json($order->coupon??null),
            },
            beforeMount() {
                if (!this.customer) {
                    if (this.customers.length) {
                        this.customer = this.customers[0].id
                    }
                }
                if (!this.shop_id) {
                    if (this.shops.length) {
                        this.shop_id = this.shops[0].id
                    }
                }
                if (!this.product_id) {
                    if (this.products.length) {
                        this.product_id = this.products[0].id
                    }
                }
                if (!this.billing) {
                    this.billing = {
                        name: '',
                        email: '',
                        street_address_1: '',
                        street_address_2: '',
                        city: '',
                        state: '',
                        country: 'BD',
                        latitude: '',
                        longitude: '',
                    }
                }
                if (!this.shipping) {
                    this.shipping = {
                        name: '',
                        email: '',
                        street_address_1: '',
                        street_address_2: '',
                        city: '',
                        state: '',
                        country: 'BD',
                        latitude: '',
                        longitude: '',
                    }
                }
                if (this.items.length) {
                    this.items = this.items.map(item => {
                        var product = this.products.filter(it => {
                            return parseInt(it.id) === parseInt(item.product_id)
                        })
                        var variant = this.variations.filter(it => {
                            return parseInt(it.id) === parseInt(item.variation_id)
                        })
                        if (product.length) {
                            if (variant.length) {
                                variant = variant[0]
                                var attrs = variant.attrs
                            } else {
                                variant = product[0]
                                var attrs = {}
                            }
                            product = product[0]
                            item.product = product
                            item.variant = variant
                        }
                        return item
                    })
                }
                if (this.coupon) {
                    this.coupon.discount = @json($order->discount);
                }
            },
            methods: {
                openModal(id) {
                    $(id).modal('show')
                },
                addCoupon() {
                    var coupon = this.coupons.filter(it => {
                        return parseInt(it.id) === parseInt(this.coupon_id)
                    })
                    if (coupon.length) {
                        coupon = coupon[0]
                        if (parseInt(coupon.discount_type) === 2) {
                            coupon.discount = parseFloat(coupon.amount)
                        } else {
                            var dis = 0;
                            var on = this.subtotal;
                            if (coupon.products.length) {
                                var couponProductIds = coupon.products.map(product => {
                                    return product.id;
                                })
                                on = 0;
                                this.items.forEach(item => {
                                    if (couponProductIds.indexOf(item.product_id) !== -1) {
                                        on += item.price * item.quantity;
                                    }
                                })
                            }
                            dis = (on * parseFloat(coupon.amount)) / 100;
                            coupon.discount = dis
                        }
                        this.coupon = coupon
                    } else {
                        this.coupon_id = ''
                        this.coupon = null
                    }
                    $('#add-coupon-modal').modal('hide')
                },
                addShipping() {
                    var method = this.shipping_methods.filter(it => {
                        return parseInt(it.id) === parseInt(this.shipping_method_id)
                    })
                    if (method.length) {
                        this.shipping_method = method[0]
                    } else {
                        this.shipping_method = null
                    }
                    $('#add-shipping-modal').modal('hide')
                },
                dropItem(index) {
                    this.items.splice(index, 1)
                },
                addProduct() {
                    var product_id = $('#add-product-modal select[name="product_id"]').val();
                    var variant_id = $('#add-product-modal select[name="variant_id"]').val();
                    var quantity = parseInt($('#add-product-modal [name="quantity"]').val());
                    var product = this.products.filter(it => {
                        return parseInt(it.id) === parseInt(product_id)
                    })
                    var variant = this.variations.filter(it => {
                        return parseInt(it.id) === parseInt(variant_id)
                    })
                    if (!product.length) return;
                    if (variant.length) {
                        variant = variant[0]
                        var attrs = variant.attrs
                    } else {
                        variant = product[0]
                        var attrs = {}
                    }
                    product = product[0]
                    if (this.items.length) {
                        const firstProduct = this.items[0]
                        if (firstProduct.hasOwnProperty('product')) {
                            if (firstProduct.product.shop_id !== product.shop_id) {
                                $('#add-product-modal').modal('hide')
                                alert("You can't add products from different different shop");
                                return;
                            }
                        }
                    }
                    this.items.push({
                        'product_id': product_id,
                        'variation_id': variant_id,
                        'product_title': product.title,
                        'quantity': quantity,
                        'price': variant.sale_price,
                        'tax': variant.tax,
                        'attrs': attrs,
                        'product': product,
                        'variant': variant
                    })
                    $('#add-product-modal').modal('hide')
                }
            },
            computed: {
                filteredProducts() {
                    return this.products.filter(product => {
                        return parseInt(product.shop_id) === parseInt(this.shop_id);
                    }).map(product => {
                        return {id: product.id, text: product.title}
                    })
                },
                filteredVariations() {
                    return this.variations.filter(variant => {
                        return parseInt(variant.parent_id) === parseInt(this.product_id);
                    })
                },
                total() {
                    var t = this.subtotal
                    if (this.shipping_method) {
                        t += this.shipping_method.charge
                    }
                    if (this.coupon) {
                        t -= this.coupon.discount
                    }
                    return t
                },
                subtotal() {
                    var sum = 0
                    this.items.forEach(item => {
                        sum += (item.price + item.tax) * item.quantity
                    })
                    return sum
                },
                filteredCoupons() {
                    return this.coupons.filter(coupon => {
                        if (parseInt(coupon.min) !== -1) {
                            if (parseFloat(coupon.min) > window.app.subtotal) return false;
                        }
                        if (coupon.users.length) {
                            var currentUserId = $('[name="customer"]').val()
                            var couponUsers = coupon.users.map(user => {
                                return user.id;
                            })
                            if (couponUsers.indexOf(currentUserId) === -1) return false;
                        }
                        if (coupon.products.length) {
                            var itemsProductIds = this.items.map(item => {
                                return item.product_id
                            })
                            var couponProductIds = coupon.products.map(product => {
                                return product.id;
                            })
                            let intersection = itemsProductIds.filter(x => couponProductIds.includes(x));
                            if (!intersection.length) return false;
                        }
                        return true
                    })
                }
            }
        })
    </script>
@endsection
