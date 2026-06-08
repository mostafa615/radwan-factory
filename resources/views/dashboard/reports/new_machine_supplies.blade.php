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
            <table class="table table-bordered text-center">
              <thead>
              <tr>
                <th>اسم المستلزم</th>
                <th>من تاريخ</th>
                <th>الى تاريخ</th>
                <th>الرصيد الافتتاحي</th>
              </tr>
              </thead>
              <tbody>
                <tr>
                  <td>{{ $supplie->name }}</td>
                  <td>{{ $startDate }}</td>
                  <td>{{ $endDate }}</td>
                  <td>{{ $supplie->init_quantity }}</td>
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
                    <a href="#machineSupplies" data-toggle="tab" aria-expanded="false">مستلزمات الألات</a>
                  </li>
                  <li class="">
                    <a href="#supplieOrdersIn" data-toggle="tab" aria-expanded="false">فواتير البيع</a>
                  </li>
                  <li class="">
                    <a href="#supplieOrdersInReturn" data-toggle="tab" aria-expanded="false">مرتجعات البيع</a>
                  </li>
                  <li class="">
                    <a href="#supplieOrdersOut" data-toggle="tab" aria-expanded="false">فواتير الشراء</a>
                  </li>
                  <li class="">
                    <a href="#supplieOrdersOutReturn" data-toggle="tab" aria-expanded="false">مرتجعات الشراء</a>
                  </li>
                  <li class="">
                    <a href="#supplieSummary" data-toggle="tab" aria-expanded="false">حساب مجمع</a>
                  </li>
                </ul>
                <div class="tab-content">
                  <div class="tab-pane active" id="machineSupplies">
                    <div class="table-responsive">
                      <table class="table table-bordered text-center">
                        <thead>
                          <tr>
                            <th>التاريخ</th>
                            <th>الألة</th>
                            <th>الكمية</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($machineSupplies as $item)
                            <tr>
                              <td>{{ Carbon\Carbon::parse($item->date)->format('Y-m-d') }}</td>
                              <td>{{ $item->machine_name }}</td>
                              <td>{{ $item->quantity }}</td>
                            </tr>
                          @endforeach
                        </tbody>
                        <tfoot>
                          <tr>
                            <td class="text-bold">الاجمالي</td>
                            <td></td>
                            <td class="text-bold">{{ $machineSupplies->sum('quantity') }}</td>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                  <div class="tab-pane" id="supplieOrdersIn">
                    <div class="table-responsive">
                      <table class="table table-bordered text-center">
                        <thead>
                          <tr>
                            <th>التاريخ</th>
                            <th>الفاتورة</th>
                            <th>الكمية</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($supplieOrdersIn as $item)
                            <tr>
                              <td>{{ Carbon\Carbon::parse($item->date)->format('Y-m-d') }}</td>
                              <td>#{{ $item->id }}</td>
                              <td>{{ $item->quantity }}</td>
                            </tr>
                          @endforeach
                        </tbody>
                        <tfoot>
                          <tr>
                            <td class="text-bold">الاجمالي</td>
                            <td></td>
                            <td class="text-bold">{{ $supplieOrdersIn->sum('quantity') }}</td>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                  <div class="tab-pane" id="supplieOrdersInReturn">
                    <div class="table-responsive">
                      <table class="table table-bordered text-center">
                        <thead>
                          <tr>
                            <th>التاريخ</th>
                            <th>الفاتورة</th>
                            <th>الكمية</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($supplieOrdersInReturn as $item)
                            <tr>
                              <td>{{ Carbon\Carbon::parse($item->date)->format('Y-m-d') }}</td>
                              <td>#{{ $item->id }}</td>
                              <td>{{ $item->quantity }}</td>
                            </tr>
                          @endforeach
                        </tbody>
                        <tfoot>
                          <tr>
                            <td class="text-bold">الاجمالي</td>
                            <td></td>
                            <td class="text-bold">{{ $supplieOrdersInReturn->sum('quantity') }}</td>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                  <div class="tab-pane" id="supplieOrdersOut">
                    <div class="table-responsive">
                      <table class="table table-bordered text-center">
                        <thead>
                          <tr>
                            <th>التاريخ</th>
                            <th>الفاتورة</th>
                            <th>الكمية</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($supplieOrdersOut as $item)
                            <tr>
                              <td>{{ Carbon\Carbon::parse($item->date)->format('Y-m-d') }}</td>
                              <td>#{{ $item->id }}</td>
                              <td>{{ $item->quantity }}</td>
                            </tr>
                          @endforeach
                        </tbody>
                        <tfoot>
                          <tr>
                            <td class="text-bold">الاجمالي</td>
                            <td></td>
                            <td class="text-bold">{{ $supplieOrdersOut->sum('quantity') }}</td>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                  <div class="tab-pane" id="supplieOrdersOutReturn">
                    <div class="table-responsive">
                      <table class="table table-bordered text-center">
                        <thead>
                          <tr>
                            <th>التاريخ</th>
                            <th>الفاتورة</th>
                            <th>الكمية</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($supplieOrdersOutReturn as $item)
                            <tr>
                              <td>{{ Carbon\Carbon::parse($item->date)->format('Y-m-d') }}</td>
                              <td>#{{ $item->id }}</td>
                              <td>{{ $item->quantity }}</td>
                            </tr>
                          @endforeach
                        </tbody>
                        <tfoot>
                          <tr>
                            <td class="text-bold">الاجمالي</td>
                            <td></td>
                            <td class="text-bold">{{ $supplieOrdersOutReturn->sum('quantity') }}</td>
                          </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                  <div class="tab-pane" id="supplieSummary">
                    <div class="table-responsive">
                      <table class="table table-bordered text-center">
                        <thead>
                          <tr>
                            <th>التاريخ</th>
                            <th>الألة / الفاتورة</th>
                            <th>النوع</th>
                            <th>الكمية</th>
                            <th>الاجمالي</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                            $supplieSummaryBalance = $supplie->init_quantity;
                          ?>
                          @foreach ($machineSupplies as $item)
                            <tr>
                              <td>{{ Carbon\Carbon::parse($item->date)->format('Y-m-d') }}</td>
                              <td>{{ $item->machine_name }}</td>
                              <td>مستلزم ألة</td>
                              <td>-{{ $item->quantity }}</td>
                              <td>
                                <?php
                                  $supplieSummaryBalance -= $item->quantity;
                                ?>
                                {{ $supplieSummaryBalance }}
                              </td>
                            </tr>
                          @endforeach
                          @foreach ($supplieOrdersIn as $item)
                            <tr>
                              <td>{{ Carbon\Carbon::parse($item->date)->format('Y-m-d') }}</td>
                              <td>#{{ $item->id }}</td>
                              <td>فاتورة بيع</td>
                              <td>-{{ $item->quantity }}</td>
                              <td>
                                <?php
                                  $supplieSummaryBalance -= $item->quantity;
                                ?>
                                {{ $supplieSummaryBalance }}
                              </td>
                            </tr>
                          @endforeach
                          @foreach ($supplieOrdersInReturn as $item)
                            <tr>
                              <td>{{ Carbon\Carbon::parse($item->date)->format('Y-m-d') }}</td>
                              <td>#{{ $item->id }}</td>
                              <td>مرتجع بيع</td>
                              <td>{{ $item->quantity }}</td>
                              <td>
                                <?php
                                  $supplieSummaryBalance += $item->quantity;
                                ?>
                                {{ $supplieSummaryBalance }}
                              </td>
                            </tr>
                          @endforeach
                          @foreach ($supplieOrdersOut as $item)
                            <tr>
                              <td>{{ Carbon\Carbon::parse($item->date)->format('Y-m-d') }}</td>
                              <td>#{{ $item->id }}</td>
                              <td>فاتورة شراء</td>
                              <td>{{ $item->quantity }}</td>
                              <td>
                                <?php
                                  $supplieSummaryBalance += $item->quantity;
                                ?>
                                {{ $supplieSummaryBalance }}
                              </td>
                            </tr>
                          @endforeach
                          @foreach ($supplieOrdersOutReturn as $item)
                            <tr>
                              <td>{{ Carbon\Carbon::parse($item->date)->format('Y-m-d') }}</td>
                              <td>#{{ $item->id }}</td>
                              <td>مرتجع شراء</td>
                              <td>-{{ $item->quantity }}</td>
                              <td>
                                <?php
                                  $supplieSummaryBalance -= $item->quantity;
                                ?>
                                {{ $supplieSummaryBalance }}
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
