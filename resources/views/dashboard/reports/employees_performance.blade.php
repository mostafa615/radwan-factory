@extends('layouts.dashboard.app')
@section('css')
    <style>
    td a{
        color: rgb(48, 136, 136);
        text-decoration: rgb(48, 136, 136);
        font-weight: bold;
        text-decoration: underline;
    }
    @media print {
        #printBtn{
            display: none;
        }
    }
    </style>
@endsection
@section('content')

    <div class="content-wrapper">
            <section class="content-header">
                <h1>تقرير اداء الموظفين والمشرفين</h1>
                <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                    <li class="active">تقرير اداء الموظفين والمشرفين</li>
                </ol>
            </section>
            <section class="content" style='padding-bottom: 0px !important;'>

                <div class="box box-primary">

                    <div class="box-header with-border">
                        @php
                            $machine = App\Machines::where('id', request()->machine_id)->first();
                        @endphp
                        <h3 class="box-title" style="margin-bottom: 15px">تقرير اداء الموظفين والمشرفين</h3>
                        <div class="row">
                            <div class="col-md-3">
                                <h3 style="font-size: 20px">اسم الآلة : {{optional($machine)->name}}</h3>
                            </div>
                            <div class="col-md-5">
                                <h3 style="font-size: 20px">المدة من  : {{\Carbon\Carbon::parse(request()->date_from)->format('Y-m-d')}} الي : {{\Carbon\Carbon::parse(request()->date_to)->format('Y-m-d') }}</h3>
                            </div>
                        </div>
                        <button id="printBtn" class="btn btn-info" onclick="window.print()">طباعة</button>


                    </div>

                    <div class="box-body" style="min-height: 250px">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="nav-tabs-custom">
                                    <ul class="nav nav-tabs">
                                        <li class="active">
                                            <a href="#employees" data-toggle="tab" aria-expanded="false">الموظفين</a>
                                        </li>
                                        <li class="">
                                            <a href="#details" data-toggle="tab" aria-expanded="false">التفصيل</a>
                                        </li>

                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="employees">
                                            <h4 style="text-align: center!important">الموظفين</h4>
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <td>اسم الموظف</td>
                                                            <td>اجمالي الطول المستخدم</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($employees as $employee)
                                                        @php

                                                        @endphp
                                                        @if ($employee->employee_performance>0)
                                                        <tr>
                                                            <td>{{$employee->name}}</td>
                                                            <td>{{$employee->employee_performance}}</td>
                                                        </tr>
                                                        @endif

                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <br>
                                            <h4 style="text-align: center!important">المشرفين</h4>
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <td>اسم المشرف</td>
                                                            <td>اجمالي الطول المستخدم</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($employees as $employee)
                                                        @if ($employee->supervisor_performance>0)
                                                        <tr>
                                                            <td>{{$employee->name}}</td>
                                                            <td>{{$employee->supervisor_performance}}</td>
                                                        </tr>
                                                        @endif

                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                        <div class="tab-pane" id="details">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <td>امر التشغيل</td>
                                                            <td>المشرف</td>
                                                            <td>الموظفين</td>
                                                            <td>التاريخ</td>
                                                            <td>ملحوظة التأكيد</td>
                                                            <td>اجمالي الطول المستخدم</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                            $totalAmount = 0;
                                                        ?>
                                                        @foreach ($operationOrderResults as $operationOrderResult)
                                                        @php
                                                            $operationOrderDetail = App\OperationOrderDetail::where('id', $operationOrderResult->order_details_id)
                                                                                                            ->where('operation_order_id', $operationOrderResult->operation_order_id)
                                                                                                            ->first();

                                                            $empIds = explode(',', $operationOrderResult->operationOrder->employee_id);
                                                            $employeesNames = App\Employee::whereIn('id', $empIds)->pluck('name')->toArray();
                                                        @endphp
                                                        <?php
                                                            $totalAmount += $operationOrderResult->total_used_length;
                                                        ?>
                                                        <tr data-amount="+{{$operationOrderResult->total_used_length}}" class="amount-row" >
                                                            <td>{{$operationOrderResult->operation_order_id}}</td>
                                                            <td>{{optional(optional($operationOrderResult->operationOrder)->supervisor)->name}}</td>
                                                            <td>{{implode(' , ',$employeesNames)}}</td>
                                                            <td>{{optional($operationOrderResult->operationOrder)->date}}</td>
                                                            <td>{{$operationOrderResult->confirm_notes}}</td>
                                                            <td>{{$operationOrderResult->total_used_length}}</td>
                                                        </tr>
                                                        @endforeach
                                                        @foreach ($outOperationOrders as $outOperationOrder)
                                                            @php
                                                                $empIds = explode(',', $outOperationOrder->employee_id);
                                                                $employeesNames = App\Employee::whereIn('id', $empIds)->pluck('name')->toArray();
                                                            @endphp
                                                            <?php
                                                                $totalAmount += $outOperationOrder->total_used_length;
                                                            ?>
                                                        <tr data-amount="+{{$outOperationOrder->total_used_length}}" class="amount-row" >
                                                            <td>{{$outOperationOrder->id}}</td>
                                                            <td>{{optional($outOperationOrder->supervisor)->name}}</td>
                                                            <td>{{implode(' , ',$employeesNames)}}</td>
                                                            <td>{{$outOperationOrder->date}}</td>
                                                            <td>{{$outOperationOrder->confirm_notes}}</td>
                                                            <td>{{$outOperationOrder->total_used_length}}</td>
                                                        </tr>
                                                        @endforeach
                                                        <tr>
                                                            <td>المجموع</td>
                                                            <td></td>
                                                            <td></td>
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
                    </div>
                </div>

            </section>

    </div>



@endsection
@section('scripts')
<script>


    $(function(){
        $('select').select2({
            width: '100%'
        });
    });
    $(document).ready(function(){

    });
</script>
@endsection
