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
            <h1>تقرير الخرده</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">تقرير الخرده</li>
            </ol>
        </section>
        <section class="content" style='padding-bottom: 0px !important;'>

            <div class="box box-primary">

                <div class="box-header with-border">
                    <h3 class="box-title" style="margin-bottom: 15px">تقرير الخرده</h3>
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
                                        <a href="#part" data-toggle="tab" aria-expanded="false">تفصيلى</a>
                                    </li>

                                    <li>
                                        <a href="#global" data-toggle="tab" aria-expanded="false">اجمالى</a>
                                    </li>
                                </ul>

                                <div class="tab-content">

                                    <div class="tab-pane active" id="part">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="parts">
                                                <thead>
                                                    <tr>
                                                        <td>ناتج الشغل</td>
                                                        <td>التاريخ</td>
                                                        <td>الإسم</td>
                                                        <td>المجموعة</td>
                                                        <td>الكمية</td>
                                                        <td>الطول</td>
                                                        <td>العرض</td>
                                                        <td>العدد</td>
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
                                    </div>
                                    <div class="tab-pane fade" id="global">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="golbal_table">
                                                <thead>
                                                    <tr>
                                                        {{-- <td>المجموعة</td> --}}
                                                        <td>الصنف</td>

                                                        @foreach ($stores as $store)
                                                            <td>{{ $store->name }}</td>
                                                        @endforeach
                                                        <td>الاجمالي</td>
                                                        {{-- <td>المخزن</td> --}}
                                                        {{-- <td>الكمية</td> --}}
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($resources as $resource)
                                                        <tr>
                                                            {{-- <td>{{ optional($resource->group)->name }}</td> --}}
                                                            <td>{{ $resource->name }}</td>

                                                            <?php
                                                            $total_quantity = 0;
                                                            ?>
                                                            @foreach ($stores as $store)
                                                                <?php
                                                                $total_quantity += optional(
                                                                    App\Quantity::where('ownerable_type', 'App\Models\Store')
                                                                        ->where('ownerable_id', $store->id)
                                                                        ->where('item_id', $resource->id)
                                                                        ->first(),
                                                                )->quantity;
                                                                ?>
                                                                <td>{{ optional(App\Quantity::where('ownerable_type', 'App\Models\Store')->where('ownerable_id', $store->id)->where('item_id', $resource->id)->first())->quantity }}
                                                                </td>
                                                            @endforeach
                                                            <td>{{ $total_quantity }}</td>
                                                            {{-- </tr> --}}
                                                            {{-- foreach($resource->quantities as $quantity) --}}
                                                            {{-- <tr> --}}
                                                            {{-- <td>{{optional($quantity->ownerable)->name}}</td> --}}
                                                            {{-- <td>{{$quantity->quantity}}</td> --}}
                                                            {{-- </tr> --}}
                                                            {{-- endforeach --}}
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
        $(function() {
            $('select').select2({
                width: '100%'
            });
        });
        var parts = $('#parts').DataTable({
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
        var golbal_table = $('#golbal_table').DataTable({
            paging: true,
            serverSide: false,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            
        });
        var date_from = $("#date_from").val(),
            date_to = $("#date_to").val();


        $("#machine_id").change(function() {
            url =
                `{{ route('dashboard.reports.scraps_report') }}?machine_id=${$(this).val()}&date_from=${date_from}&date_to=${date_to}`;
            window.location.href = url;
        })
    </script>
@endsection
