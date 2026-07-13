<style>
    li{
        font-weight: bolder !important;
    }
</style>
<aside class="main-sidebar" style='background-color: black !important;'>

    <section class="sidebar">

        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ asset('dashboard/img/avatar5.png') }}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p> {{ auth()->user()->name }} {{--auth()->user()->last_name--}}</p>
                <p style="font-size: 13px"><i class="fa fa-circle text-success"></i>
                        Radwan Steel
                   </p>
            </div>
        </div>

        <ul class="sidebar-menu" data-widget="tree">
        <li class="{{str_replace(['/',':','.'] , '' , route('dashboard.index'))}}"><a href="{{route('dashboard.index')}}"><i class="fa fa-th"></i><span>@lang('site.dashboard')</span></a></li>







        <li class="treeview">
            <a href="#"><i class="fa fa-television"></i> <span>الألات</span>
                  <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                  </span>
                </a>
            <ul class="treeview-menu">
                @if (Auth::user()->can('read_machine_types'))
                    <li class="{{str_replace(['/',':','.'] , '' , route('dashboard.machine_types.index'))}}"><a href="{{route('dashboard.machine_types.index')}}"><i class="fa fa-sitemap"></i><span>أنواع الألات</span></a></li>
                @endif
                @if (Auth::user()->can('read_machines'))
                    <li class="{{str_replace(['/',':','.'] , '' , route('dashboard.machines.index'))}}"><a href="{{route('dashboard.machines.index')}}"><i class="fa fa-television"></i><span>الألات</span></a></li>
                @endif
                @if (Auth::user()->can('reade_machine_groups'))
                <li class="{{str_replace(['/',':','.'] , '' , route('dashboard.machine_groups.index'))}}"><a href="{{route('dashboard.machine_groups.index')}}"><i class="fa fa-television"></i><span>تسجيل مجموعات للآلات</span></a></li>
                @endif
                {{-- <li class="{{str_replace(['/',':','.'] , '' , route('dashboard.machine_items.index'))}}"><a href="{{route('dashboard.machine_items.index')}}"><i class="fa fa-television"></i><span>صرف اصناف للآلات</span></a></li> --}}
                @if (Auth::user()->can('reade_machine_supplies'))
                <li class="{{str_replace(['/',':','.'] , '' , route('dashboard.machine_supplies.index'))}}"><a href="{{route('dashboard.machine_supplies.index')}}"><i class="fa fa-television"></i><span>صرف مستلزمات للآلات</span></a></li>
                @endif
            </ul>
        </li>

        <li class="treeview">
            <a href="#"><i class="fa fa-wrench"></i> <span>المستلزمات</span>
                  <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                  </span>
                </a>
            <ul class="treeview-menu">
                @if (Auth::user()->can('reade_supplie_types'))
                <li class="{{str_replace(['/',':','.'] , '' , route('dashboard.supplie_types.index'))}}"><a href="{{route('dashboard.supplie_types.index')}}"><i class="fa fa-sitemap"></i><span>أنواع مستلزمات التشغيل</span></a></li>
                @endif
                @if (Auth::user()->can('reade_supplies'))
                <li class="{{str_replace(['/',':','.'] , '' , route('dashboard.supplies.index'))}}"><a href="{{route('dashboard.supplies.index')}}"><i class="fa fa-tree"></i><span>مستلزمات التشغيل</span></a></li>
                @endif
                @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('branch_factor_respons') || Auth::user()->hasRole('safe_factor_response'))
                <li class="{{str_replace(['/',':','.'] , '' , route('dashboard.supplies.exchange_machine_supplies_view'))}}"><a href="{{route('dashboard.supplies.exchange_machine_supplies_view')}}"><i class="fa fa-exchange"></i><span>تحويل المستلزمات بين الآلات</span></a></li>
                @endif
            </ul>
        </li>

        <li class="treeview">
            <a href="#"><i class="fa fa-fire"></i> <span>التشغيل الداخلي</span>
                  <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                  </span>
                </a>
            <ul class="treeview-menu">
                @if (Auth::user()->can('reade_operation_orders'))
                <li class="{{str_replace(['/',':','.'] , '' , route('dashboard.operation_orders.index'))}}"><a href="{{route('dashboard.operation_orders.index')}}"><i class="fa fa-rocket"></i><span>أوامر التشغيل الداخلية </span></a></li>
                @endif
                @if (Auth::user()->can('reade_operation_order_results'))
                <li class="{{str_replace(['/',':','.'] , '' , route('dashboard.operation_order_results.index'))}}"><a href="{{route('dashboard.operation_order_results.index')}}"><i class="fa fa-retweet"></i><span>ناتج التشغيل الداخلي</span></a></li>
                @endif
            </ul>
        </li>

        <li class="treeview">
            <a href="#"><i class="fa fa-fire"></i> <span>التشغيل الخارجي</span>
                  <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                  </span>
                </a>
            <ul class="treeview-menu">
                @if (Auth::user()->can('reade_operation_orders'))
                <li class="{{str_replace(['/',':','.'] , '' , route('dashboard.operation_orders.index_out'))}}"><a href="{{route('dashboard.operation_orders.index_out')}}"><i class="fa fa-rocket"></i><span>أوامر التشغيل الخارجية</span></a></li>
                @endif
                @if (Auth::user()->can('reade_operation_order_results'))
                <li class="{{str_replace(['/',':','.'] , '' , route('dashboard.operation_order_results.index_out'))}}"><a href="{{route('dashboard.operation_order_results.index_out')}}"><i class="fa fa-retweet"></i><span>ناتج التشغيل الخارجي</span></a></li>
                @endif
            </ul>
        </li>

        <li class="treeview">
            <a href="#"><i class="fa fa-fire"></i> <span>التالف والمقاسات الخاصة</span>
                  <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                  </span>
                </a>
            <ul class="treeview-menu">
                @if (Auth::user()->can('reade_damages'))
                <li class="{{str_replace(['/',':','.'] , '' , route('dashboard.damages.index'))}}"><a href="{{route('dashboard.damages.index')}}"><i class="fa fa-rocket"></i><span>التالف</span></a></li>
                @endif
                @if (Auth::user()->can('reade_specials'))
                <li class="{{str_replace(['/',':','.'] , '' , route('dashboard.specials.index'))}}"><a href="{{route('dashboard.specials.index')}}"><i class="fa fa-retweet"></i><span>المقاسات الخاصة</span></a></li>
                @endif
            </ul>
        </li>

        @if (Auth::user()->can('reade_operation_orders'))
        <li class="{{str_replace(['/',':','.'] , '' , route('dashboard.operation_orders.summary'))}}"><a href="{{route('dashboard.operation_orders.summary')}}"><i class="fa fa-bar-chart"></i> <span> حركات تشغيل التصنيع</span></a></li>
        @endif
        
        @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 9 || Auth::user()->can('reports'))
        <li class="{{str_replace(['/',':','.'] , '' , route('dashboard.reports.index'))}}"><a href="{{route('dashboard.reports.index')}}"><i class="fa   fa-bar-chart"></i> <span>  التقارير</span></a></li>
        @endif

        @if (Auth::user()->hasRole('admin'))
        <li class="{{str_replace(['/',':','.'] , '' , route('dashboard.action_histories.index'))}}"><a href="{{route('dashboard.action_histories.index')}}"><i class="fa fa-history"></i> <span>  سجل النشاطات</span></a></li>
        @endif

        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('branch_factor_respons') || Auth::user()->hasRole('safe_factor_response') || auth()->user()->hasRole('factor_response'))
            <li><a href="http://radwansteel.org/login" target="_blank"><i class="fa fa-th-list"></i><span>سستم الحسابات</span></a></li>
        @endif

    </section>

</aside>

    <script>
    var currentUrl= window.location.href;
    var result1 = currentUrl.replaceAll("/", "");
    var result2 = result1.replaceAll(":", "");
    var resultFinal = result2.replaceAll(".", "");
    $(`.${resultFinal}`).addClass('active');
    $(`.${resultFinal}`).parent().parent().addClass('menu-open active');
    $(`.${resultFinal}`).parent().css('display', 'block')
    </script>
