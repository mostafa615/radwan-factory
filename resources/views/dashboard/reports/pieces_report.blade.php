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
            <h1>تقرير الفضله</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">تقرير الفضله</li>
            </ol>
        </section>
        <section class="content" style='padding-bottom: 0px !important;'>

            <div class="box box-primary">

                <div class="box-header with-border">
                    <h3 class="box-title" style="margin-bottom: 15px">تقرير الفضله</h3>
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


                                <div class="tab-content">

                                    <div class="tab-pane active" id="part">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="piece_dataTable">
                                                <thead>
                                                    <tr>
                                                        <td>ناتج الشغل</td>
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
        });
        var piece_dataTable = $('#piece_dataTable').DataTable({
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
        var date_from = $("#date_from").val(),
            date_to = $("#date_to").val();


        $("#machine_id").change(function() {
            url =
                `{{ route('dashboard.reports.pieces_report') }}?machine_id=${$(this).val()}&date_from=${date_from}&date_to=${date_to}`;
            window.location.href = url;
        })
    </script>
@endsection
