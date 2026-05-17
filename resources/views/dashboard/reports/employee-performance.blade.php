@extends('layouts.dashboard.app')
@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>تقرير انتاجية الموظف</h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
      <li class="active">تقرير انتاجية الموظف</li>
    </ol>
  </section>

  <section class="content" style='padding-bottom: 0px !important;'>
    <div class="box box-primary">
      <div class="box-header">
        <div class="table-responsive text-center">
          <table class="table table-bordered">
            <thead class="text-bold">
              <tr>
                <td>اسم الموظف</td>
                <td>من تاريخ</td>
                <td>الى تاريخ</td>
              </tr>
              </thead>
              <tbody>
                <tr>
                  <td>{{$user->name}}</td>
                  <td>{{\Carbon\Carbon::parse(request()->date_from)->format('Y-m-d')}}</td>
                  <td>{{\Carbon\Carbon::parse(request()->date_to)->format('Y-m-d')}}</td>
                </tr>
              </tbody>
          </table>
        </div>
      </div>

      <div class="box-body">
        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs">
            <li class="active">
              <a href="#mainTab" data-toggle="tab" aria-expanded="true">كشف انتاجية الموظف</a>
            </li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="mainTab">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead class="text-bold">
                    <tr>
                      <td>التاريخ</td>
                      <td>ناتج امر التشغيل</td>
                      <td>الماكينة</td>
                      <td>اجمالي الطول المستخدم</td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $totalAmount = 0; ?>
                    @foreach($operationOrderResults as $operationOrderResult)
                      <?php
                        $totalAmount += $operationOrderResult->total_used_length;
                      ?>
                      <tr>
                        <td>{{$operationOrderResult->operationOrder->date}}</td>
                        <td>
                          @if($operationOrderResult->operationOrder->out_operation == 1)
                            <a href="{{route('dashboard.operation_order_results.show_out', $operationOrderResult->id)}}" title="print" target="_blank">
                              {{$operationOrderResult->id}} - خارجي
                            </a>
                          @else
                            <a href="{{route('dashboard.operation_order_results.show', $operationOrderResult->id)}}" title="print" target="_blank">
                              {{$operationOrderResult->id}} - داخلي
                            </a>
                          @endif
                        </td>
                        <td>{{App\Machines::where('id', $operationOrderResult->operationOrder->machine_id)->first()->name ?? '-'}}</td>
                        <td>{{$operationOrderResult->total_used_length ?? '-'}}</td>
                      </tr>
                    @endforeach
                    <tr class="text-bold">
                      <td>الاجمالي</td>
                      <td></td>
                      <td></td>
                      <td class="balance-col">{{$totalAmount}}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection