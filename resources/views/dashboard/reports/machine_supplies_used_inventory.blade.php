@extends('layouts.dashboard.app')
@section('title', 'التقارير')
@section('sub-title', ' جرد استخدام مستلزمات للماكينه')
@section('content')
    <div class="content-wrapper">
        <div class="row">

            <div class="col-md-12">


                <div class="nav-tabs-custom">
                    <table class="table table-bordered">
                        <tr>
                            <td>الماكينه</td>
                            <td>المستلزم</td>
                            <td>باقى الاستخدام</td>
                            <td>التاريخ من </td>
                            <td>التاريخ الى</td>
                        </tr>
                        <tr>
                            <td>{{ App\Machines::where('id', $machine_supplie->machine_id)->first()->name }}</td>
                            <td>{{ App\Supplies::where('id', $machine_supplie->supplie_id)->first()->name }}</td>
                            <td>{{ $machine_supplie->used }}</td>
                            <td>{{ $request->date_from }}</td>
                            <td>{{ $request->date_to }}</td>
                        </tr>
                    </table>
                        <hr style="border:1px solid #CCC">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#operation-order-in" data-toggle="tab" aria-expanded="false"> ناتج أوامر
                                التشغيل
                                الداخلى</a>
                        </li>
                        <li><a href="#operation-order-out" data-toggle="tab" aria-expanded="false"> ناتج أوامر التشغيل
                                الخارجى</a>
                        </li>

                        <li><a href="#exchange_from" data-toggle="tab" aria-expanded="false">تحويل من</a></li>
                        <li><a href="#exchange_to" data-toggle="tab" aria-expanded="false">تحويل الى</a></li>
                        <li><a href="#machine_supplie" data-toggle="tab" aria-expanded="false">صرف للآله</a></li>
                        <li><a href="#itemReport" data-toggle="tab" aria-expanded="false">حساب مجمع</a></li>
                    </ul>
                    <div class="tab-content">
                        
                        
                        <div class="tab-pane  active" id="operation-order-in">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <td>التاريخ</td>
                                        <td>الكميه</td>
                                        <td>رقم ناتج الامر</td>
                                        {{--<td>رقم الامر</td>--}}
                                    </tr>
                                    @php
                                        $total_order_in_quantity = 0;
                                    @endphp
                                    @foreach ($operation_orders_in as $item)
                                        @php
                                            $total_order_in_quantity += $item->quantity;
                                        @endphp
                                        <tr>
                                            <td>{{ $item->date }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td><a href="{{ route('dashboard.operation_order_results.show', $item->operation_order_result_id ??0) }}"
                                                    target="_blank">
                                                    {{ $item->operation_order_result_id }}
                                                </a>
                                            </td>
                                            {{--<td><a href="{{ route('dashboard.operation_orders.show', $item->operation_order_id ??0) }}"
                                                    target="_blank">
                                                    {{ $item->operation_order_id }}
                                                </a>
                                            </td>--}}
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td>إجمالي الكمية</td>
                                        <td>{{ $total_order_in_quantity }}</td>
                                        <td></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade " id="operation-order-out">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <td>التاريخ</td>
                                        <td>الكميه</td>
                                        <td>رقم ناتج الامر</td>
                                        {{--<td>رقم الامر</td>--}}
                                    </tr>
                                    @php
                                        $total_order_in_quantity = 0;
                                    @endphp
                                    @foreach ($operation_orders_out as $item)
                                        @php
                                            $total_order_in_quantity += $item->quantity;
                                        @endphp
                                        <tr>
                                            <td>{{ $item->date }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td><a href="{{ route('dashboard.operation_order_results.show_out', $item->operation_order_result_id ??0) }}"
                                                    target="_blank">
                                                    {{ $item->operation_order_result_id }}
                                                </a>
                                            </td>
                                            {{--<td><a href="{{ route('dashboard.operation_orders.show_out', $item->operation_order_id ??0) }}"
                                                    target="_blank">
                                                    {{ $item->operation_order_id }}
                                                </a>
                                            </td>--}}
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td>إجمالي الكمية</td>
                                        <td>{{ $total_order_in_quantity }}</td>
                                        <td></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade " id="exchange_from">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <td>التاريخ</td>
                                        {{-- <td>الكميه قبل التحويل</td>
                                        <td>الكميه بعد التحويل</td> --}}
                                        <td>الكميه المحوله</td>
                                        <td>الآله المحول اليها</td>
                                        {{-- <td>كميه الآاله الجديده قبل</td>
                                        <td>كميه الآاله الجديده بعد</td>
                                        <td>ملاحظات</td> --}}

                                    </tr>
                                    @php
                                        $exchange_from_quantity = 0;
                                    @endphp
                                    @foreach ($exchange_from as $item)
                                        @php
                                            $exchange_from_quantity += $item->quantity;
                                        @endphp
                                        <tr>
                                            <td>{{ optional($item)->date }}</td>
                                            {{-- <td>{{ $item->old_machine_pre_used }}</td>
                                            <td>{{ $item->old_machine_used }}</td> --}}
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ App\Machines::find(App\SuppliesExchanges::find(optional($item)->exchange_id)->new_machine_id)->name }}
                                            </td>
                                            {{-- <td>{{ App\Machines::where('id', $item->new_machine_id)->first()->name }}</td>
                                            <td>{{ $item->new_machine_pre_used }}</td>
                                            <td>{{ $item->new_machine_used }}</td>
                                            <td>{{ optional($item)->notes }}</td> --}}
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td>إجمالي الكميه المحوله</td>

                                        <td>{{ $exchange_from_quantity }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade " id="exchange_to">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <td>التاريخ</td>
                                        <td>الكميه المحوله</td>
                                        <td>الآله المحول منها</td>
                                    </tr>
                                    @php
                                        $exchange_to_quantity = 0;
                                    @endphp
                                    @foreach ($exchange_to as $item)
                                        @php
                                            $exchange_to_quantity += $item->quantity;
                                        @endphp
                                        <tr>
                                            <td>{{ optional($item)->date }}</td>
                                            <td>{{ $item->quantity }}</td>
                                        <td>{{ App\Machines::find(App\SuppliesExchanges::find(optional($item)->exchange_id)->old_machine_id)->name }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td>إجمالي الكميه المحوله</td>

                                        <td>{{ $exchange_to_quantity }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade " id="machine_supplie">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <td>التاريخ</td>
                                        <td>الكمية</td>
                                        <td>الملاحظات</td>
                                    </tr>
                                    @php
                                        $total_machine_supplie_quantity = 0;
                                    @endphp
                                    @foreach ($machine_supplies as $item)
                                        @php
                                            $total_machine_supplie_quantity += $item->quantity;
                                        @endphp
                                        <tr>
                                            <td>{{ $item->date }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ $item->notes }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td>إجمالي الكميه المحوله</td>
                                        <td>{{ $total_machine_supplie_quantity }}</td>
                                    </tr>

                                </table>
                            </div>
                        </div>


                        <div class="tab-pane fade" id="itemReport">
                            <div class="table-responsive">
                                <!--<div>-->
                                <!--    الرصيد الافتتاحي / {{ $init_quantities[0] ?? 0 }}-->
                                <!--</div>-->
                                <table class="table table-bordered datatable-no-paging" style="width: 100%!important"
                                    id="item-report-table">
                                    <thead>
                                        <tr class="amount-header">
                                            <td>التاريخ</td>
                                            <td>الكميه الافتتاحيه</td>
                                            <td>ناتج امر تشغيل داخلى</td>
                                            <td>ناتج امر تشغيل خارجى</td>
                                            <td>تحويل من</td>
                                            <td>تحويل الى</td>
                                            <td>صرف</td>
                                            <td>كميه نهايه اليوم </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $test = [];
                                        @endphp

                                        @forelse ($dates_array as $index => $date)
                                            @php
                                                $quantity = ($order_operation_orders_in[$index] ?? 0) + ($order_operation_orders_out[$index] ?? 0) + ($exchanges_from[$index] ?? 0) + ($exchanges_to[$index] ?? 0) + ($order_machine_supplies[$index] ?? 0);
                                            @endphp
                                            <tr class="amount-row" date-data={{ $date }}>
                                                <td>{{ $date }}</td>
                                                @php
                                                    $init_quantity = $index != 0 ? $test[$index - 1] ?? 0 : $init_quantities[$index] ?? 0;
                                                @endphp
                                                <td class="init">
                                                    {{ $index != 0 ? $test[$index - 1] ?? 0 : $init_quantities[$index] ?? 0 }}
                                                <td class="order_in">{{ $order_operation_orders_in[$index] ?? 0 }}</td>
                                                <td class="order_out">{{ $order_operation_orders_out[$index] ?? 0 }}</td>
                                                <td class="ex_from">{{ $exchanges_from[$index] ?? 0 }}</td>
                                                <td class="ex_to">{{ $exchanges_to[$index] ?? 0 }}</td>

                                                <td class="machine_supplie">{{ $order_machine_supplies[$index] ?? 0 }}</td>
                                                <td class="last">{{ $test[] = $quantity + $init_quantity ?? 0 }}
                                                </td>
                                            </tr>
                                        @empty
                                        @endforelse

                                    </tbody>
                                </table>
                            </div>
                        </div>


                        
                    </div>
                </div>


            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        dates = {!! json_encode($dates) !!};

        function calculateBalance() {
            setTimeout(function() {
                console.log(dates);
                $('.amount-row').each(function() {
                    date = $(this).attr('date-data');
                    if (!dates.includes(date)) {
                        $(this).css('display', 'none');
                    }
                });
            }, 1000);
        }

        // $('.amount-header td').click(function() {
        //     calculateBalance();
        // });

        $('#item-report-table').DataTable({
            paging: true,
            serverSide: false,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            /**/

        });

        $(document).ready(function() {
            calculateBalance();
        });
    </script>
@endsection
