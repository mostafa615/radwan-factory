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
      <h1>تقرير جرد مستلزمات المكينة</h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
        <li class="active">تقرير جرد مستلزمات المكينة</li>
      </ol>
    </section>
    <section class="content" style='padding-bottom: 0px !important;'>
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title" style="margin-bottom: 15px">تقرير جرد مستلزمات المكينة
            ({{ App\Machines::find($request->machine_id)->name }})</h3>
        </div>
        <div class="box-body" style="min-height: 250px">
          <div class="row">
            <div class="col-md-12">
              <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                  <li class="active">
                    <a href="#supplies" data-toggle="tab" aria-expanded="false">جرد مستلزمات
                      المكينة</a>
                  </li>
                </ul>
                <div class="tab-content">
                  <div class="tab-pane active" id="supplies">
                    <div class="table-responsive">
                      <table class="table table-bordered" id="machineSupplieTable">
                        <thead>
                          <tr>
                            <td>المستلزم</td>
                            <td>باقي الاستخدام</td>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($machineSupplies as $machineSupply)
                            <tr class="amount-row">
                              <td>{{ $machineSupply->Supplie->name }}</td>
                              <td>{{ number_format($machineSupply->total_used < 1 ? 0 : $machineSupply->total_used, 2, '.', '') }}</td>
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
    $(document).ready(function() {

      $('#machineSupplieTable').DataTable({
        "pageLength": 100,
        "dom": 'frtip',
      });
    });
  </script>
@endsection
