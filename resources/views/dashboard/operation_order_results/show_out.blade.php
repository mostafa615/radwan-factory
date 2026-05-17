{{-- @extends('layouts.dashboard.app') --}}
<!DOCTYPE html>
<html dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">
<header>
    <title> ناتج أمر شغل رقم ({{$operationOrderResult->operationOrder->id}})</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="shortcut icon" href="{{ asset('dashboard/images/login/logo.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('dashboard/datatable/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard/css/bootstrap-switch.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard/css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard/css/skin-blue.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dashboard/datatable/dataTables.bootstrap.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('css/iziToast.css') }}" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.6.4/css/buttons.dataTables.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
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
    </style>
    <script src="{{ asset('dashboard/js/jquery.min.js')}}"></script>

    <link rel="stylesheet" href=" {{ asset('dashboard/plugins/noty/noty.css')}}">
    <script src="{{ asset('dashboard/plugins/noty/noty.min.js')}}"></script>

    <link rel="stylesheet" href="{{ asset('dashboard/plugins/icheck/all.css')}}">

    {{--morris--}}
    <link rel="stylesheet" href="{{ asset('dashboard/plugins/morris/morris.css') }}">

    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>

</header>
{{-- @section('content') --}}

<body onload="window.print()" >

<div class="header">
    <div class="row">
        <div class="col-xs-4">
            <h2 class=" text-center">
                 ناتج تشغيل خارجي
            </h2>
        </div>
        <div class="col-xs-4"></div>
        <div class="col-xs-4">
            <img src="{{url('dashboard/images/rsteel_logo.jpeg')}}" style="width: 80px;height: 60px">
        </div>
    </div>
</div>
<div class="">

