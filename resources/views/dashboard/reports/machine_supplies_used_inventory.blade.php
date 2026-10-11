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
                            <td>الرصيد الافتتاحي <small>({{ $openingDate }})</small></td>
                            <td>رصيد بداية المدة <small>({{ $periodStartDate }})</small></td>
                            <td>رصيد نهاية المدة <small>({{ $periodEndDate }})</small></td>
                            <td>التاريخ من </td>
                            <td>التاريخ الى</td>
                        </tr>
                        <tr>
                            <td>{{ App\Machines::where('id', $machine_supplie->machine_id)->first()->name }}</td>
                            <td>{{ App\Supplies::where('id', $machine_supplie->supplie_id)->first()->name }}</td>
                            <td>{{ $machine_supplie->used }}</td>
                            <td>{{ number_format($openingBalance, 2, '.', '') }}</td>
                            <td>{{ number_format($periodStartBalance, 2, '.', '') }}</td>
                            <td>{{ number_format($periodEndBalance, 2, '.', '') }}</td>
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
                                            <td>
                                                {{ $item->operation_order_result_id }}
                                                <a href="{{ route('dashboard.operation_order_results.show', $item->operation_order_result_id ??0) }}"
                                                    target="_blank" class="btn btn-xs btn-primary" title="طباعة">
                                                    <i class="fa fa-print"></i> طباعة
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
                                            <td>
                                                {{ $item->operation_order_result_id }}
                                                <a href="{{ route('dashboard.operation_order_results.show_out', $item->operation_order_result_id ??0) }}"
                                                    target="_blank" class="btn btn-xs btn-primary" title="طباعة">
                                                    <i class="fa fa-print"></i> طباعة
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
                                        {{-- صفوف الفترة المختارة فقط (محسوبة في الكنترولر مع حمل الرصيد الصحيح للفترة) --}}
                                        @forelse ($summaryRows as $row)
                                            <tr class="amount-row" date-data="{{ $row['date'] }}">
                                                <td>{{ $row['date'] }}</td>
                                                <td class="init">{{ number_format($row['init'], 2, '.', '') }}</td>
                                                <td class="order_in">{{ $row['order_in'] }}</td>
                                                <td class="order_out">{{ $row['order_out'] }}</td>
                                                <td class="ex_from">{{ $row['ex_from'] }}</td>
                                                <td class="ex_to">{{ $row['ex_to'] }}</td>
                                                <td class="machine_supplie">{{ $row['machine_supplie'] }}</td>
                                                <td class="last">{{ number_format($row['last'], 2, '.', '') }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="8" class="text-center">لا توجد حركات في الفترة المختارة</td></tr>
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
            order: [[0, 'desc']], // ترتيب بالتاريخ الأحدث أولاً (الأرصدة محسوبة مسبقًا كنص ثابت فلا تتأثر)
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
