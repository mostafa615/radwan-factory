<!DOCTYPE html>
<html dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">
<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@lang('site.atis')</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">



  <!--bootstrap-->
        <link rel="shortcut icon" href="{{ asset('dashboard/images/logo-2.png') }}" type="image/png">

    {{-- <link rel="stylesheet" href="{{ asset('dashboard/css/bootstrap.min.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('dashboard/datatable/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard/css/bootstrap-switch.css') }}">
    {{-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"> --}}
    <link rel="stylesheet" href="{{ asset('dashboard/css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard/css/skin-blue.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard/datatable/dataTables.bootstrap.min.css') }}">
    <!--<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    {{-- <link href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css" rel="stylesheet" /> --}}
    <link href="{{ asset('css/iziToast.css') }}" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.6.4/css/buttons.dataTables.min.css" rel="stylesheet" />
    {{-- <link rel="stylesheet" href="{{ asset('dashboard/datatable/dataTables.material.min.css') }}"> --}}
    {{-- <link rel="stylesheet" href="{{ asset('dashboard/datatable/bootstrap.min.css') }}"> --}}
    {{-- <link rel="stylesheet" href="{{ asset('dashboard/datatable/buttons.dataTables.min.css') }}"> --}}

    {{-- <link rel="stylesheet" href="{{ asset('css/style.min.css') }}"> --}}
          <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />

  <!-- Theme style -->

  <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect. -->
        @if (app()->getLocale() == 'ar')
          <link rel="stylesheet" href="{{ asset('dashboard/css/font-awesome-rtl.min.css') }}">
          <link rel="stylesheet" href="{{ asset('dashboard/css/AdminLTE-RTL.min.css') }}">
          <link rel="stylesheet" href="{{ asset('dashboard/css/fontcairo.css') }}">
          <link rel="stylesheet" href="{{ asset('dashboard/css/bootstrap-rtl.min.css') }}">
          <link rel="stylesheet" href="{{ asset('dashboard/css/rtl.css') }}">

          <style>
            body, h1, h2, h3, h4, h5, h6{
              font-family: 'Cairo', sans-serif !important;
            }
          </style>
        @else
          <link rel="stylesheet" href="{{ asset('dashboard/css/adminlte.min.css') }}">
          <link rel="stylesheet" href="{{ asset('dashboard/css/font-awesome.min.css') }}">
          <link rel="stylesheet" href="{{ asset('dashboard/css/fontbasic.css') }}">
        @endif

          <link rel="stylesheet" href="{{ url('/css/w3.css') }}">
          <link rel="stylesheet" href="{{ url('/css/switch.css') }}">
        <style>
            .mr-2{
                margin-right: 5px;
            }
            .mt-2{
                margin-top: 5px;
            }

            .loader {
                border: 5px solid #f3f3f3;
                border-radius: 50%;
                border-top: 5px solid green;
                width: 60px;
                height: 60px;
                -webkit-animation: spin 1s linear infinite; /* Safari */
                animation: spin 1s linear infinite;
            }
            select{
                padding: 2px 12px !important;
            }

            .small-box>.inner {
                padding: 30px;
            }

            /* Safari */
            @-webkit-keyframes spin {
                0% {
                    -webkit-transform: rotate(0deg);
                }
                100% {
                    -webkit-transform: rotate(360deg);
                }
            }

            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }
                100% {
                    transform: rotate(360deg);
                }
            }

            .pdfobject-container {
                width: 800px;
                margin: 30px auto;
            }
            #viewpdf{
                width: 100%;
                height: 400px;
                border: 1px solid rgb(0, 0, .2);
            }
            .material-switch > input:checked + label::before {
                background: #33b35a;
                opacity: 0.5;
            }
            .material-switch > input:checked + label::after {
                background: #33b35a;
                left: 20px;
            }
            .small-box .inner a{
                color: white;
                font-size: 19px;
            }
            input[type="date"]::-webkit-calendar-picker-indicator {
            background: transparent;
            bottom: 0;
            color: transparent;
            cursor: pointer;
            height: auto;
            left: 0;
            position: absolute;
            right: 0;
            top: 0;
            width: auto;
            }

            .select2-js {
                width: 100% !important;
            }

            .form-group > .select2-container {
                width: 100% !important;
            }

            th{
                position: sticky !important;
                top: 0;
                background-color: black !important;
                border: 1px solid grey !important;
                font-weight: bolder;
                text-align: center;
                color: white;
                &:first-of-type {
                    text-align: right;
                }
            }
            td{
                border: 1px solid black;
                text-align: center !important;
                vertical-align: middle !important;
            }
            table.dataTable thead .sorting:after{
                opacity: 1;
            }
            table{
                width: 100% !important;
                border-collapse: collapse;
                border-spacing: 0;
            }
            table.dataTable{
                margin-top: 0px !important;
            }


            /* width */

            ::-webkit-scrollbar {
                width: 7px;
                height: 7px;
            }


            /* Track */

            ::-webkit-scrollbar-track {
                box-shadow: inset 0 0 5px #33b35a;
                border-radius: 10px;
            }


            /* Handle */

            ::-webkit-scrollbar-thumb {
                background: #33b35a;
                border-radius: 10px;
            }


            /* Handle on hover */

            ::-webkit-scrollbar-thumb:hover {
                background: #33b35a;
            }


        </style>
        @yield('css')


        <script src="{{ asset('dashboard/js/jquery.min.js')}}"></script>

        <link rel="stylesheet" href=" {{ asset('dashboard/plugins/noty/noty.css')}}">
        <script src="{{ asset('dashboard/plugins/noty/noty.min.js')}}"></script>

        <link rel="stylesheet" href="{{ asset('dashboard/plugins/icheck/all.css')}}">

        {{--morris--}}
        <link rel="stylesheet" href="{{ asset('dashboard/plugins/morris/morris.css') }}">

        <!--<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>-->
        <!--<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>-->

        <script src="https://js.pusher.com/4.3/pusher.min.js"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>


  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- Main Header -->
  <header class="main-header">

    <!-- Logo -->
    <a href="" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>R</b></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>RADWAN</b></span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation" style='background-color: black !important;'>
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
            @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('branch_factor_respons') || Auth::user()->hasRole('safe_factor_response') || Auth::user()->hasRole('factor_response') || Auth::user()->hasRole('store_factor_response'))
            <li data-toggle="tooltip" data-placement="bottom" title="استكمال أوامر التشغيل الخارجي" class="dropdown">
                <a href="{{route('dashboard.operation_orders.index_out')}}?type=complete">
                    <i class="fa fa-bell"></i>
                    <span class="label label-success">{{ count(
                        App\OperationOrder::where('is_complete', 0)->where('out_operation', 1)->get(),
                    ) }}</span>
                </a>
            </li>
            @endif
            
@if (!auth()->user()->hasRole('machine_response'))
                            <li data-toggle="tooltip" data-placement="bottom" title=" ناتج أوامر التشغيل الخارجي "
                                class="dropdown">
                                <a href="{{ route('dashboard.operation_order_results.index_out') }}?type=not_all">
                                    <i class="fa fa-building"></i>
                                    @if(auth()->user()->hasRole('admin'))
                                        <span class="label label-success">{{ count(
                                                App\OperationOrderResult::where('store_confirm', 0)->whereHas('operationOrder', function ($q) {
                                                        $q->where('out_operation', 1);
                                                    })->get(),
                                            ) }}</span>
                                    @elseif(auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('safe_factor_response'))
                                        <span
                                            class="label label-success">{{ count(
                                                App\OperationOrderResult::where('store_confirm', 0)->whereHas('operationOrder', function ($q) {
                                                        $q->where('out_operation', 1);
                                                    })->get(),
                                            ) }}</span>
                                    @elseif(auth()->user()->hasRole('factor_response'))
                                        <span
                                            class="label label-success">{{ count(
                                                App\OperationOrderResult::where('store_confirm', 0)->whereHas('operationOrder', function ($q) {
                                                        $q->where('out_operation', 1)->where('supervisor_process', auth()->user()->id);
                                                    })->get(),
                                            ) }}</span>
                                    @elseif(auth()->user()->hasRole('store_factor_response'))
                                        <span
                                            class="label label-success">{{ count(
                                                App\OperationOrderResult::where('store_confirm', 0)->whereHas('operationOrder', function ($q) {
                                                        $q->where('out_operation', 1)->where('supervisor_store', auth()->user()->id);
                                                    })->get(),
                                            ) }}</span>
                                    @else
                                        <span
                                            class="label label-success">{{ count(
                                                App\OperationOrderResult::toUser()->where('store_confirm', 0)->whereHas('operationOrder', function ($q) {
                                                        $q->where('out_operation', 1);
                                                    })->get(),
                                            ) }}</span>
                                    @endif
                                </a>
                            </li>

                            <li data-toggle="tooltip" data-placement="bottom" title="ناتج أوامر التشغيل الداخلي"
                                class="dropdown">
                                <a href="{{ route('dashboard.operation_order_results.index') }}?type=not_all">
                                    <i class="fa fa-building"></i>
                                    @if(auth()->user()->hasRole('admin'))
                                        <span
                                            class="label label-success">{{ count(
                                                App\OperationOrderResult::where('store_confirm', 0)->whereHas('operationOrder', function ($q) {
                                                        $q->where('out_operation', 0);
                                                    })->get(),
                                            ) }}</span>
                                    @elseif(auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('safe_factor_response'))
                                        <span
                                            class="label label-success">{{ count(
                                                App\OperationOrderResult::where('confirmed', 0)->whereHas('operationOrder', function ($q) {
                                                        $q->where('out_operation', 0);
                                                    })->get(),
                                            ) }}</span>
                                    @elseif(auth()->user()->hasRole('factor_response'))
                                        <span
                                            class="label label-success">{{ count(
                                                App\OperationOrderResult::where('confirmed', 0)->whereHas('operationOrder', function ($q) {
                                                        $q->where('out_operation', 0)->where('supervisor_process', auth()->user()->id);
                                                    })->get(),
                                            ) }}</span>
                                    @elseif(auth()->user()->hasRole('store_factor_response'))
                                        <span
                                            class="label label-success">{{ count(
                                                App\OperationOrderResult::where('store_confirm', 0)->whereHas('operationOrder', function ($q) {
                                                        $q->where('out_operation', 0)->where('supervisor_store', auth()->user()->id);
                                                    })->get(),
                                            ) }}</span>
                                    @else
                                        <span
                                            class="label label-success">{{ count(
                                                App\OperationOrderResult::toUser()->where('confirmed', 0)->whereHas('operationOrder', function ($q) {
                                                        $q->where('out_operation', 0);
                                                    })->get(),
                                            ) }}</span>
                                    @endif

                                </a>
                            </li>
                        @endif
          <!--Operatoin order notifications-->
        
                         <li data-toggle="tooltip" data-placement="bottom" title="أوامر التشغيل الخارجي"
                            class="dropdown">
                            <a href="{{ route('dashboard.operation_orders.index_out') }}?type=not_all">
                                <i class="fa fa-truck"></i>
                                <span class="label label-success" id="outOperationOrdersNotify">
                                  {{-- the response here --}}
                                </span>
                            </a>
                        </li>

                        <li data-toggle="tooltip" data-placement="bottom" title="أوامر التشغيل الداخلي"
                            class="dropdown">
                            <a href="{{ route('dashboard.operation_orders.index') }}?type=not_all">
                                <i class="fa fa-truck"></i>
                                <span class="label label-success" id="inOperationOrdersNotify">
                                  {{-- the response here --}}
                                </span>
                            </a>
                        </li>          
          
          <!-- Tasks Menu -->
          <li class="dropdown tasks-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-flag-o"></i>
              <span class="label label-danger"></span>
            </a>
            <ul class="dropdown-menu">
              <li>
                <!-- Inner menu: contains the langs -->
                <ul class="menu">
                    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                        <li>
                            <a rel="alternate" hreflang="{{ $localeCode }}" href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                                {{ $properties['native'] }}
                            </a>
                        </li>
                    @endforeach
                  <!-- end task item -->
                </ul>
              </li>

            </ul>
          </li>
          <!-- User Account Menu -->
          <li class="dropdown user user-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" style='background-color: #33b35a !important;'>
              <!-- The user image in the navbar-->
              <img src="{{ asset('dashboard/img/avatar5.png') }}" class="user-image" alt="User Image">
              <!-- hidden-xs hides the username on small devices so only the image appears. -->
              <span class="hidden-xs">{{ auth()->user()->name }} {{--auth()->user()->last_name--}}</span>
            </a>
            <ul class="dropdown-menu">
              <!-- The user image in the menu -->
              <li class="user-header">
                <img src="{{ asset('dashboard/img/avatar5.png') }} " class="img-circle" alt="User Image">

                <p>
                  {{ auth()->user()->name }} {{--auth()->user()->last_name--}}
                  {{--<small>Member since Nov. 2012</small>--}}
                </p>
              </li>
              <!-- Menu Body -->

              <!-- Menu Footer-->
              <li class="user-footer">


                <a href="{{ route('logout') }}" class="btn btn-default btn-flat" onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();">@lang('site.logout')</a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>

            </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
         </ul>
      </div>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->

  <!-- /.content-wrapper -->
    @include('layouts.dashboard._aside')
    @yield('content')
    @include('partials._session')

  <!-- Main Footer -->
  {{--<footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">

    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; 2016 <a href="#">Company</a>.</strong> All rights reserved.
  </footer>--}}

  <!-- Control Sidebar -->

</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 3 -->


<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. -->

      <!-- Bootstrap 3.3.7 -->
      <script src="{{ asset('dashboard/js/bootstrap.min.js')}}"></script>
      <script src="{{ asset('dashboard/js/bootstrap-switch.min.js')}}"></script>
      <script src="{{ asset('dashboard/js/jquery.min.js')}}"></script>
      {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script> --}}

      {{-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script> --}}
      <script src="{{ asset('dashboard/js/bootstrap.bundle.min.js')}}"></script>
      <script src="{{ asset('dashboard/datatable/jquery.dataTables.min.js')}}"></script>
            <script src="{{ asset('dashboard/datatable/dataTables.bootstrap4.min.js')}}"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.18/pdfmake.min.js"></script>
        <script type="text/javascript" src="{{ asset('dashboard/datatable/vfs_fonts.js')}}"></script>
        <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.print.js"></script>
        <script src="{{ asset('dashboard/datatable/jszip.min.js')}}"></script>
      {{-- <script src="{{ asset('dashboard/datatable/jquery.dataTables.min_1.js')}}"></script> --}}

       <!-- AdminLTE App -->
      <script src="{{ asset('dashboard/js/adminlte.min.js')}}"></script>
      <script src="{{ asset('dashboard/js/fastclick.js')}}"></script>
       {{--icheck--}}
      <script src="{{ asset('dashboard/plugins/icheck/icheck.min.js') }}"></script>
      <script src="{{ asset('dashboard/plugins/ckeditor/ckeditor.js') }}"></script>
        {{--jquery number--}}
      <script src="{{ asset('dashboard/js/jquery.number.min.js') }}"></script>

      {{--print this--}}
      <script src="{{ asset('dashboard/js/printThis.js') }}"></script>

      {{--custom--}}
      <script src="{{ asset('dashboard/js/custom/order.js')}}"></script>
      <script src="{{ asset('dashboard/js/custom/image_preview.js')}}"></script>

      <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>

    {{--morris --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="{{ asset('dashboard/plugins/morris/morris.min.js') }}"></script>
    <!--<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>-->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="{{ asset('js/iziToast.js') }}"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.bootstrap.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>
    <script src="{{ url('/js/formajax.js') }}"></script>
    @include('vendor.lara-izitoast.toast')

@yield('scripts')

<script>


    $(document).ready(function () {

        $('.sidebar-menu').tree();

        $('#table').DataTable({
            "pageLength": 5,
            "sorting": [0, 'DESC'],
        });

        //icheck
        $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });

        //delete
        $(document).on('click', '.delete', function(e){
            e.preventDefault();

            var nthis = $(this)
            var n = new Noty({
                text: "@lang('site.confirm_delete')" + nthis[0].name,
                type: "warning",
                killer: true,
                buttons: [
                    Noty.button("@lang('site.yes')", 'btn btn-danger mr-2', function () {
                        nthis.closest('form').submit();
                    }) ,

                     Noty.button("@lang('site.no')", 'btn btn-primary mr-2', function () {
                        n.close();
                    })
                ]
            });
            n.show();
        });//end of delete
        
        //edit
        $(document).on('click', '.editForm', function(e){
            e.preventDefault();
            var nthis = $(this)
            var n = new Noty({
                text: "هل انت متأكد من تعديل" + nthis[0].name,
                type: "warning",
                killer: true,
                buttons: [
                    Noty.button("@lang('site.yes')", 'btn btn-success mr-2', function () {
                        nthis.closest('form').submit();
                    }) ,

                     Noty.button("@lang('site.no')", 'btn btn-primary mr-2', function () {
                        n.close();
                    })
                ]
            });
            n.show();
        });//end of edit


        CKEDITOR.config.language = "{{ app()->getLocale() }}";//to change the direction of textearea of description by change the language


    });//end of ready


</script>
<script>
    Pusher.logToConsole = true;
      var pusher = new Pusher('e75d58425f4b10f93cfb', {
          cluster: 'eu',
          forceTLS: true
      });
      var audio = new Audio('{{url('audio/')}}'+'/notification.mp3');
      var channel = pusher.subscribe('my-channel');
      channel.bind('my-event', function (data) {
          if ('{{Auth()->user()->id}}' == data.message.user_id) {
              audio.play();
              swal("يوجد عمليه جديدة ");
                //   .then(function () {
                //      window.location=data.message.url;
                //   });

          }
      });
    $(document).ready(function() {

        $('.select2-js').select2();
    });

</script>

<script>
  const apiEndpoint1 = "{{route('inOperationOrdersNotify')}}";

  fetch(apiEndpoint1)
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok ' + response.statusText);
      }
      return response.json();
    })
    .then(data => {
      console.log(data);
      document.getElementById('inOperationOrdersNotify').textContent = JSON.stringify(data);
    })
    .catch(error => {
      console.error('There has been a problem with your fetch operation:', error);
    });
</script>

<script>
  const apiEndpoint2 = "{{route('outOperationOrdersNotify')}}";

  fetch(apiEndpoint2)
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok ' + response.statusText);
      }
      return response.json();
    })
    .then(data => {
      console.log(data);
      document.getElementById('outOperationOrdersNotify').textContent = JSON.stringify(data);
    })
    .catch(error => {
      console.error('There has been a problem with your fetch operation:', error);
    });
</script>
</body>
</html>