</div>
<div class="content">

    <div class="box box-primary">
        <div class="box-header">
            <div class="row">
                <label class="col-md-2">المستخدم</label>
                <label class="col-md-4">{{optional($operationOrderResult->operationOrder->user)->name}}</label>
                <!--<label class="col-md-2">المشرف</label>-->
                <!--<label class="col-md-4">{{optional($operationOrderResult->operationOrder->supervisor)->name}}</label>-->
                <label class="col-md-2">رقم أمر التشغيل</label>
                <label class="col-md-4">{{ $operationOrderResult->operationOrder->id }} -> {{$operationOrderResult->order_details_id}}</label>
                <label class="col-md-2">التاريخ</label>
                <label class="col-md-4">{{ $operationOrderResult->created_at }}</label>
            </div>
        </div>
        <div class="box-body">
            <table class="table table-responsive table-bordered">
                <thead>
                    <tr>
                        <td>الآلة</td>
                        <td>الموظفين</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $empIds = explode(',', $operationOrderResult->employee_id);
                        $employeesNames = App\Employee::whereIn('id', $empIds)->pluck('name')->toArray();

                        // dd($itemName);
                    ?>
                    <tr>
                        <td>{{optional($operationOrderResult->operationOrder->machine)->name}}</td>
                        <td>{{implode(' , ',$employeesNames)}}</td>
                    </tr>
                </tbody>
            </table>
            <br>
            <table class="table table-responsive table-bordered">
                <thead>
                    <tr>
                        <!--<td>المستخدمين</td>-->
                        <td>الخامة المستخدمة</td>
                        <td>ناتج الخامة المستخدمة</td>
                        @if (optional($operationOrderResult->operationOrder)->out_operation == 1)
                        <td>الوحدة</td>
                        @endif
                        <td>الخامة الناتجة</td>
                        {{-- <td>اسم الناتج</td> --}}
                        <td>الناتج الفعلي</td>
                        @if (optional($operationOrderResult->operationOrder)->out_operation == 1)
                        <td>الوحدة</td>
                        @endif
                        <td>اجمالي الطول المستخدم</td>
                        {{-- <td>التالف</td> --}}
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $empIds = explode(',', $operationOrderResult->operationOrder->employee_id);
                        $employeesNames = App\Employee::whereIn('id', $empIds)->pluck('name')->toArray();
                        $userIds = explode(',', $operationOrderResult->operationOrder->user_id);
                        $usersNames = App\User::whereIn('id', $userIds)->pluck('name')->toArray();

                        $operationOrderDetail = App\OperationOrderDetail::where('id', $operationOrderResult->order_details_id)->where('operation_order_id', $operationOrderResult->operation_order_id)->first();
                        $operationSupliesIds = explode(',', $operationOrderDetail->operation_suplies_id);
                        $operationSupliesNames = App\Supplies::whereIn('id', $operationSupliesIds)->pluck('name')->toArray();
                        $itemName = App\Item::where('id',$operationOrderDetail->out_item_id )->first();
                        // dd($itemName);
                    ?>
                    <tr>
                        <!--<td>{{implode(' , ',$usersNames)}}</td>-->
                        <td>{{optional($operationOrderDetail)->item_name}}</td>
                        <td>{{optional($operationOrderResult)->old_item_quantity}}</td>
                        @if (optional($operationOrderResult->operationOrder)->out_operation == 1)
                        <td>{{optional($operationOrderResult->operationOrder)->old_item_unit}}</td>
                        @endif
                        <td>{{optional($operationOrderDetail)->out_item_name}}</td>
                        {{-- <td>{{$operationOrderDetail->out_name}}</td> --}}
                        <td>{{optional($operationOrderResult)->actual_output}}</td>
                        @if (optional($operationOrderResult->operationOrder)->out_operation == 1)
                        <td>{{optional($operationOrderResult->operationOrder)->out_item_unit}}</td>
                        @endif
                        {{-- <td>{{optional($operationOrderResult)->damage}}</td> --}}
                        <td>{{ optional($operationOrderResult)->total_used_length }}</td>
                        {{-- <td>{{ $quantity }}</td>--}}
                    </tr>
                </tbody>
            </table>
            <br>
            @if ($operationOrderResult->confirm_notes)
                <p>ملحوظة المسؤول : {{ optional($operationOrderResult)->confirm_notes}}</p>
            @endif
            <br>
            <table class="table table-responsive table-bordered">
                <thead>
                    <tr>
                        <td>نوع التالف</td>
                        <td>إسم التالف</td>
                        <td>عدد التالف</td>
                        <td>الطول</td>
                        <td>العرض</td>
                        <td>السمك</td>
                        <td>الوزن</td>
                    </tr>
                </thead>
                <tbody>
                    @php

                        $OrderResultDetails = App\OperationOrderResultDetail::where('order_results_id',$operationOrderResult->id)->get();
                    @endphp
                    @foreach ($OrderResultDetails as $OrderResultDetail)
                    <tr>
                        <td>{{$OrderResultDetail->damage_type}}</td>
                        <td>{{$OrderResultDetail->damage_name}}</td>
                        <td>{{$OrderResultDetail->damage_quantity}}</td>
                        <td>{{$OrderResultDetail->damage_length}}</td>
                        <td>{{$OrderResultDetail->damage_width}}</td>
                        <td>{{$OrderResultDetail->damage_thickness}}</td>
                        <td>{{$OrderResultDetail->damage_weight}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <br>

            <table class="table table-responsive table-bordered">
                <thead>
                    <tr>
                        <td>المستلزم</td>
                        <td>الكميه المستخدمه</td>
                        <td>الكميه الباقيه</td>
                    </tr>
                </thead>
                <tbody>
                    @php

                        $OrderResultDetails = App\OperationOrderResultDetail::where('order_results_id',$operationOrderResult->id)->get();
                    @endphp
                    @foreach ($supplies as $index=>$supply)
                    <tr>
                        <td>{{$supply}}</td>
                        <td>{{optional($operationOrderResult)->total_used_length}}</td>
                        <td>{{$used[$index]??0}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="clearfix"></div>
            <br>
            <div class="row">
                <div class="col-xs-4">
                        العميل : {{$operationOrderResult->operationOrder->client_name??'-----------' }}
                </div>
                <div class="col-xs-4">

                </div>
                <div class="col-xs-4">
                   ملاحظات : {!! $operationOrderResult->notes ?? '-----------' !!}
                </div>
            </div>

        </div>
    </div>
</div>
<footer style="position: fixed;
    bottom: 0;
    width: 100%;">
    <div class="row" >
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-center" style="width: 50%!important" >
            <b>السبتية / 01025009288</b>
            <br>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-center" style="width: 50%!important" >
            <b>قليوب / 01095797888</b>
            <br>
            <b>٦ أكتوبر / 01007180405</b>
        </div>
    </div>

    <br>

    <div class="row text-center">
        <div class="col-xs-2"></div>
        <div class="col-xs-8 text-center">
            www.elradwansteel.com  E-mail:radwan304@yahoo.com
        </div>
        <div class="col-xs-2"></div>
    </div>
</footer>
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script src="{{ asset('js/iziToast.js') }}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.bootstrap.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>
<script src="{{ url('/js/formajax.js') }}"></script>

</body>
</html>
{{-- @endsection --}}
