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
                <h1>تقرير ناتج امر التشغيل</h1>
                <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                    <li class="active">تقرير ناتج امر التشغيل</li>
                </ol>
            </section>
            <section class="content" style='padding-bottom: 0px !important;'>

                <div class="box box-primary">

                    <div class="box-header with-border">
                        <h3 class="box-title" style="margin-bottom: 15px">تقرير ناتج امر التشغيل</h3>
                    </div>

                    <div class="box-body" style="min-height: 250px">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="nav-tabs-custom">
                                    <ul class="nav nav-tabs">
                                        <li class="active">
                                            <a href="#operationOrderResult" data-toggle="tab" aria-expanded="false">ناتج امر التشغيل</a>
                                        </li>

                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="operationOrderResult">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <td>امر التشغيل</td>
                                                            <td>المشرف</td>
                                                            <td>التاريخ</td>
                                                            <td>الخامات المستخدمة</td>
                                                            <td>مستلزمات التشغيل</td>
                                                            <td>ملحوظة التأكيد</td>
                                                            <td>الناتج الفعلي</td>
                                                            <td>الوزن</td>
                                                            <td>اجمالي الطول المستخدم</td>
                                                            <td>وزن التالف</td>
                                                            <td>ملاحظات</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($operationOrderResults as $operationOrderResult)
                                                        @php
                                                            $operationOrderDetail = App\OperationOrderDetail::where('id', $operationOrderResult->order_details_id)
                                                                                                            ->where('operation_order_id', $operationOrderResult->operation_order_id)
                                                                                                            ->first();
                                                            $operationSupliesIds = explode(',', $operationOrderDetail->operation_suplies_id);
                                                            $operationSupliesNames = App\Supplies::whereIn('id', $operationSupliesIds)->pluck('name')->toArray();
                                                            $itemName = App\Item::where('id',$operationOrderDetail->item_id )->first();

                                                            $OrderResultDetails = App\OperationOrderResultDetail::where('order_results_id',$operationOrderResult->id)
                                                                                                                ->sum('damage_weight');
                                                        @endphp
                                                        <tr>
                                                            <td><a target="_blank" href="{{route('dashboard.operation_orders.show',$operationOrderResult->operation_order_id)}}">{{$operationOrderResult->operation_order_id}}</a></td>
                                                            <td>{{optional(optional($operationOrderResult->operationOrder)->supervisor)->name}}</td>
                                                            <td>{{optional($operationOrderResult->operationOrder)->date}}</td>
                                                            <td>{{optional($itemName)->name}}</td>
                                                            <td>{{implode(' , ',$operationSupliesNames)}}</td>
                                                            <td>{{$operationOrderResult->confirm_notes}}</td>
                                                            <td>{{$operationOrderResult->actual_output}}</td>
                                                            <td>{{$operationOrderResult->weight}}</td>
                                                            <td>{{$operationOrderResult->total_used_length}}</td>
                                                            <td>{{number_format($OrderResultDetails, 2)}}</td>
                                                            <td>{{$operationOrderResult->notes}}</td>
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
