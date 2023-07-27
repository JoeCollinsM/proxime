<div id="sidebar-wrapper" data-simplebar="" data-simplebar-auto-hide="true">
     <div class="brand-logo">
      <a href="{{ route('staff.dashboard') }}">
       <img src="{{ config('ui.logo.large') }}" class="logo-icon" alt="{{ config('app.name') }}">
       <h5 class="logo-text">{{ config('app.name') }}</h5>
     </a>
   </div>

   <div class="user-details">
        <div class="media align-items-center user-pointer collapsed" data-toggle="collapse" data-target="#user-dropdown">
            <div class="avatar"><img class="mr-3 side-user-img" src="{{ auth()->guard('staff')->user()->avatar }}" alt=""></div>
            <div class="media-body">
                <h6 class="side-user-name">{{ auth()->guard('staff')->user()->name }}</h6>
            </div>
        </div>
        <div id="user-dropdown" class="collapse">
            <ul class="user-setting-menu">
                <li><a href="{{ route('staff.profile') }}"><i class="icon-user"></i>  My Profile</a></li>
                <form action="{{ route('staff.logout') }}" method="post" id="logout-form"
                                        class="d-none">
                                        @csrf
                                    </form>
                <li><a href="{{ route('staff.logout') }}"
                            onclick="event.preventDefault();document.getElementById('logout-form').submit()"><i class="icon-power"></i> Logout</a></li>
            </ul>
        </div>
    </div>
    
   <ul class="sidebar-menu">
      <li class="sidebar-header">MAIN NAVIGATION</li>
      <li class="{{ request()->route()->getName() == 'staff.dashboard'?' active':'' }}">
        <a href="{{ route('staff.dashboard') }} " class="waves-effect">
          <i class="zmdi zmdi-view-dashboard"></i> <span>Dashboard</span>
        </a>
      </li>
      @able('manage_catalog')
      <li>
        <a class="waves-effecthas-arrow" href="#" aria-expanded="false">
          <i class="fa fa-tags"></i> <span>Catalog</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="sidebar-submenu">
            <!-- categories -->
            <li>
                <a aria-expanded="false" href="#"> Categories <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="sidebar-submenu">
                    <li class="{{ in_array(request()->route()->getName(), ['staff.catalog.category.index', 'staff.catalog.category.edit'])?'active':'' }}">
                        <a  href="{{ route('staff.catalog.category.index') }}">All Categories</a>
                    </li>
                    <li class="{{ in_array(request()->route()->getName(), ['staff.catalog.category.create'])?'active':'' }}">
                        <a href="{{ route('staff.catalog.category.create') }}">Add New</a>
                    </li>
                </ul>
            </li>

            <!-- attributes -->
            <li class="{{ request()->is('staff/catalog/attribute*')?'active':'' }}">
                <a href="{{ route('staff.catalog.attribute.index') }}">Attributes</a>
            </li>

            <!-- products -->
            <li>
                <a aria-expanded="false" href="#"> Products <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="sidebar-submenu">
                    <li class="{{ in_array(request()->route()->getName(), ['staff.catalog.product.index', 'staff.catalog.product.edit'])?'active':'' }}">
                        <a href="{{ route('staff.catalog.product.index') }}">All Products</a>
                    </li>
                    <li class="{{ in_array(request()->route()->getName(), ['staff.catalog.product.create'])?'active':'' }}">
                        <a href="{{ route('staff.catalog.product.create') }}">Add New</a>
                    </li>
                </ul>
            </li>

            <!-- orders -->
            <li>
                <a aria-expanded="false" href="#"> Orders <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="sidebar-submenu">
                    <li class="{{ in_array(request()->route()->getName(), ['staff.catalog.order.index', 'staff.catalog.order.edit'])?'active':'' }}">
                        <a href="{{ route('staff.catalog.order.index') }}">All Orders</a>
                    </li>
                    <li class="{{ in_array(request()->route()->getName(), ['staff.catalog.order.create'])?'active':'' }}">
                        <a href="{{ route('staff.catalog.order.create') }}">Add New</a>
                    </li>
                </ul>
            </li>

            <!-- coupons -->
            <li class="{{ request()->route()->getName() == 'staff.catalog.coupon.index'?'active':'' }}">
                        <a href="{{ route('staff.catalog.coupon.index') }}">Coupons</a>
            </li>  
        </ul>
      </li>
      @endable

      {{-- manage users --}}
      @able('manage_users')
      <li  @if(in_array(request()->route()->getName(), ['staff.catalog.user.index', 'staff.catalog.delivery-man.index'])) class="mm-active" @endif >
        <a class="waves-effecthas-arrow" href="#" aria-expanded="false">
          <i class="fa fa-users"></i> <span>User Management</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="sidebar-submenu @if(in_array(request()->route()->getName(), ['staff.catalog.user.index', 'staff.catalog.delivery-man.index'])) mm-collapse mm-show @endif " >
            <!-- customers -->
            <li class="{{ request()->route()->getName() == 'staff.catalog.user.index'?'active':'' }}">
                <a href="{{ route('staff.catalog.user.index') }}">Customers</a>
            </li>

            <!-- Delivery Man -->
            <li class="{{ request()->route()->getName() == 'staff.catalog.delivery-man.index'?'active':'' }}">
                <a href="{{ route('staff.catalog.delivery-man.index') }}">Delivery Man</a>
            </li>
        </ul>
      </li>

      @endable

      <!-- manage stores -->
      @able('manage_shop')
        <li  @if(in_array(request()->route()->getName(), ['staff.shop-category.index', 'staff.shop.index', 'staff.shop.create'])) class="mm-active" @endif >
        <a class="waves-effecthas-arrow" href="#" aria-expanded="false">
          <i class="fa fa-institution"></i> <span>Shop Management</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        
        <ul class="sidebar-submenu @if(in_array(request()->route()->getName(), ['staff.shop-category.index', 'staff.shop.index', 'staff.shop.create'])) mm-collapse mm-show @endif " >
            <li class="{{ request()->route()->getName() == 'staff.shop-category.index'?'active':'' }}">
                <a href="{{ route('staff.shop-category.index') }}">Categories</a>
            </li>
            <li class="{{ request()->route()->getName() == 'staff.shop.index'?'active':'' }}">
                <a href="{{ route('staff.shop.index') }}">All Shops</a>
            </li>
            <li class="{{ request()->route()->getName() == 'staff.shop.create'?'active':'' }}">
                <a href="{{ route('staff.shop.create') }}">New Shop</a>
            </li>
        </ul>
      </li>
      @endable

      <!-- manage transactions -->
      @able('manage_transaction')
      <li>
        <a class="waves-effecthas-arrow" href="#" aria-expanded="false">
          <i class="fa fa-bitcoin"></i> <span>Transactions</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="sidebar-submenu">
            <li class="{{ in_array(request()->route()->getName(), ['staff.transaction.index'])?'active':'' }}">
                <a  href="{{ route('staff.transaction.index') }}">All Transactions</a>
            </li>
            <li class="{{ in_array(request()->route()->getName(), ['staff.transaction.create'])?'active':'' }}">
                <a href="{{ route('staff.transaction.create') }}">Add New</a>
            </li>
        </ul>
      </li>
      @endable

      <!-- manage withdrawals -->
      @able('manage_withdraw')
      <li class="{{ request()->route()->getName() == 'staff.withdraw.index'?'active':'' }}">
        <a href="{{ route('staff.withdraw.index') }} " class="waves-effect">
          <i class="zmdi zmdi-money-box"></i> <span>Withdrawals</span>
        </a>
      </li>
      @endable

      <!-- manage roles -->
      @able('manage_role')
      <li class="{{ request()->route()->getName() == 'staff.role.index'?'active':'' }}">
        <a href="{{ route('staff.role.index') }} " class="waves-effect">
          <i class="zmdi zmdi-layers"></i> <span>Role Management</span>
        </a>
      </li>
      @endable

      <!-- manage staff -->
      @able('manage_staff')
      <li class="{{ request()->route()->getName() == 'staff.staff.index'?'active':'' }}">
        <a href="{{ route('staff.staff.index') }} " class="waves-effect">
          <i class="zmdi zmdi-lock"></i> <span>Staff Management</span>
        </a>
      </li>
      @endable

      <!-- manage resports -->
      @able('manage_report')
      <li @if(in_array(request()->route()->getName(), ['staff.report.sales-by-date', 'staff.report.sales-by-category', 'staff.report.sales-by-product'])) class="mm-active" @endif>
        <a class="waves-effecthas-arrow" href="#" aria-expanded="false">
          <i class="zmdi zmdi-case-download"></i> <span>Reports</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class= " sidebar-submenu @if(in_array(request()->route()->getName(), ['staff.setting.currency.index', 'staff.setting.language.index', 'staff.setting.payment-method.index', 'staff.setting.shipping-method.index', 'staff.setting.banner.index', 'staff.setting.general.index', 'staff.setting.logo.index', 'staff.setting.email.index', 'staff.setting.service.index', 'staff.setting.application.update.index']) || request()->is('staff/setting/withdraw-method*')) mm-collapse mm-active @endif " >
            <li class="{{ request()->route()->getName() == 'staff.report.sales-by-date'?'active':'' }}">
                <a href="{{ route('staff.report.sales-by-date') }}">Sales By Date</a>
            </li>
            <li class="{{ request()->route()->getName() == 'staff.report.sales-by-category'?'active':'' }}">
                <a href="{{ route('staff.report.sales-by-category') }}">Sales By Category</a>
            </li>
            <li class="{{ request()->route()->getName() == 'staff.report.sales-by-product'?'active':'' }}">
                <a href="{{ route('staff.report.sales-by-product') }}">Sales By Product</a>
            </li>
        </ul>
      </li>
      @endable

      @able('manage_setting')
      <li @if(in_array(request()->route()->getName(), ['staff.setting.currency.index', 'staff.setting.language.index', 'staff.setting.payment-method.index', 'staff.setting.shipping-method.index', 'staff.setting.banner.index', 'staff.setting.general.index', 'staff.setting.logo.index', 'staff.setting.email.index', 'staff.setting.service.index', 'staff.setting.application.update.index']) || request()->is('staff/setting/withdraw-method*')) class="mm-active" @endif>
        <a class="waves-effecthas-arrow" href="#" aria-expanded="false">
          <i class="zmdi zmdi-settings"></i> <span>settings</span>
          <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="sidebar-submenu @if(in_array(request()->route()->getName(), ['staff.report.sales-by-date', 'staff.report.sales-by-category', 'staff.report.sales-by-product'])) mm-collapse mm-active @endif" >
            <li class="{{ in_array(request()->route()->getName(), ['staff.setting.general.index'])?'active':'' }}">
                <a href="{{ route('staff.setting.general.index') }}">General Settings</a>
            </li>
            <li @if(in_array(request()->route()->getName(), ['staff.setting.template.index', 'staff.setting.template.edit'])) class="mm-active" @endif>
                <a href="#" aria-expanded="false">Templates  <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="sidebar-submenu @if(in_array(request()->route()->getName(), ['staff.setting.template.index', 'staff.setting.template.edit'])) mm-collapse mm-active @endif">
                    <li class="{{ request()->is('staff/setting/template', 'staff/setting/template/email')?'active':'' }}"><a href="{{ route('staff.setting.template.index') }}">Email Template</a></li>
                    <li class="{{ request()->is('staff/setting/template/sms')?'active':'' }}"><a href="{{ route('staff.setting.template.index', 'sms') }}">SMS Template</a></li>
                    <li class="{{ request()->is('staff/setting/template/fcm')?'active':'' }}"><a href="{{ route('staff.setting.template.index', 'fcm') }}">FCM Template</a></li>
                </ul>
            </li>

            <li class="{{ request()->route()->getName() == 'staff.setting.mpesa.index'?'active':'' }}">
                <a href="{{ route('staff.setting.mpesa.index') }}">Mpesa</a>

            <li class="{{ request()->route()->getName() == 'staff.setting.currency.index'?'active':'' }}">
                <a href="{{ route('staff.setting.currency.index') }}">Currencies</a>
            </li>
            <li class="{{ request()->route()->getName() == 'staff.setting.language.index'?'active':'' }}">
                <a href="{{ route('staff.setting.language.index') }}">Languages</a>
            </li>
            <li class="{{ request()->route()->getName() == 'staff.setting.payment-method.index'?'active':'' }}">
                <a href="{{ route('staff.setting.payment-method.index') }}">Payment Methods</a>
            </li>
            <li class="{{ request()->is('staff/setting/withdraw-method*')?'active':'' }}">
                <a href="{{ route('staff.setting.withdraw-method.index') }}">Withdraw Methods</a>
            </li>
            <li class="{{ request()->route()->getName() == 'staff.setting.shipping-method.index'?'active':'' }}">
                <a href="{{ route('staff.setting.shipping-method.index') }}">Shipping Methods</a>
            </li>
            <li class="{{ in_array(request()->route()->getName(), ['staff.setting.banner.index', 'staff.setting.banner.create', 'staff.setting.banner.edit'])?'active':'' }}">
                <a href="{{ route('staff.setting.banner.index') }}">Banners</a>
            </li>
            <li class="{{ in_array(request()->route()->getName(), ['staff.setting.logo.index'])?'active':'' }}">
                <a href="{{ route('staff.setting.logo.index') }}">Logo Settings</a>
            </li>
            <li class="{{ in_array(request()->route()->getName(), ['staff.setting.email.index'])?'active':'' }}">
                <a href="{{ route('staff.setting.email.index') }}">Email Settings</a>
            </li>
            <li class="{{ in_array(request()->route()->getName(), ['staff.setting.app.index'])?'active':'' }}">
                <a href="{{ route('staff.setting.app.index') }}">App Settings</a>
            </li>
            <li class="{{ in_array(request()->route()->getName(), ['staff.setting.service.index'])?'active':'' }}">
                <a href="{{ route('staff.setting.service.index') }}">Service Settings</a>
            </li>
            {{-- <li class="{{ in_array(request()->route()->getName(), ['staff.setting.application.update.index'])?'active':'' }}">
                <a href="{{ route('staff.setting.application.update.index') }}">Update Manager</a>
            </li> --}}
        </ul>
      </li>
      @endable
    </ul>
   
   </div>

