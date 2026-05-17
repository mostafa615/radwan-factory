@extends('layouts.dashboard.app')
@section('css')
    <style>
        td a {
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
            <h1>تقرير المقاسات الخاصة </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">تقرير المقاسات الخاصة </li>
            </ol>
        </section>
        <section class="content" style='padding-bottom: 0px !important;'>

            <div class="box box-primary">

                <div class="box-header with-border">
                    <h3 class="box-title" style="margin-bottom: 15px">تقرير المقاسات الخاصة </h3>
                    <center>
                        <h3 class="box-title" style="margin-bottom: 15px">من {{ $request['date_from'] }}</h3>
                        <h3 class="box-title" style="margin-bottom: 15px">الى {{ $request['date_to'] }}</h3>
                    </center>
                </div>

                <input type="hidden" name="" id="date_from" value="{{ $request->date_from }}">
                <input type="hidden" name="" id="date_to" value="{{ $request->date_to }}">
                <div class="box-body" style="min-height: 250px">
                    <div class="form-group">
                        <label for="">اختار الآله</label>
                        <select class="form-control" id="machine_id" style="width:50%" name="" id="">
                            <option value="" selected disabled>الآله</option>
                            @foreach (App\Machines::get() as $machine)
                                <option value="{{ $machine->id }}"
                                    {{ optional($request)->machine_id == $machine->id ? 'selected' : '' }}>
                                    {{ $machine->name }}</option>
                            @endforeach
                        </select>
                        {{-- <button class="btn btn-primary">بحث</button> --}}
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="nav-tabs-custom">
                                <ul class="nav nav-tabs">
                                    <li class="active">
                                        <a href="#specials" data-toggle="tab" aria-expanded="false">المقاسات الخاصة</a>
                                    </li>
                                    {{-- <li>
                                        <a href="#scraps" data-toggle="tab" aria-expanded="false">الخردة</a>
                                    </li> --}}
                                    {{-- <li>
                                        <a href="#pieces" data-toggle="tab" aria-expanded="false">الفضل</a>
                                    </li> --}}
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane active" id="specials">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="special_dataTable">
                                                <thead>
                                                    <tr>
                                                        <td>ناتج الشغل</td>
                                                        <td>التاريخ</td>
                                                        <td>الإسم</td>
                                                        <td>المجموعة</td>
                                                        <td>الكمية</td>
                                                        <td>الطول</td>
                                                        <td>العرض</td>
                                                        <td>ملاحظات</td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $totalQnt = 0.0;
                                                    @endphp
                                                    @foreach ($specials as $special)
                                                        @if ($special->is_special == 1)
                                                            @php
                                                                $itemQnt = App\Quantity::where('ownerable_type', 'App\Models\Store')
                                                                    ->where('ownerable_id', $special->operationOrder->store_id)
                                                                    ->where('item_id', $special->id)
                                                                    ->first();
                                                                $totalQnt += optional($itemQnt)->quantity;
                                                            @endphp
                                                            <tr>
                                                                <td><a target="_blank"
                                                                        href="{{ route('dashboard.operation_orders.show', $special->operat_ord_id) }}">{{ $special->operat_ord_id }}</a>
                                                                </td>
                                                                <td>{{ $special->operationOrder->date }}</td>
                                                                <td>{{ $special->name }}</td>
                                                                <td>{{ $special->group->name }}</td>
                                                                <td>{{ optional($itemQnt)->quantity }}</td>
                                                                <td>{{ $special->length }}</td>
                                                                <td>{{ $special->width }}</td>
                                                                <td>{{ $special->operationOrder->notes }}</td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                    @foreach ($specials2 as $special2)
                                                        @if ($special2->outItem->is_special == 1)
                                                            @php
                                                                $itemQnt = App\Quantity::where('ownerable_type', 'App\Models\Store')
                                                                    ->where('ownerable_id', $special2->operationOrder->store_id)
                                                                    ->where('item_id', $special2->out_item_id)
                                                                    ->first();
                                                                $totalQnt += optional($itemQnt)->quantity;
                                                            @endphp
                                                            <tr>
                                                                <td><a target="_blank"
                                                                        href="{{ route('dashboard.operation_orders.show', $special2->operation_order_id) }}">{{ $special2->operation_order_id }}</a>
                                                                </td>
                                                                <td>{{ $special2->operationOrder->date }}</td>
                                                                <td>{{ optional($special2->outItem)->name }}</td>
                                                                <td>{{ optional(optional($special2->outItem)->group)->name }}
                                                                </td>
                                                                <td>{{ optional($itemQnt)->quantity }}</td>
                                                                <td>{{ $special2->length }}</td>
                                                                <td>{{ $special2->width }}</td>
                                                                <td>{{ $special2->operationOrder->notes }}</td>
                                                            </tr>
                                                        @endif
                                                    @endforeach

                                                </tbody>
                                                <tr>
                                                    <td>المجموع</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>{{ $totalQnt }}</td>
                                                    <td></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    {{-- <div class="tab-pane" id="scraps">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <td>أمر التشغيل</td>
                                                        <td>التاريخ</td>
                                                        <td>الإسم</td>
                                                        <td>المجموعة</td>
                                                        <td>الكمية</td>
                                                        <td>الطول</td>
                                                        <td>العرض</td>
                                                        <td>الوزن</td>
                                                        <td>ملاحظات</td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $totalQnt = 0.0;
                                                        $totalWeight = 0.0;
                                                    @endphp
                                                    @foreach ($scraps as $scrap)
                                                        @php
                                                            $itemQnt = App\Quantity::where('ownerable_type', 'App\Models\Store')
                                                                ->where('ownerable_id', $scrap->operationOrder->store_id)
                                                                ->where('item_id', $scrap->id)
                                                                ->first();
                                                            $totalQnt += optional($itemQnt)->quantity;
                                                            $totalWeight += $scrap->weight;
                                                        @endphp
                                                        <tr>
                                                            <td><a target="_blank"
                                                                    href="{{ route('dashboard.operation_orders.show', $scrap->operat_ord_id) }}">{{ $scrap->operat_ord_id }}</a>
                                                            </td>
                                                            <td>{{ $scrap->operationOrder->date }}</td>
                                                            <td>{{ $scrap->name }}</td>
                                                            <td>{{ $scrap->group->name }}</td>
                                                            <td>{{ optional($itemQnt)->quantity }}</td>
                                                            <td>{{ $scrap->length }}</td>
                                                            <td>{{ $scrap->width }}</td>
                                                            <td>{{ $scrap->weight }}</td>
                                                            <td>{{ $scrap->operationOrder->notes }}</td>
                                                        </tr>
                                                    @endforeach
                                                    <tr>
                                                        <td>المجموع</td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>{{ $totalQnt }}</td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>{{ $totalWeight }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div> --}}
                                    {{-- <div class="tab-pane" id="pieces">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="piece_dataTable">
                                                <thead>
                                                    <tr>
                                                        <td>أمر التشغيل</td>
                                                        <td>التاريخ</td>
                                                        <td>الإسم</td>
                                                        <td>المجموعة</td>
                                                        <td>الكمية</td>
                                                        <td>الطول</td>
                                                        <td>العرض</td>
                                                        <td>الوزن</td>
                                                        <td>ملاحظات</td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $totalQnt = 0.0;
                                                        $totalWeight = 0.0;
                                                    @endphp
                                                    @foreach ($pieces as $piece)
                                                        @php
                                                            $itemQnt = App\Quantity::where('ownerable_type', 'App\Models\Store')
                                                                ->where('ownerable_id', $piece->operationOrder->store_id)
                                                                ->where('item_id', $piece->id)
                                                                ->first();
                                                            $totalQnt += optional($itemQnt)->quantity;
                                                            $totalWeight += $piece->weight;
                                                        @endphp
                                                        <tr>
                                                            <td><a target="_blank"
                                                                    href="{{ route('dashboard.operation_orders.show', $piece->operat_ord_id) }}">{{ $piece->operat_ord_id }}</a>
                                                            </td>
                                                            <td>{{ $piece->operationOrder->date }}</td>
                                                            <td>{{ $piece->name }}</td>
                                                            <td>{{ $piece->group->name }}</td>
                                                            <td>{{ optional($itemQnt)->quantity }}</td>
                                                            <td>{{ $piece->length }}</td>
                                                            <td>{{ $piece->width }}</td>
                                                            <td>{{ $piece->weight }}</td>
                                                            <td>{{ $piece->operationOrder->notes }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tr>
                                                    <td>المجموع</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>{{ $totalQnt }}</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td>{{ $totalWeight }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div> --}}
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
        $(function() {
            $('select').select2({
                width: '100%'
            });
        });
        var special_dataTable = $('#special_dataTable').DataTable({
            paging: true,
            serverSide: false,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            order: [
                    // Initial order column index and direction
                    [1, 'desc'] // Assuming 'joined_at' is the third column (zero-based index)
                ],
        });
        var piece_dataTable = $('#piece_dataTable').DataTable({
            paging: true,
            serverSide: false,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
        })
        var date_from = $("#date_from").val(),
            date_to = $("#date_to").val();


        $("#machine_id").change(function() {
            url =
                `{{ route('dashboard.reports.damage_special') }}?machine_id=${$(this).val()}&date_from=${date_from}&date_to=${date_to}`;
            window.location.href = url;
        })
    </script>
@endsection



{{--@extends('layouts.dashboard.app')
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
                <h1>تقرير الخردة والفضل والمقاسات الخاصة</h1>
                <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                    <li class="active">تقرير الخردة والفضل والمقاسات الخاصة</li>
                </ol>
            </section>
            <section class="content" style='padding-bottom: 0px !important;'>

                <div class="box box-primary">

                    <div class="box-header with-border">
                        <h3 class="box-title" style="margin-bottom: 15px">تقرير الخردة والفضل والمقاسات الخاصة</h3>
                    </div>

                    <div class="box-body" style="min-height: 250px">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="nav-tabs-custom">
                                    <ul class="nav nav-tabs">
                                        <li class="active">
                                            <a href="#specials" data-toggle="tab" aria-expanded="false">المقاسات الخاصة</a>
                                        </li>
                                        <li>
                                            <a href="#scraps" data-toggle="tab" aria-expanded="false">الخردة</a>
                                        </li>
                                        <li>
                                            <a href="#pieces" data-toggle="tab" aria-expanded="false">الفضل</a>
                                        </li>
                                    </ul>

                                    <div class="tab-content">
                                        <div class="tab-pane active" id="specials">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <td>أمر التشغيل</td>
                                                            <td>التاريخ</td>
                                                            <td>الإسم</td>
                                                            <td>المجموعة</td>
                                                            <td>الكمية</td>
                                                            <td>الطول</td>
                                                            <td>العرض</td>
                                                            <td>ملاحظات</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $totalQnt=0.0;
                                                        @endphp
                                                        @foreach ($specials as $special)
                                                        @if ($special->is_special == 1)
                                                        @php
                                                            $itemQnt = App\Quantity::where('ownerable_type', 'App\Models\Store')
                                                                                    ->where('ownerable_id', $special->operationOrder->store_id)
                                                                                    ->where('item_id', $special->id)
                                                                                    ->first();
                                                            $totalQnt+=optional($itemQnt)->quantity;
                                                        @endphp
                                                        <tr>
                                                            <td><a target="_blank" href="{{route('dashboard.operation_orders.show',$special->operat_ord_id)}}">{{$special->operat_ord_id}}</a></td>
                                                            <td>{{$special->operationOrder->date}}</td>
                                                            <td>{{$special->name}}</td>
                                                            <td>{{$special->group->name}}</td>
                                                            <td>{{optional($itemQnt)->quantity}}</td>
                                                            <td>{{$special->length}}</td>
                                                            <td>{{$special->width}}</td>
                                                            <td>{{$special->operationOrder->notes}}</td>
                                                        </tr>
                                                        @endif
                                                        @endforeach
                                                        @foreach ($specials2 as $special2)
                                                        @if ($special2->outItem->is_special == 1)
                                                        @php
                                                            $itemQnt = App\Quantity::where('ownerable_type', 'App\Models\Store')
                                                                                    ->where('ownerable_id', $special2->operationOrder->store_id)
                                                                                    ->where('item_id', $special2->out_item_id)
                                                                                    ->first();
                                                            $totalQnt+=optional($itemQnt)->quantity;
                                                        @endphp
                                                        <tr>
                                                            <td><a target="_blank" href="{{route('dashboard.operation_orders.show',$special2->operation_order_id)}}">{{$special2->operation_order_id}}</a></td>
                                                            <td>{{$special2->operationOrder->date}}</td>
                                                            <td>{{optional($special2->outItem)->name}}</td>
                                                            <td>{{optional(optional($special2->outItem)->group)->name}}</td>
                                                            <td>{{optional($itemQnt)->quantity}}</td>
                                                            <td>{{$special2->length}}</td>
                                                            <td>{{$special2->width}}</td>
                                                            <td>{{$special2->operationOrder->notes}}</td>
                                                        </tr>
                                                        @endif
                                                        @endforeach
                                                        <tr>
                                                            <td>المجموع</td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td>{{$totalQnt}}</td>
                                                            <td></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="scraps">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <td>أمر التشغيل</td>
                                                            <td>التاريخ</td>
                                                            <td>الإسم</td>
                                                            <td>المجموعة</td>
                                                            <td>الكمية</td>
                                                            <td>الطول</td>
                                                            <td>العرض</td>
                                                            <td>الوزن</td>
                                                            <td>ملاحظات</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $totalQnt=0.0;
                                                            $totalWeight=0.0;
                                                        @endphp
                                                        @foreach ($scraps as $scrap)
                                                        @php
                                                            $itemQnt = App\Quantity::where('ownerable_type', 'App\Models\Store')
                                                                                    ->where('ownerable_id', $scrap->operationOrder->store_id)
                                                                                    ->where('item_id', $scrap->id)
                                                                                    ->first();
                                                            $totalQnt+=optional($itemQnt)->quantity;
                                                            $totalWeight+=$scrap->weight;
                                                        @endphp
                                                        <tr>
                                                            <td><a target="_blank" href="{{route('dashboard.operation_orders.show',$scrap->operat_ord_id)}}">{{$scrap->operat_ord_id}}</a></td>
                                                            <td>{{$scrap->operationOrder->date}}</td>
                                                            <td>{{$scrap->name}}</td>
                                                            <td>{{$scrap->group->name}}</td>
                                                            <td>{{optional($itemQnt)->quantity}}</td>
                                                            <td>{{$scrap->length}}</td>
                                                            <td>{{$scrap->width}}</td>
                                                            <td>{{$scrap->weight}}</td>
                                                            <td>{{$scrap->operationOrder->notes}}</td>
                                                        </tr>
                                                        @endforeach
                                                        <tr>
                                                            <td>المجموع</td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td>{{$totalQnt}}</td>
                                                            <td></td>
                                                            <td></td>
                                                            <td>{{$totalWeight}}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="pieces">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <td>أمر التشغيل</td>
                                                            <td>التاريخ</td>
                                                            <td>الإسم</td>
                                                            <td>المجموعة</td>
                                                            <td>الكمية</td>
                                                            <td>الطول</td>
                                                            <td>العرض</td>
                                                            <td>الوزن</td>
                                                            <td>ملاحظات</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $totalQnt=0.0;
                                                            $totalWeight=0.0;
                                                        @endphp
                                                        @foreach ($pieces as $piece)
                                                        @php
                                                            $itemQnt = App\Quantity::where('ownerable_type', 'App\Models\Store')
                                                                                    ->where('ownerable_id', $piece->operationOrder->store_id)
                                                                                    ->where('item_id', $piece->id)
                                                                                    ->first();
                                                            $totalQnt+=optional($itemQnt)->quantity;
                                                            $totalWeight+=$piece->weight;
                                                        @endphp
                                                        <tr>
                                                            <td><a target="_blank" href="{{route('dashboard.operation_orders.show',$piece->operat_ord_id)}}">{{$piece->operat_ord_id}}</a></td>
                                                            <td>{{$piece->operationOrder->date}}</td>
                                                            <td>{{$piece->name}}</td>
                                                            <td>{{$piece->group->name}}</td>
                                                            <td>{{optional($itemQnt)->quantity}}</td>
                                                            <td>{{$piece->length}}</td>
                                                            <td>{{$piece->width}}</td>
                                                            <td>{{$piece->weight}}</td>
                                                            <td>{{$piece->operationOrder->notes}}</td>
                                                        </tr>
                                                        @endforeach
                                                        <tr>
                                                            <td>المجموع</td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td>{{$totalQnt}}</td>
                                                            <td></td>
                                                            <td></td>
                                                            <td>{{$totalWeight}}</td>
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
--}}