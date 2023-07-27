@extends('shop.layouts.app')

@section('css_libs')
    <!-- Apex chart -->
    <link rel="stylesheet" href="{{ asset('staff/admin/plugins/apexcharts/css/apexcharts.css') }}">
@endsection

@section('content')
    <!-- Breadcrumb-->
    <div class="row pt-2 pb-2">
        <div class="col-sm-9">
		    <h4 class="page-title">Dashboard</h4>
		    <ol class="breadcrumb">
            <li class="breadcrumb-item">Dashboard</li>
         </ol>
	   </div>
     </div>
    <!-- End Breadcrumb-->


            <!-- Start analytic area -->
               
            <div class="row mt-3">
                <div class="col-12 col-lg-6 col-xl-3">
                    <div class="card gradient-deepblue">
                    <div class="card-body">
                        <h5 class="text-white mb-0">{{ $currency->symbol }}{{ $total_sale }} <span class="float-right"><i class="fa fa-shopping-cart"></i></span></h5>
                            <!-- <div class="progress my-3" style="height:3px;">
                            <div class="progress-bar" style="width:55%"></div>
                            </div> -->
                        <p class="mb-0 text-white small-font">Total Sales <span class="float-right">   <i class="zmdi zmdi-long-arrow-up"></i></span></p>
                        </div>
                    </div> 
                </div>
                
                <div class="col-12 col-lg-6 col-xl-3">
                    <div class="card gradient-orange">
                    <div class="card-body">
                        <h5 class="text-white mb-0">{{ $currency->symbol }}{{ $today_sale }} <span class="float-right"><i class="fa fa-usd"></i></span></h5>
                            <!-- <div class="progress my-3" style="height:3px;">
                            <div class="progress-bar" style="width:55%"></div>
                            </div> -->
                        <p class="mb-0 text-white small-font">Today's Sale <span class="float-right">  <i class="zmdi zmdi-long-arrow-up"></i></span></p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 col-xl-3">
                    <div class="card gradient-ohhappiness">
                        <div class="card-body">
                        <h5 class="text-white mb-0">{{ $new_orders }}<span class="float-right"><i class="fa fa-eye"></i></span></h5>
                            <!-- <div class="progress my-3" style="height:3px;">
                            <div class="progress-bar" style="width:55%"></div>
                            </div> -->
                        <p class="mb-0 text-white small-font">New Orders <span class="float-right"> <i class="zmdi zmdi-long-arrow-up"></i></span></p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 col-xl-3">
                    <div class="card gradient-ibiza">
                        <div class="card-body">
                        <h5 class="text-white mb-0">{{ auth('shop')->user()->balance }} {{ \App\Models\Currency::getDefaultCurrency()->code }}<span class="float-right"><i class="fa fa-envira"></i></span></h5>
                            <!-- <div class="progress my-3" style="height:3px;">
                            <div class="progress-bar" style="width:55%"></div>
                            </div> -->
                        <p class="mb-0 text-white small-font">Balance <span class="float-right"> <i class="zmdi zmdi-long-arrow-up"></i></span></p>
                        </div>
                    </div>
                </div>
                </div>
          
            <!-- End analytic area -->


            <!-- Start trafic area -->
                <div class="row">
                    <div class="col-xl-5 col-12">
                        <div class="card trade-history">
                            <div class="card-header">
                                Order Status
                            </div>
                            <div class="card-body">
                                <div id="order-status"></div>
                                <ul class="chart-coin-list email-sent-list">
                                    <li><span
                                                class="color-box color-box-1"></span> {{ round($t_orders == 0?0:($new_orders*100)/$t_orders) }}
                                        % New
                                    </li>
                                    <li><span
                                                class="color-box color-box-2"></span> {{ round($t_orders == 0?0:($processing_orders*100)/$t_orders) }}
                                        % Processing
                                    </li>
                                    <li><span
                                                class="color-box color-box-3"></span> {{ round($t_orders == 0?0:($way_orders*100)/$t_orders) }}
                                        % On The Way
                                    </li>
                                    <li><span
                                                class="color-box color-box-1"></span> {{ round($t_orders == 0?0:($completed_orders*100)/$t_orders) }}
                                        % Completed
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-7 col-12 mb-xl-0">
                        <div class="card">
                            <div class="card-header cypto-trading">
                                <div class="pull-left">
                                    <h3>Sales Statistics of this month (In {{ $currency->code }})</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="comparison"></div>
                            </div>
                        </div>
                    </div>
                </div>
            <!-- End  trafic area -->


            <!-- Start Order List -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                Pending Orders
                            </div>
                            <div class="card-body">
                                <div class="">
                                    <table class="table table-striped table-bordered"
                                           id="data-table" style="width:100%">
                                        <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Order</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Total</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
           
            <!-- End Order List -->

        
