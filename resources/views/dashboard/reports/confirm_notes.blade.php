@extends('layouts.dashboard.app')
@section('css')
    <style>
    td a{
        color: rgb(48, 136, 136);
        text-decoration: rgb(48, 136, 136);
        font-weight: bold;
        text-decoration: underline;
    }
    </style>
@endsection
@section('content')

    <div class="content-wrapper">
            <section class="content-header">
                <h1>تقرير ملحوظة التأكيد</h1>
                <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                    <li class="active">تقرير ملحوظة التأكيد</li>
                </ol>
            </section>
            <section class="content" style='padding-bottom: 0px !important;'>

                <div class="box box-primary">

                    <div class="box-header with-border">
                        <h3 class="box-title" style="margin-bottom: 15px">تقرير ملحوظة التأكيد</h3>
                    </div>

                    <div class="box-body" style="min-height: 250px">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="nav-tabs-custom">
                                    <ul class="nav nav-tabs">
                                        <li class="active">
                                            <a href="#operationOrderResult" data-toggle="tab" aria-expanded="false">ناتج امر التشغيل</a>
                                        </li>
                                        <li class="">
                                            <a href="#outOperationOrder" data-toggle="tab" aria-expanded="false"> اوامر التشغيل الخارجية</a>
                                        </li>

                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="operationOrderResult">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <td>امر التشغيل</td>
                                                            <td>التاريخ</td>
                                                            <td>ملحوظة التأكيد</td>
                                                            <td>الناتج الفعلي</td>
                                                            <td>الوزن</td>
                                                            <td>اجمالي الطول المستخدم</td>
                                                            <td>ملاحظات</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($operationOrderResults as $operationOrderResult)
                                                        <tr>
                                                            <td><a target="_blank" href="{{route('dashboard.operation_orders.show',$operationOrderResult->operation_order_id)}}">{{$operationOrderResult->operation_order_id}}</a></td>
                                                            <td>{{optional($operationOrderResult->operationOrder)->date}}</td>
                                                            <td>{{$operationOrderResult->confirm_notes}}</td>
                                                            <td>{{$operationOrderResult->actual_output}}</td>
                                                            <td>{{$operationOrderResult->weight}}</td>
                                                            <td>{{$operationOrderResult->total_used_length}}</td>
                                                            <td>{{$operationOrderResult->notes}}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                        <div class="tab-pane" id="outOperationOrder">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <td>امر التشغيل</td>
                                                            <td>التاريخ</td>
                                                            <td>ملحوظة التأكيد</td>
                                                            <td>اجمالي الطول المستخدم</td>
                                                            <td>ملاحظات</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($outOperationOrders as $outOperationOrder)
                                                        <tr>
                                                            <td><a target="_blank" href="{{route('dashboard.operation_orders.show',$outOperationOrder->id)}}">{{$outOperationOrder->id}}</a></td>
                                                            <td>{{optional($outOperationOrder)->date}}</td>
                                                            <td>{{$outOperationOrder->confirm_notes}}</td>
                                                            <td>{{$outOperationOrder->total_used_length}}</td>
                                                            <td>{{$outOperationOrder->notes}}</td>
                                                        </tr>
                                                        @endforeach
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
