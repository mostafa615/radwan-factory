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
    <h1>تقرير حركة المستلزم</h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
      <li class="active">تقرير حركة المستلزم</li>
    </ol>
  </section>

  <section class="content" style='padding-bottom: 0px !important;'>
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title" style="margin-bottom: 15px">تقرير حركة المستلزم ( {{ optional($spplie)->name }} )
        </h3>
        <center>
          <h3 class="box-title" style="margin-bottom: 15px">من {{ $request['date_from'] }}</h3>
          <h3 class="box-title" style="margin-bottom: 15px">الى {{ $request['date_to'] }}</h3>
        </center>
      </div>

      <div class="box-body" style="min-height: 250px">
        <div class="row">
          <div class="col-md-12">
            <div class="nav-tabs-custom">
              <ul class="nav nav-tabs">
                <li class="active">
                  <a href="#machine_supplies" data-toggle="tab" aria-expanded="false">حركة
                    المستلزم</a>
                </li>
              </ul>
              <div class="tab-content">
                <div class="tab-pane active" id="machine_supplies">
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr class="text-bold">
                          <td>الألة/الفاتورة</td>
                          <td>المستلزم</td>
                          <td>التاريخ</td>
                          <td>الكمية</td>
                          <td>الرصيد</td>
                          <td>ملاحظات</td>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $amountItem = $openingBalance; ?>
                        @foreach($machineSupplies as $machineSupply)
                        <?php $amountItem -= $machineSupply->quantity; ?>
                        <tr data-amount="-{{ $machineSupply->quantity }}" class="amount-row">
                          <td>{{ $machineSupply->Machine->name }}</td>
                          <td>{{ $machineSupply->Supplie->name }}</td>
                          <td>{{ $machineSupply->date }}</td>
                          <td>{{ $machineSupply->quantity }}</td>
                          <td class="balance-col">{{ $amountItem }}</td>
                          <td>{{ $machineSupply->notes }}</td>
                        </tr>
                        @endforeach
                        
                        @foreach($supplyOrders as $supplyOrder)
                        <?php
                          $order = DB::table('orders')->where('id', $supplyOrder->order_id)->first();
                          $amountChange = $order->role == 'return-out' ? -$supplyOrder->quantity : $supplyOrder->quantity;
                          $amountItem += $amountChange;
                        ?>
                        <tr data-amount="{{ $amountChange }}" class="amount-row">
                          <td>
                            {{ $order->role == 'return-out' ? 'مرتجع شراء' : 'شراء' }}
                          </td>
                          <td>{{ $spplie->name }}</td>
                          <td>{{ \Carbon\Carbon::parse($supplyOrder->created_at)->format('Y-m-d') }}</td>
                          <td>{{ $supplyOrder->quantity }}</td>
                          <td class="balance-col">{{ $amountItem }}</td>
                          <td>{{ $supplyOrder->notes }}</td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-12">
            <div class="box">
              <div class="box-body">
                <table class="table table-bordered">
                  <thead>
                    <tr class="text-bold">
                      <td>المسلزم</td>
                      <td>الرصيد الافتتاحي </td>
                      <td>الرصيد الحالي</td>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>{{ optional($spplie)->name }}</td>
                      <td>{{ $openingBalance }}</td>
                      <td>{{ $currentBalance }}</td>
                    </tr>
                  </tbody>
                </table>
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
  var initValue = {{ $openingBalance }};

  function calculateBalance() {
    setTimeout(function () {
      var initV = parseFloat({{ $openingBalance }});
  $('.amount-row').each(function () {
    var amount = $(this).attr('data-amount');
    initV += parseFloat(amount);
    $(this).find('.balance-col').text(initV.toFixed(2));
  });
  console.log('done');
            }, 1000);
        }

  $(function () {
    $('select').select2({
      width: '100%'
    });
  });
  $(document).ready(function () {
    calculateBalance();

    $('#machineSupplieTable').DataTable({
      "pageLength": 100,
      "dom": 'lBfrtip',
    });
  });
</script>
@endsection