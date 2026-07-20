@extends('layouts.dashboard.app')
@section('content')
  <div class="content-wrapper">
    <section class="content-header">
      <h1>تقرير حركة المستلزم</h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
        <li class="active">تقرير حركة المستلزم</li>
      </ol>
    </section>
    <section class="content">
      <div class="box box-primary">
        <div class="box-header">
          <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover text-center">
              <thead>
                <tr>
                  <th>اسم المستلزم</th>
                  <th>من تاريخ</th>
                  <th>الى تاريخ</th>
                  <th>الرصيد الافتتاحي</th>
                  <th>رصيد بداية المدة</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>{{ $supplie->name }}</td>
                  <td>{{ $startDate }}</td>
                  <td>{{ $endDate }}</td>
                  <td>{{ $supplie->first_balance }}</td>
                  <td>{{ $initBalance }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="box-body">
          <div class="row">
            <div class="col-md-12">
              <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                  <li class="active">
                    <a href="#machineSupplies" data-toggle="tab" aria-expanded="false">صرف الالات</a>
                  </li>
                  <li class="">
                    <a href="#exchange" data-toggle="tab" aria-expanded="false">تحويل الالات</a>
                  </li>
                  <li class="">
                    <a href="#ordersIn" data-toggle="tab" aria-expanded="false">فواتير البيع</a>
                  </li>
                  <li class="">
                    <a href="#ordersInReturn" data-toggle="tab" aria-expanded="false">مرتجعات البيع</a>
                  </li>
                  <li class="">
                    <a href="#ordersOut" data-toggle="tab" aria-expanded="false">فواتير الشراء</a>
                  </li>
                  <li class="">
                    <a href="#ordersOutReturn" data-toggle="tab" aria-expanded="false">مرتجعات الشراء</a>
                  </li>
                  <li class="">
                    <a href="#supplieSummary" data-toggle="tab" aria-expanded="false">حساب مجمع</a>
                  </li>
                </ul>
                <div class="tab-content">
                  <div class="tab-pane active" id="machineSupplies">
                    <div class="table-responsive">
                      <table class="table table-bordered table-striped table-hover text-center">
                        <thead>
                          <tr>
                            <th>التاريخ</th>
                            <th>النوع</th>
                            <th>الألة</th>
                            <th>الكمية</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($machineSupplies->where('type', 'machine_supplie') as $item)
                            <tr>
                              <td>
                                {{ Carbon\Carbon::parse($item->date)->format('Y-m-d') }}
                              </td>
                              <td>
                                صرف ألة
                              </td>
                              <td>
                                {{ $item->machine_name }}
                              </td>
                              <td class="text-bold">
                                {{ $item->quantity }}
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                        <tfoot>
                          <tr>
                            <td class="text-bold">الاجمالي</td>
                            <td></td>
                            <td></td>
                            <td class="text-bold">
                              {{ $machineSupplies->where('type', 'machine_supplie')->sum('quantity') }}
                            </td>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                  <div class="tab-pane" id="exchange">
                    <div class="table-responsive">
                      <table class="table table-bordered table-striped table-hover text-center">
                        <thead>
                          <tr>
                            <th>التاريخ</th>
                            <th>النوع</th>
                            <th>الألة</th>
                            <th>الكمية</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($machineSupplies->whereIn('type', ['exchange_in', 'exchange_out']) as $item)
                            @php
                              $type = $item->type == 'exchange_in' ? 'تحويل الى ألة' : 'تحويل من ألة';
                              $quantity = $item->type == 'exchange_in' ? -$item->quantity : $item->quantity;
                            @endphp
                            <tr>
                              <td>
                                {{ Carbon\Carbon::parse($item->date)->format('Y-m-d') }}
                              </td>
                              <td>
                                {{ $type }}
                              </td>
                              <td>
                                {{ $item->machine_name }}
                              </td>
                              <td class="text-bold">
                                {{ $quantity > 0 ? '+' . $quantity : $quantity }}
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                        <tfoot>
                          <tr>
                            <td class="text-bold">الاجمالي</td>
                            <td></td>
                            <td></td>
                            <td class="text-bold">
                              {{ $machineSupplies->whereIn('type', ['exchange_in', 'exchange_out'])->sum(function ($item) {
                                  return $item->type == 'exchange_out' ? $item->quantity : -$item->quantity;
                              }) }}
                            </td>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                  <div class="tab-pane" id="ordersIn">
                    <div class="table-responsive">
                      <table class="table table-bordered table-striped table-hover text-center">
                        <thead>
                          <tr>
                            <th>التاريخ</th>
                            <th>الفاتورة</th>
                            <th>الكمية</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($ordersIn as $item)
                            <tr>
                              <td>{{ Carbon\Carbon::parse($item->date)->format('Y-m-d') }}</td>
                              <td><a href="https://www.radwansteel.org/orders-in/{{ $item->id }}"
                                  target="_blank">#{{ $item->id }}</a></td>
                              <td>{{ $item->quantity }}</td>
                            </tr>
                          @endforeach
                        </tbody>
                        <tfoot>
                          <tr>
                            <td class="text-bold">الاجمالي</td>
                            <td></td>
                            <td class="text-bold">{{ $ordersIn->sum('quantity') }}</td>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                  <div class="tab-pane" id="ordersInReturn">
                    <div class="table-responsive">
                      <table class="table table-bordered table-striped table-hover text-center">
                        <thead>
                          <tr>
                            <th>التاريخ</th>
                            <th>الفاتورة</th>
                            <th>الكمية</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($ordersInReturn as $item)
                            <tr>
                              <td>{{ Carbon\Carbon::parse($item->date)->format('Y-m-d') }}</td>
                              <td><a href="https://www.radwansteel.org/return-orders-in/{{ $item->id }}"
                                  target="_blank">#{{ $item->id }}</a></td>
                              <td>{{ $item->quantity }}</td>
                            </tr>
                          @endforeach
                        </tbody>
                        <tfoot>
                          <tr>
                            <td class="text-bold">الاجمالي</td>
                            <td></td>
                            <td class="text-bold">{{ $ordersInReturn->sum('quantity') }}</td>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                  <div class="tab-pane" id="ordersOut">
                    <div class="table-responsive">
                      <table class="table table-bordered table-striped table-hover text-center">
                        <thead>
                          <tr>
                            <th>التاريخ</th>
                            <th>الفاتورة</th>
                            <th>الكمية</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($ordersOut as $item)
                            <tr>
                              <td>{{ Carbon\Carbon::parse($item->date)->format('Y-m-d') }}</td>
                              <td><a href="https://www.radwansteel.org/orders-out/{{ $item->id }}"
                                  target="_blank">#{{ $item->id }}</a></td>
                              <td>{{ $item->quantity }}</td>
                            </tr>
                          @endforeach
                        </tbody>
                        <tfoot>
                          <tr>
                            <td class="text-bold">الاجمالي</td>
                            <td></td>
                            <td class="text-bold">{{ $ordersOut->sum('quantity') }}</td>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                  <div class="tab-pane" id="ordersOutReturn">
                    <div class="table-responsive">
                      <table class="table table-bordered table-striped table-hover text-center">
                        <thead>
                          <tr>
                            <th>التاريخ</th>
                            <th>الفاتورة</th>
                            <th>الكمية</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($ordersOutReturn as $item)
                            <tr>
                              <td>{{ Carbon\Carbon::parse($item->date)->format('Y-m-d') }}</td>
                              <td><a href="https://www.radwansteel.org/return-orders-out/{{ $item->id }}"
                                  target="_blank">#{{ $item->id }}</a></td>
                              <td>{{ $item->quantity }}</td>
                            </tr>
                          @endforeach
                        </tbody>
                        <tfoot>
                          <tr>
                            <td class="text-bold">الاجمالي</td>
                            <td></td>
                            <td class="text-bold">{{ $ordersOutReturn->sum('quantity') }}</td>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                  <div class="tab-pane" id="supplieSummary">
                    <div class="table-responsive">
                      <table class="table table-bordered table-striped table-hover text-center">
                        <thead>
                          <tr>
                            <th>التاريخ</th>
                            <th>حركة المستلزم</th>
                            <th>فاتورة بيع</th>
                            <th>مرتجع بيع</th>
                            <th>فاتورة شراء</th>
                            <th>مرتجع شراء</th>
                            <th>الرصيد</th>
                          </tr>
                        </thead>
                        <tbody>
                          @php
                            $balance = $initBalance;
                          @endphp
                          @foreach ($supplieSummary as $item)
                            @php
                              $balance += $item['quantity'];
                            @endphp
                            <tr>
                              <td>
                                {{ $item['date'] }}
                              </td>
                              <td>
                                {{ $item['machine_supplie'] > 0 ? '+' . $item['machine_supplie'] : $item['machine_supplie'] }}
                              </td>
                              <td>
                                {{ $item['orders_in'] != 0 ? $item['orders_in'] : 0 }}
                              </td>
                              <td>
                                {{ $item['orders_in_return'] > 0 ? '+' . $item['orders_in_return'] : 0 }}
                              </td>
                              <td>
                                {{ $item['orders_out'] > 0 ? '+' . $item['orders_out'] : 0 }}
                              </td>
                              <td>
                                {{ $item['orders_out_return'] != 0 ? $item['orders_out_return'] : 0 }}
                              </td>
                              <td class="text-bold">
                                {{ $balance }}
                              </td>
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
