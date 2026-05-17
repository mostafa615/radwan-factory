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
                <h1>تقرير الألة</h1>
                <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                    <li class="active">تقرير الألة</li>
                </ol>
            </section>
            <section class="content" style='padding-bottom: 0px !important;'>

                <div class="box box-primary">

                    <div class="box-header with-border">
                        <h3 class="box-title" style="margin-bottom: 15px">تقرير الألة</h3>
                    </div>

                    <div class="box-body" style="min-height: 250px">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="nav-tabs-custom">
                                    <ul class="nav nav-tabs">
                                        <li class="active">
                                            <a href="#operation_orders" data-toggle="tab" aria-expanded="false">أوامر التشغيل</a>
                                        </li>
                                        <li>
                                            <a href="#operation_order_details" data-toggle="tab" aria-expanded="false">الخامات المستخدمة في أمر التشغيل</a>
                                        </li>
                                        <li>
                                            <a href="#operation_order_results" data-toggle="tab" aria-expanded="false">الخامات الناتجة من أمر التشغيل</a>
                                        </li>
                                        <li>
                                            <a href="#operation_order_supplies" data-toggle="tab" aria-expanded="false">المستلزمات المستخدمة</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="operation_orders">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <td>أمر التشغيل</td>
                                                            <td> الألة</td>
                                                            <td>التاريخ</td>
                                                            <td>ملاحظات</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($operation_orders as $operation_order)
                                                        <tr>
                                                            <td><a target="_blank" href="{{route('dashboard.operation_orders.show',$operation_order->id)}}">{{$operation_order->id}}</a></td>
                                                            <td>{{$operation_order->machine->name}}</td>
                                                            <td>{{$operation_order->date}}</td>
                                                            <td>{{$operation_order->notes}}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="operation_order_details">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <td>أمر التشغيل</td>
                                                            <td> الألة</td>
                                                            <td>التاريخ</td>
                                                            <td>الخامة المستخدمة</td>
                                                            <td>كمية الخامة المستخدمة</td>
                                                            <td>ملاحظات</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($operation_orders as $operation_order)
                                                        @foreach($operation_order->operationOrderDetails as $operationOrderDetail)
                                                        <tr>
                                                            <td><a target="_blank" href="{{route('dashboard.operation_orders.show',$operation_order->id)}}">{{$operation_order->id}}</a></td>
                                                            <td>{{$operation_order->machine->name}}</td>
                                                            <td>{{$operation_order->date}}</td>
                                                            <td>{{optional($operationOrderDetail->item)->name}}</td>
                                                            <td>{{$operationOrderDetail->old_item_quantity}}</td>
                                                            <td>{{$operation_order->notes}}</td>
                                                        </tr>
                                                        @endforeach
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="operation_order_results">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <td>أمر التشغيل</td>
                                                            <td> الألة</td>
                                                            <td>التاريخ</td>
                                                            <td>الخامة الناتجة</td>
                                                            <td>كمية الخامة الناتجة</td>
                                                            <td>وزن الخامة الناتجة</td>
                                                            <td>عرض الناتج</td>
                                                            <td>ملاحظات</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($operation_orders as $operation_order)
                                                        @foreach($operation_order->operationOrderDetails as $operationOrderDetail)
                                                        @foreach($operationOrderDetail->operationOrderResults as $operationOrderResult)
                                                        @php
                                                            $item = App\Item::where('id', $operationOrderDetail->out_item_id)->first();
                                                        @endphp
                                                        <tr>
                                                            <td><a target="_blank" href="{{route('dashboard.operation_orders.show',$operation_order->id)}}">{{$operation_order->id}}</a></td>
                                                            <td>{{$operation_order->machine->name}}</td>
                                                            <td>{{$operation_order->date}}</td>
                                                            <td>{{optional($item)->name}}</td>
                                                            <td>{{$operationOrderResult->actual_output}}</td>
                                                            <td>{{$operationOrderResult->weight}}</td>
                                                            <td><a style="text-decoration: none" target="_blank" class="btn btn-info" href="{{route('dashboard.operation_order_results.show',$operationOrderResult->id)}}" >عرض الناتج</a></td>
                                                            <td>{{$operation_order->notes}}</td>
                                                        </tr>
                                                        @endforeach
                                                        @endforeach
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="operation_order_supplies">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <td>أمر التشغيل</td>
                                                            <td> الألة</td>
                                                            <td>التاريخ</td>
                                                            <td>المستلزمات</td>
                                                            <td>ملاحظات</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($operation_orders as $operation_order)
                                                        @foreach($operation_order->operationOrderDetails as $operationOrderDetail)
                                                        <?php
                                                            $operationDetailSupliesIds = explode(',', $operationOrderDetail->operation_suplies_id);
                                                            $operationDetailSupliesNames = App\Supplies::whereIn('id', $operationDetailSupliesIds)->pluck('name')->toArray();
                                                        ?>
                                                        <tr>
                                                            <td><a target="_blank" href="{{route('dashboard.operation_orders.show',$operation_order->id)}}">{{$operation_order->id}}</a></td>
                                                            <td>{{$operation_order->machine->name}}</td>
                                                            <td>{{$operation_order->date}}</td>
                                                            <td>{{implode(' , ',$operationDetailSupliesNames)}}</td>
                                                            <td>{{$operation_order->notes}}</td>
                                                        </tr>
                                                        @endforeach
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
