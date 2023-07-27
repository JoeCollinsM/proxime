@extends('staff.layouts.app')

@section('page_title', 'Order #' . $order->id)

@section('css_libs')
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/bootstrap-datepicker/css/bootstrap-datepicker.standalone.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/select2/css/select2-bootstrap.css') }}">
@endsection

@section('css')
    <style>
        [v-cloak] {
            display: none;
        }

        .bubble {
            margin-top: 20px;
            position: relative;
            background: #ddd;
            color: #48484b;
            width: 100%;
            border-radius: 10px;
            padding: 10px;
        }

        .bubble:after {
            content: '';
            position: absolute;
            display: block;
            width: 0;
            z-index: 1;
            border-style: solid;
            border-color: #ddd transparent;
            border-width: 0 14px 20px;
            top: -20px;
            left: 12%;
            margin-left: -14px;
        }

        .notes {
            height: 30vh;
            overflow-y: scroll;
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
            <li class="breadcrumb-item"><a href="{{ route('staff.catalog.order.index') }}">Orders</a></li>
            <li class="breadcrumb-item active">{{ 'Order #' . $order->id }}</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->
   
    <div id="app">
        <div class="row">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header  d-flex justify-content-between align-content-center">
                        <div class="pull-left">
                            <h3>Order #{{ $order->id }} Details</h3>
                            <h6 class="text-black-50">Placed
                                on {{ optional($order->created_at)->format('M d, Y @ h:i a') }}</h6>
                        </div>
                        @if($order->status == 0)
                            <a href="{{ route('staff.catalog.order.edit', $order->id) }}"
                                class=""><i class="fa fa-edit"></i>
                                Edit</a>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h5>General</h5>
                                <p class="text-black-50">Status: @if($order->status == -1) <span
                                            class="badge badge-secondary">Placed</span> @elseif($order->status == 0)
                                        <span
                                                class="badge badge-warning">Pending</span> @elseif($order->status == 1)
                                        <span
                                                class="badge badge-info">Processing</span> @elseif($order->status == 2)
                                        <span
                                                class="badge badge-info">On The Way</span> @elseif($order->status == 3)
                                        <span
                                                class="badge badge-success">Completed</span> @elseif($order->status == 4)
                                        <span class="badge badge-warning">Hold</span> @elseif($order->status == 5)
                                        <span class="badge badge-danger">Canceled</span> @endif</p>
                                <p class="text-black-50">Customer: @if($order->user)<a
                                            href="{{ route('staff.catalog.user.edit', $order->user_id) }}">{{ $order->user->name }}</a>@endif
                                </p>
                                @if($order->shop)
                                    <p class="text-black-50">Shop: <a
                                                href="{{ route('staff.shop.edit', $order->shop_id) }}">{{ $order->shop->name }}</a>
                                    </p>
                                @endif
                                @if($review)<p class="text-black-50">Rating: @php echo $review->rating_html; @endphp
                                    <a href="{{ route('staff.catalog.review.index', ['type' => 'order', 'type_id' => $order->id]) }}">More...</a>
                                </p>@endif
                            </div>
                            <div class="col-md-4">
                                <h5>Billing</h5>
                                @if($billing)
                                    <p class="text-black-50">{{ $billing->name }}</p>
                                    <p class="text-black-50">{{ $billing->street_address_1 }}</p>
                                    @if($billing->street_address_2)<p
                                            class="text-black-50">{{ $billing->street_address_2 }}</p>@endif
                                    <p class="text-black-50">{{ $billing->city }}, {{ $billing->state }}
                                        , {{ $billing->country }}</p>
                                    @if($billing->email)
                                        <p class="text-black-50">Email Address: <a
                                                    href="mailto:{{ $billing->email }}">{{ $billing->email }}</a>
                                        </p>
                                    @endif
                                    @if($billing->phone)
                                        <p class="text-black-50">Phone Number: <a
                                                    href="tel:{{ $billing->phone }}">{{ $billing->phone }}</a></p>
                                    @endif
                                @endif
                            </div>
                            <div class="col-md-4">
                                <h5>Shipping</h5>
                                @if($shipping)
                                    <p class="text-black-50">{{ $shipping->name }}</p>
                                    <p class="text-black-50">{{ $shipping->street_address_1 }}</p>
                                    @if($shipping->street_address_2)<p
                                            class="text-black-50">{{ $shipping->street_address_2 }}</p>@endif
                                    <p class="text-black-50">{{ $shipping->city }}, {{ $shipping->state }}
                                        , {{ $shipping->country }}</p>
                                    @if($shipping->email)
                                        <p class="text-black-50">Email Address: <a
                                                    href="mailto:{{ $shipping->email }}">{{ $shipping->email }}</a>
                                        </p>
                                    @endif
                                    @if($shipping->phone)
                                        <p class="text-black-50">Phone Number: <a
                                                    href="tel:{{ $shipping->phone }}">{{ $shipping->phone }}</a></p>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
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
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex">
                                            <div class="image mr-2">
                                                <img class="img-thumbnail" style="width: 50px;height: 50px;"
                                                        src="{{ $item->variant?$item->variant->image:$item->product->image }}"
                                                        alt="{{ $order->product_title }}">
                                            </div>
                                            <div class="meta">
                                                <p>
                                                    <a href="{{ route('staff.catalog.product.edit', $item->product_id) }}">{{ $item->product_title }}</a>
                                                </p>
                                                @if($item->attrs && is_array($item->attrs))
                                                    <p class="text-black-50">
                                                        @foreach($item->attrs as $key => $value)
                                                            {{ $key }}: {{ $value }}<br>
                                                        @endforeach
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $currency->symbol }}{{ $item->price+$item->tax }}</td>
                                    <td>x{{ $item->quantity }}</td>
                                    <td>{{ $currency->symbol }}{{ ($item->price+$item->tax)*$item->quantity }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="3" class="text-right">Items Subtotal</td>
                                <td>{{ $currency->symbol }}{{ $order->gross_total }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right">Shipping Method</td>
                                <td>{{ $order->shipping_method_name }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right">Shipping Charge</td>
                                <td>{{ $currency->symbol }}{{ $order->shipping_charge }}</td>
                            </tr>
                            @if($order->coupon_code)
                                <tr>
                                    <td colspan="3" class="text-right">Coupon Code</td>
                                    <td>{{ $order->coupon_code }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right">Coupon Discount</td>
                                    <td>{{ $currency->symbol }}{{ $order->discount }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td colspan="3" class="text-right">Total</td>
                                <td>{{ $currency->symbol }}{{ $order->gross_total+$order->shipping_charge-$order->discount }}</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div
                            class="card-header 
                             d-flex justify-content-between align-content-center">
                        <div class="pull-left">
                            <h3>Payment Details</h3>
                        </div>
                        <div class="pull-right">
                            {{--                                <form class="form-inline" action="{{ route('staff.catalog.order.payment.store') }}"--}}
                            {{--                                      method="post">--}}
                            {{--                                    @csrf--}}
                            {{--                                    <div class="form-group">--}}
                            {{--                                        <select name="payment_method" class="custom-select" id="payment_method">--}}
                            {{--                                            @foreach($methods as $method)--}}
                            {{--                                                <option value="{{ $method->id }}">{{ $method->name }}</option>--}}
                            {{--                                            @endforeach--}}
                            {{--                                        </select>--}}
                            {{--                                    </div>--}}
                            {{--                                    <button type="submit" class="btn btn-primary">Generate Payment</button>--}}
                            {{--                                </form>--}}
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <td>Details</td>
                                <td>Amount</td>
                                <td>Trx</td>
                                <td>Note</td>
                                <td>Status</td>
                                <td>On</td>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td>
                                        Payment Track No. {{ $payment->track }} |
                                        Gateway {{ optional($payment->payment_method)->name }}
                                    </td>
                                    <td>
                                        Net {{ $payment->net_amount }} {{ $currency->code }} |
                                        Charge {{ $payment->charge }} {{ $currency->code }} |
                                        Gross {{ $payment->gross_amount }} {{ $currency->code }}
                                    </td>
                                    <td>{{ $payment->transaction_id }}</td>
                                    <td>{{ $payment->note }}</td>
                                    <td>
                                        @if($payment->status == 0)
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($payment->status == 1)
                                            <span class="badge badge-success">Completed</span>
                                        @elseif($payment->status == 2)
                                            <span class="badge badge-warning">Hold</span>
                                        @elseif($payment->status == 3)
                                            <span class="badge badge-danger">Canceled</span>
                                        @endif
                                    </td>
                                    <td>{{ optional($payment->updated_at)->format('M d, Y h:i a') }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header  d-flex justify-content-between align-content-center">
                        <div class="pull-left">
                            <h5>Shipment</h5>
                        </div>
                        @if(!$order->consignments()->whereIn('status', [0,1,3,4])->count())
                            <button
                                    data-toggle="modal"
                                    data-target="#addShipments"
                                    class="btn btn-primary btn-round"><i class="fa fa-plus"></i>
                                Add
                            </button>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <td>#</td>
                                    <td>Delivery By</td>
                                    <td>Commission</td>
                                    <td>Start On</td>
                                    <td>Resolved On</td>
                                    <td>Status</td>
                                    <td>View</td>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($order->consignments as $consignment)
                                    <tr>
                                        <td>{{ $consignment->id }}</td>
                                        <td>
                                            @if($consignment->delivery_man)
                                                <a href="{{ route('staff.catalog.delivery-man.edit', $consignment->delivery_man_id) }}">{{ $consignment->delivery_man->username }}</a>
                                            @endif
                                        </td>
                                        <td>{{ $consignment->commission }} {{ $currency->code }}</td>
                                        <td>{{ optional($consignment->start_on)->format('M d, Y') }}</td>
                                        <td>{{ optional($consignment->resolved_on)->format('M d, Y') }}</td>
                                        <td>
                                            @if($consignment->status == 0)
                                                <span class="badge badge-warning">Pending</span>
                                            @elseif($consignment->status == 1)
                                                <span class="badge badge-primary">Accepted</span>
                                            @elseif($consignment->status == 2)
                                                <span class="badge badge-danger">Rejected</span>
                                            @elseif($consignment->status == 3)
                                                <span class="badge badge-info">On The Way</span>
                                            @elseif($consignment->status == 4)
                                                <span class="badge badge-success">Shipped</span>
                                            @elseif($consignment->status == 5)
                                                <span class="badge badge-warning">Hold</span>
                                            @elseif($consignment->status == 6)
                                                <span class="badge badge-danger">Canceled</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button data-id="{{ $consignment->id }}"
                                                    data-status="{{ $consignment->status }}"
                                                    data-track="{{ $consignment->track }}"
                                                    data-notes="{{ $consignment->notes }}"
                                                    data-rejection_cause="{{ $consignment->rejection_cause }}"
                                                    data-images='@php echo json_encode($consignment->images); @endphp'
                                                    class="btn btn-primary btn-view-consignment btn-sm">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            @if($consignment->status == 4)
                                                @if($consignment->commissionExist())

                                                @else
                                                    <a class="btn btn-info  btn-sm"
                                                        href="{{ route('staff.catalog.consignment.commission', $consignment->id) }}"
                                                        onclick="return confirm('Are you sure to pay commission?')">Pay
                                                        Commission
                                                        ({{ $currency->symbol }}{{ $consignment->commission }})</a>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-content-center">
                        <div class="pull-left">
                            <h5>Update Status</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('staff.catalog.order.status', $order->id) }}" method="post">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label class="text-black-50">Status</label>
                                <select name="status" class="form-control select2">
                                    <option value="-1" disabled {{ $order->status == -1?'selected':'' }}>Placed
                                    </option>
                                    <option value="0" {{ $order->status == 0?'selected':'' }}>Pending
                                    </option>
                                    <option value="1" {{ $order->status == 1?'selected':'' }}>Processing
                                    </option>
                                    <option value="2" {{ $order->status == 2?'selected':'' }}>On The Way
                                    </option>
                                    <option value="3" {{ $order->status == 3?'selected':'' }}>Completed
                                    </option>
                                    <option value="4" {{ $order->status == 4?'selected':'' }}>Hold</option>
                                    <option value="5" {{ $order->status == 5?'selected':'' }}>Canceled
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block btn-round">
                                    Update
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                @if($order->status == 5)
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-content-center">
                            <div class="pull-left">
                                <h5>Refund</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($order->refundExist())
                                <h6 class="text-info">Already Refunded</h6>
                            @else
                                <form action="{{ route('staff.catalog.order.refund', $order->id) }}" method="post">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <button type="submit"
                                                onclick="return confirm('Are you sure to refund order? You can\'t revert this.')"
                                                class="btn btn-warning btn-block btn-round">
                                            Refund
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
                @if($order->status == 3 && $order->shop)
                    <div class="card mb-4">
                        <div
                                class="card-header d-flex justify-content-between align-content-center">
                            <div class="pull-left">
                                <h5>Shop Commission</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($order->shopCommissionExist())
                                <h6 class="text-info">Paid Already</h6>
                            @else
                                <form action="{{ route('staff.catalog.order.commission', $order->id) }}"
                                        method="post">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <button type="submit"
                                                onclick="return confirm('Are you sure to pay shop commission? You can\'t revert this.')"
                                                class="btn btn-warning btn-block btn-round">
                                            Pay
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-content-center">
                        <div class="pull-left">
                            <h5>Actions</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('staff.catalog.order.action', $order->id) }}" method="post">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label class="text-black-50">Select Action</label>
                                <select name="action" class="form-control select2">
                                    <option value="1">Send Order Invoice To Customer</option>
                                    <option value="2">Resend New Order Notification To Customer</option>
                                    <option value="3">Resend New Order Notification To Shop</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block btn-round">
                                    Perform
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-content-center">
                        <div class="pull-left">
                            <h5>Order Invoice</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('order.invoice', [$order->id, 'pdf']) }}" target="_blank"
                                    class="btn btn-outline-info">Download</a>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-outline-success"
                                        @click.prevent="printInvoice">Print
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card" v-cloak>
                    <div class="card-header d-flex justify-content-between align-content-center">
                        <div class="pull-left">
                            <h5>Order Notes</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="notes">
                            <div class="bubble" v-for="(n, i) in notes" :key="i">
                                @{{ n.content }}
                            </div>
                        </div>
                        <div class="new-note">
                            <div class="row no-gutters">
                                <div class="form-group col-sm-12">
                                    <select class="form-control" v-model="note.context">
                                        <option value="1">Staff & Customer</option>
                                        <option value="2">Only Staff</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-9">
                                <textarea class="form-control" v-model="note.content"
                                            placeholder="Write Something..."></textarea>
                                </div>
                                <div class="form-group col-sm-3">
                                    <button @click.prevent="addNote"
                                            class="btn btn-outline-success">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addShipments">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">New Shipment</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span></button>
                    </div>
                    <form action="{{ route('staff.catalog.consignment.store') }}" method="post">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="control-label d-block">Delivery Man <a
                                            href="{{ route('staff.catalog.delivery-man.create') }}" class="float-right">Add
                                        New</a></label>
                                <select name="delivery_man_id" class="form-control select2">
                                    @foreach($delivery_mans as $delivery_man)
                                        <option value="{{ $delivery_man->id }}">{{ $delivery_man->username }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-{{ config('proxime.delivery.type') == 'fixed'?'12':'6' }}">
                                    <div class="form-group">
                                        <label class="control-label">Start On</label>
                                        <input type="text" class="form-control date-picker" name="start_on">
                                    </div>
                                </div>
                                @if(config('proxime.delivery.type') == 'custom')
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">Commission</label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" class="form-control" id="commission"
                                                       name="commission" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">{{ $currency->code }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label class="control-label">Parcel Images (Optional)</label>
                                <input type="hidden" v-for="(image, i) in images" :key="i" name="images[]"
                                       :value="image.url">
                                <button class="btn btn-outline-info"
                                        @click.prevent="selectImages">
                                    <span v-if="images.length">@{{ images.length }} images selected</span>
                                    <span v-else>Select Image</span>
                                </button>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Notes</label>
                                <textarea name="notes" id="notes" class="form-control"
                                          placeholder="Write something..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>

        <div class="modal fade" id="view-consignment-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Details</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span></button>
                    </div>
                    <form method="post">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <p class="text-black-50">
                                Track: #<span class="track"></span>
                            </p>
                            <p class="text-black-50">
                                Notes: <span class="notes"></span>
                            </p>
                            <p class="text-black-50">
                                Rejection Cause (If Any): <span class="rejection_cause"></span>
                            </p>
                            <div class="images"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
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
    <script src="{{ asset('staff/admin/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('staff/admin/plugins/select2/js/select2.min.js') }}"></script>
@endsection

@section('js')
    <script>
        (function ($) {
            $(document).ready(function () {
                $.fn.select2.defaults.set("theme", "bootstrap");
                $('.select2').select2()
                $('.date-picker').datepicker({
                    format: "dd-mm-yyyy",
                    minDate: new Date(),
                    startDate: new Date(),
                })
            })
            $(document).on('click', '.btn-view-consignment', function (e) {
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
                        var url = '{{ url('staff/catalog/consignment') }}/' + data.value;
                        $('#view-consignment-modal form').attr('action', url);
                    } else if (data.name == 'images') {
                        let html = ''
                        let images = JSON.parse(data.value)
                        if (images) {
                            images.forEach(image => {
                                html += '<img src="' + image + '" class="img-thumbnail" style="width: 100px;height: 100px;">';
                            })
                        }
                        $('#view-consignment-modal .images').html(html)
                    } else {
                        $('#view-consignment-modal .' + data.name).text(data.value);
                    }
                })
                $('#view-consignment-modal').modal('show');
            })
        })(jQuery)
        window.app = new Vue({
            el: '#app',
            data: {
                notes: @json($notes),
                note: {
                    context: 1,
                    content: null
                },
                images: []
            },
            mounted() {
                this.scrollToBottom()
            },
            methods: {
                selectImages() {
                    openMediaManager(items => {
                        window.app.images = items
                    }, 'image', 'Select Images')
                },
                addNote() {
                    $.post('{{ route('staff.catalog.order.note.store') }}', {
                        _token: '{{ csrf_token() }}',
                        order_id: '{{ $order->id }}',
                        context: this.note.context,
                        content: this.note.content,
                    }).done(response => {
                        window.app.notes.push(response)
                        window.app.note = {
                            context: 1,
                            content: null
                        }
                        window.app.scrollToBottom()
                    })
                },
                scrollToBottom() {
                    const notesContainer = $(".notes");
                    notesContainer.animate({scrollTop: notesContainer[0].scrollHeight}, 1000);
                },
                downloadInvoice() {
                    window.open('', '_blank');
                },
                printInvoice() {
                    var win = window.open('{{ route('order.invoice', [$order->id, '']) }}', '_blank');
                    win.print()
                    window.close()
                },
            }
        })
    </script>
@endsection
