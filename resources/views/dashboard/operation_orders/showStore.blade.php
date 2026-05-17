<!DOCTYPE html>
<html dir="{{ LaravelLocalization::getCurrentLocaleDirection() }}">
<header>
  <title> امر شغل رقم ({{$operationOrder->id}})</title>
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
    body,
    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
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
    th {
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

    td {
      border: 1px solid black;
      text-align: center !important;
      vertical-align: middle !important;
    }

    table.dataTable thead .sorting:after {
      opacity: 1;
    }

    table {
      width: 100% !important;
      border-collapse: collapse;
      border-spacing: 0;
    }

    table.dataTable {
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

<body onload="window.print()">

  <div class="header">
    <div class="row">
      <div class="col-xs-4">
        @if ($operationOrder->out_operation == 1)
        <h2 class=" text-center">
          أمر تشغيل خارجي - مخزن
        </h2>
        @else
        <h2 class=" text-center">
          أمر تشغيل - مخزن
        </h2>
        @endif

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
          <label class="col-md-2">اسم المستخدم</label>
          <label class="col-md-4">{{optional($operationOrder->user)->name}}</label>
          <label class="col-md-2">رقم أمر التشغيل</label>
          <label class="col-md-4">{{{$operationOrder->id}}}</label>
          <label class="col-md-2">التاريخ</label>
          <label class="col-md-4">{{{$operationOrder->created_at}}}</label>
        </div>
      </div>
      <div class="box-body">
        <table class="table table-responsive table-bordered">
          <thead>
            <tr>
              <td>الآلة</td>
              @if ($operationOrder->out_operation == 1)
              <td>اسم العميل</td>
              @endif
              <!--<td>المشرف</td>-->
              @if ($operationOrder->related_operat_ord_id)
              <td>أمر شغل سابق</td>
              @endif
              {{-- <td>الموظفين</td> --}}
              <!--<td>المستخدمين</td>-->
              @if ($operationOrder->item_id)
              <td>الخامة</td>
              <td> كمية الخامة المستخدمة بالوزن</td>
              <td>مستلزمات التشغيل</td>

              <td>مجموعة الناتج</td>
              <td>الناتج</td>
              <td>الطول</td>
              <td>العرض</td>
              <td>الكمية</td>
              @endif
              @if ($operationOrder->out_operation == 1)
              <td>اجمالي الطول المستخدم</td>
              @endif
            </tr>
          </thead>
          <tbody>
            <?php
                        $empIds = explode(',', $operationOrder->employee_id);
                        $employeesNames = App\Employee::whereIn('id', $empIds)->pluck('name')->toArray();
                        $userIds = explode(',', $operationOrder->user_id);
                        $usersNames = App\User::whereIn('id', $userIds)->pluck('name')->toArray();
                        $operationSupliesIds = explode(',', $operationOrder->operation_suplies_id);
                        $operationSupliesNames = App\Supplies::whereIn('id', $operationSupliesIds)->pluck('name')->toArray();

                        $itemName = App\Item::where('id',$operationOrder->out_item_id )->first();
                    ?>
                 
            <tr>
              <td>{{optional($operationOrder->machine)->name}}</td>
              @if ($operationOrder->out_operation == 1)
              <td>{{$operationOrder->client_name}}</td>
              @endif
              @if ($operationOrder->related_operat_ord_id)
              <td>{{ $operationOrder->related_operat_ord_id}}</td>
              @endif
              <!--<td>{{optional($operationOrder->supervisor)->name}}</td>-->
              {{-- <td>{{implode(' , ',$employeesNames)}}</td> --}}
              <!--<td>{{implode(' , ',$usersNames)}}</td>-->
              @if ($operationOrder->item_id)
              <td>{{optional($operationOrder->item)->name}}</td>
              <td>{{$operationOrder->old_item_quantity}}</td>
              <td>{{implode(' , ',$operationSupliesNames)}}</td>

              <td>{{optional($operationOrder->group)->name}}</td>
              <td>{{optional($itemName)->name}}</td>
              <td>{{$operationOrder->length}}</td>
              <td>{{$operationOrder->width}}</td>
              <td>{{$operationOrder->quantity}}</td>
              @endif
              @if ($operationOrder->out_operation == 1)
              <td>{{$operationOrder->total_used_length}}</td>
              @endif

            </tr>
          </tbody>
        </table>
        <br>
        @if(!$operationOrder->item_id)
        <table class="table table-responsive table-bordered">
            <thead>
                <tr>
                    <td>#</td>
                    <td>الخامة المستخدمة</td>
                    @if ($operationOrder->out_operation == 0)
                    <td>رصيد الخامة المستخدمة قبل التشغيل</td>
                    @endif
                    <td>كمية الخامة المستخدمة</td>
                    @if ($operationOrder->out_operation == 1)
                    <td>الوحدة</td>
                    @endif
                    <td>مستلزمات التشغيل</td>
                    <td>كيمه المستلزم المستخدمه فى امر التشغيل</td>
                    <td>كميه المستلزم قبل امر التشغيل</td>
                    <td>المتبقى من المستلزم الان</td>
                    @if ($operationOrder->out_operation == 0)
                    <td>صنف سابق</td>
                    <td>رصيد الخامةالناتجة قبل التشغيل</td>
                    @endif
                    @if ($operationOrder->out_operation == 1)
                    <td>الخامةالناتجة</td>
                    @endif
                    <td>الطول</td>
                    <td>العرض</td>
                    <td>الكمية</td>
                    @if ($operationOrder->out_operation == 1)
                    <td>الوحدة</td>
                    @endif
                </tr>
            </thead>
            <tbody>
                <?php
                    $operationOrderDetails = App\OperationOrderDetail::where('operation_order_id', $operationOrder->id)->get();
                    $groupedDetails = $operationOrderDetails->groupBy('id'); // تجميع التفاصيل حسب الـ id
                ?>
                @foreach ($groupedDetails as $id => $details)
                    <?php
                        $firstDetail = $details->first();
                        $rowspan = $details->count(); // عدد الصفوف التي سيتم دمجها
                    ?>
                    <tr>
                        <td rowspan="{{ $rowspan }}">{{ $id }}</td>
                        @if ($operationOrder->out_operation == 0)
                        <td rowspan="{{ $rowspan }}">{{ optional($firstDetail->item)->name }}</td>
                        @else
                        <td rowspan="{{ $rowspan }}">{{ optional($firstDetail)->item_name }}</td>
                        @endif
                        @if ($operationOrder->out_operation == 0)
                        <td rowspan="{{ $rowspan }}">{{ $firstDetail->old_in_balance }}</td>
                        @endif
                        <td rowspan="{{ $rowspan }}">{{ $firstDetail->old_item_quantity }}</td>
                        @if ($operationOrder->out_operation == 1)
                        <td rowspan="{{ $rowspan }}">{{ $operationOrder->old_item_unit }}</td>
                        @endif
    
                        <?php
                            $operationDetailSupliesIds = explode(',', $firstDetail->operation_suplies_id);
                            $operationDetailSupliesPreUsed = explode(',', $firstDetail->supplie_quantity_pre_used);
                            $operationDetailSupliesNames = [];
                            $operationSupliesUsed = [];
                            $machine_id = optional($operationOrder->machine)->id;
                            foreach ($operationDetailSupliesIds as $ids) {
                                $operationDetailSupliesNames[] = App\Supplies::where('id', $ids)->first()->name ?? '';
                                $operationSupliesUsed[] = App\MachineSupplie::select('used')
                                    ->where('machine_id', $machine_id)
                                    ->where('supplie_id', $ids)->first()->used ?? '';
                            }
                            $itemName = App\Item::where('id', $firstDetail->out_item_id)->first();
                        ?>
    
                        <td>
                            @foreach ($operationDetailSupliesNames as $name)
                                {{ $name }}<br>
                            @endforeach
                        </td>
                        <td>
                            @foreach (explode(',', $firstDetail->supplie_quantity_used) as $used)
                                {{ $used }}<br>
                            @endforeach
                        </td>
                        <td>
                            @foreach ($operationDetailSupliesPreUsed as $preUsed)
                                {{ $preUsed }}<br>
                            @endforeach
                        </td>
                        <td>
                            @foreach ($operationSupliesUsed as $used)
                                {{ $used }}<br>
                            @endforeach
                        </td>
                        @if ($operationOrder->out_operation == 0)
                        <td rowspan="{{ $rowspan }}">{{ optional($itemName)->name }}</td>
                        <td rowspan="{{ $rowspan }}">{{ $firstDetail->old_out_balance }}</td>
                        @endif
                        @if ($operationOrder->out_operation == 1)
                        <td rowspan="{{ $rowspan }}">{{ $firstDetail->out_item_name }}</td>
                        @endif
                        <td rowspan="{{ $rowspan }}">{{ $firstDetail->length }}</td>
                        <td rowspan="{{ $rowspan }}">{{ $firstDetail->width }}</td>
                        <td rowspan="{{ $rowspan }}">{{ $firstDetail->quantity }}</td>
                        @if ($operationOrder->out_operation == 1)
                        <td rowspan="{{ $rowspan }}">{{ $operationOrder->out_item_unit }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
        @endif
        <br>
        <table class="table table-responsive table-bordered">
          <thead>
            <tr>
              <td>اسماء العاملين على أمر التشغيل</td>
            </tr>
          </thead>
          <tbody>
            <?php
              $storeEmployeesIds = explode(',', $operationOrder->store_employees);
              $storeEmployeesNames = App\Employee::whereIn('id', $storeEmployeesIds)->pluck('name')->toArray();
            ?>
            <tr>
              <td>{{implode(' , ',$storeEmployeesNames)}}</td>
            </tr>
          </tbody>
        </table>

        <div class="clearfix"></div>
        <br>
        <div class="row">
        <div class="col-xs-12">
            مسئول الأمر : {{App\User::where('id', $operationOrder->created_by)->first()->name ?? '-'}}
        </div>
          <div class="col-xs-12">
            {!! $operationOrder->notes2 !!}
          </div>
        </div>

      </div>
    </div>
  </div>
  <footer style="position: fixed;
    bottom: 0;
    width: 100%;">
    <div class="row">
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-center" style="width: 50%!important">
        <b>السبتية / 01025009288</b>
        <br>
      </div>
      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-center" style="width: 50%!important">
        <b>قليوب / 01095797888</b>
        <br>
        <b>٦ أكتوبر / 01007180405</b>
      </div>
    </div>

    <br>

    <div class="row text-center">
      <div class="col-xs-2"></div>
      <div class="col-xs-8 text-center">
        www.elradwansteel.com E-mail:radwan304@yahoo.com
      </div>
      <div class="col-xs-2"></div>
    </div>
  </footer>


  <!-- Bootstrap 3.3.7 -->
  <script src="{{ asset('dashboard/js/bootstrap.min.js')}}"></script>
  <script src="{{ asset('dashboard/js/bootstrap-switch.min.js')}}"></script>
  <script src="{{ asset('dashboard/js/jquery.min.js')}}"></script>
  {{--
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script> --}}

  {{--
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
    crossorigin="anonymous"></script> --}}
  <script src="{{ asset('dashboard/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{ asset('dashboard/datatable/jquery.dataTables.min.js')}}"></script>
  <script src="{{ asset('dashboard/datatable/dataTables.bootstrap4.min.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.18/pdfmake.min.js"></script>
  <script type="text/javascript" src="{{ asset('dashboard/datatable/vfs_fonts.js')}}"></script>
  <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.print.js"></script>
  <script src="{{ asset('dashboard/datatable/jszip.min.js')}}"></script>
  {{--
  <script src="{{ asset('dashboard/datatable/jquery.dataTables.min_1.js')}}"></script> --}}

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