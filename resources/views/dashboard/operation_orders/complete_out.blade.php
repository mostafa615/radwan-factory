@extends('layouts.dashboard.app')
@section('css')
<style>
  #supplies+.select2-container {
    overflow: auto !important;
    height: 40px !important;
  }
</style>
@endsection
@section('content')
<div class="content-wrapper">
  <section class="content container-fluid">
    <section class="content-header">
      <h1>استكمال التشغيل الخارجي</h1>
      <ol class="breadcrumb">
        <li><a href=" {{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
        <li> <a href=" {{ route('dashboard.operation_orders.index') }}"> التشغيل الخارجي</a></li>
        <li class="active">استكمال</li>
      </ol>
    </section>
    <section class="content">
      <div class="box box-primary">
        <div class="box-body">
          @include('partials._errors')
          <form action="{{route('dashboard.operation_orders.updateCompleteOut', $resource->id)}}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            {{ method_field('put') }}
            <div class="row">
              {{-- readonly --}}
              <div class="form-group col-md-6">
                <label>@lang('site.machine_types')</label>
                <input type="text" class="form-control" value="{{$resource->machine->MachineType->name}}" readonly>
              </div>

              {{-- readonly --}}
              <div class="form-group col-md-6">
                <label>@lang('site.machine')</label>
                <input type="text" class="form-control" value="{{$resource->machine->name}}" readonly>
              </div>
            </div>

            <div class="row">
                <div class="form-group col-md-4">
                    <label>المستخدمين*</label>
                    <select name="user_id[]" class="form-control select2-js" multiple="">
                      <option disabled>-- اختر من القائمة --</option>
                      @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ in_array($user->id, explode(',', $resource->user_id)) ? 'selected' : '' }}>
                          {{ $user->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>

                  {{-- readonly --}}
                  <div class="form-group col-md-4">
                    <label>@lang('site.supervisor_store')</label>
                    <select name="supervisor_store" class="form-control" readonly>
                      <option value="{{App\User::where('id', $resource->supervisor_store)->first()->id ?? ''}}" selected>{{App\User::where('id', $resource->supervisor_store)->first()->name ?? ''}}</option>
                    </select>
                  </div>

                  <div class="form-group col-md-4">
                    <label>@lang('site.supervisor_process')*</label>
                    <select name="supervisor_process[]" class="form-control select2-js" multiple="">
                      <option disabled>-- اختر من القائمة --</option>
                      @foreach ($admins as $admin)
                        <option value="{{ $admin->id }}" {{ in_array($admin->id, explode(',', $resource->supervisor_process)) ? 'selected' : '' }}>
                          {{ $admin->name }}
                        </option>
                      @endforeach
                    </select>
                  </div>
            </div>

            <div class="row">
              {{-- readonly --}}
              <div class="form-group col-md-4">
                <label>إسم العميل</label>
                <input type="text" class="form-control" value="{{$resource->client_name}}" readonly>
              </div>

              {{-- readonly --}}
              <div class="form-group col-md-4">
                <label>وحدة الخامة المستخدمة</label>
                <input type="text" class="form-control" value="{{$resource->old_item_unit}}" readonly>
              </div>

              {{-- readonly --}}
              <div class="form-group col-md-4">
                <label>وحدة الخامة الناتجة</label>
                <input type="text" class="form-control" value="{{$resource->out_item_unit}}" readonly>
              </div>
            </div>

            {{-- <div class="row">
              <div class="form-group col-md-4">
                <label>اجمالي الطول المستخدم*</label>
                <input type="number" id="total_used_length" step="any" min="0" name="total_used_length"
                  class="form-control" value="{{ old('total_used_length') }}" required>
              </div>
            </div> --}}

            {{-- start table form --}}
            <div class="box">
              <div class="box-body">
                <table class="table text-center " id="table_items">
                  <thead>
                    <tr>
                      <th>@lang('site.operation_supplies')</th>
                      <th>@lang('site.used_item')</th>
                      <th>@lang('site.old_item_quantity')</th>
                      <th>@lang('site.out_item_name')</th>
                      <th>كميه المستلزم</th>
                      {{-- <th>@lang('site.out_quantity')</th> --}}
                      <th>@lang('site.length')</th>
                      <th>@lang('site.width')</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($resource->operationOrderDetails as $item)
                    <input type="text" name="operation_order_detail_id[]" value="{{$item->id}}" hidden>
                    <tr>
                      <td min-width="200px">
                        <div class="form-group">
                          <select name="supplies_id[{{$item->id}}][]" class="form-control select2-js" multiple>
                            @foreach($machineSupplies as $supply)
                            <option data-used="{{$supply->used}}" value="{{$supply->id}}" {{ in_array($supply->supplie_id, explode(',', $item->operation_suplies_id)) ? 'selected' : '' }}>{{$supply->supplie->name}} : المتبقي {{number_format($supply->used, 2, '.', '')}}</option>
                            @endforeach
                          </select>
                        </div>
                      </td>
                      <td>{{$item->item_name}}</td>
                      <td>{{$item->old_item_quantity}}</td>
                      <td>{{$item->out_item_name}}</td>
                      <td>{{$item->quantity}}</td>
                      {{-- <td>0</td> --}}
                      <td>{{$item->length}}</td>
                      <td>{{$item->width}}</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            {{-- end table form --}}

            <div class="row">
              <div class="form-group col-md-6" style="position: relative">
                <label>@lang('site.date')</label>
                <input type="date" class="form-control" value="{{$resource->date}}" readonly>
              </div>

              <div class="form-group col-md-6">
                <label>ملاحظات المخزن</label>
                <input type="text" class="form-control" value="{{$resource->notes2}}" readonly>
              </div>
            </div>

            <div class="row">
              <div class="form-group col-md-12">
                <label>ملاحظات</label>
                <textarea name="notes" class="form-control" rows="3"></textarea>
              </div>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary">استكمال</button>
              <a href="{{route('dashboard.operation_orders.update_is_complete', $resource->id)}}" class="btn btn-danger">تراجع</a>
            </div>
          </form>
        </div><!--end of box-body-->
      </div>
    </section>
  </section>
</div>
@endsection