@endsection

@section('js_libs')

    <!-- Apex chart -->
    <script src="{{ asset('staff/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('staff/js/jquery.circlechart.js') }}"></script>

   
@endsection

@section('js')
    <script>
        $(document).ready(function () {

            $('#data-table').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{{ route('shop.catalog.order.index', ['status' => 0]) }}',
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'order', name: 'order', orderable: false},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'status', name: 'status'},
                    {data: 'total', name: 'total', searchable: false}
                ],
                order: [[0, 'desc']]
            });

            var options = {
                series: [{{ round($t_orders == 0?0:($new_orders*100)/$t_orders) }}, {{ round($t_orders == 0?0:($processing_orders*100)/$t_orders) }}, {{ round($t_orders == 0?0:($way_orders*100)/$t_orders) }}, {{ round($t_orders == 0?0:($completed_orders*100)/$t_orders) }}],
                chart: {
                    height: 295,
                    type: 'radialBar',
                },
                plotOptions: {
                    radialBar: {
                        dataLabels: {
                            name: {
                                fontSize: '22px',
                            },
                            value: {
                                fontSize: '16px',
                            },
                            total: {
                                show: false,
                                label: 'Total',
                                formatter: function (w) {
                                    return 175
                                }
                            }
                        }
                    }
                },
                labels: ['New', 'Processing', 'On The Way', 'Completed'],
                fill: {
                    colors: ['#F1682C', '#0EB7FE', '#F1682C', '#2E42B4']
                }
            };

            var chart = new ApexCharts(document.querySelector("#order-status"), options);
            chart.render();


            var options = {
                series: [
                    {
                        name: 'Order Placed',
                        data: @json($order_placed_in_month_by_date)
                    },
                    {
                        name: 'Net Sale',
                        data: @json($net_sale_in_month_by_date)
                    },
                    {
                        name: 'Discount',
                        data: @json($discount_in_month_by_date)
                    },
                    {
                        name: 'Gross Sale',
                        data: @json($gross_sale_in_month_by_date)
                    },
                ],
                chart: {
                    type: 'line',
                    height: 310,
                    stacked: true,
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: true
                    }
                },
                legend: {
                    show: false
                },
                responsive: [{
                    breakpoint: 480,
                    options: {}
                }],
                plotOptions: {
                    bar: {
                        horizontal: false,
                    },
                },
                stroke: {
                    curve: 'smooth',
                },
                xaxis: {
                    type: 'datetime',
                    categories: @json($dates_in_month_by_order),
                },
            };

            var chart = new ApexCharts(document.querySelector("#comparison"), options);
            chart.render();


            Highcharts.chart('nimmu-audience-growth', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: ''
                },
                xAxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: ''
                    }
                },
                tooltip: {
                    enabled: false,
                    pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
                    shared: true
                },
                plotOptions: {
                    column: {
                        stacking: 'normal',
                        pointWidth: 7,
                    },
                },
                legend: {
                    align: 'right',
                    x: 0,
                    verticalAlign: 'top',
                    y: -17,
                    floating: false,
                    shadow: false,
                    enabled: false
                },
                exporting: {
                    enabled: false
                },
                series: [{
                    name: 'BTC',
                    data: [2, 4, 4, 2, 5, 2, 4, 4, 2, 1, 2, 3],
                    color: '#BD20D3'
                }],
                // DashStyle:'Dash',
            });
        })
    </script>
@endsection
