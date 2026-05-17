@extends('layouts.dashboard.app')
@section('content')
<div class="content-wrapper">
  <section class="content container-fluid">
    <section class="content-header">
      <h1>أوامر التشغيل {{$operationOrder->out_operation == 1 ? "الخارجية" : "الداخلية"}}</h1>
      <ol class="breadcrumb">
        <li><a href="{{route('dashboard.index')}}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
        <li><a href="{{route('dashboard.operation_orders.index')}}">أوامر التشغيل {{$operationOrder->out_operation == 1 ? "الخارجية" : "الداخلية"}}</a></li>
        <li class="active">@lang('site.edit')</li>
      </ol>
    </section>
    <section class="content">
      <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">@lang('site.edit')</h3>
        </div>

        <div class="box-body">
          @include('partials._errors')
          <form action="{{route('dashboard.operation_orders.update', $operationOrder)}}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            {{ method_field('put') }}
            <div class="row">
              <div class="form-group col-md-4">
                <label>@lang('site.machine_types')</label>
                <select class="form-control  select2-js" id="machineType">
                  <option value="">-- اختر من القائمة --</option>
                  @foreach($machineTypes as $machineType)
                  <option value="{{$machineType->id}}">{{$machineType->name}}</option>
                  @endforeach
                </select>
                <p style="font-size: 12px; color: #dd4b39;">{{App\MachineTypes::where('id', App\Machines::where('id', $operationOrder->machine_id)->first()->type)->first()->name ?? '-'}}</p>
              </div>

              <div class="form-group col-md-4">
                <label>@lang('site.machine')</label>
                <select name="machine_id" id="machines" class="form-control  select2-js">
                  <option value="">-- اختر من القائمة --</option>
                  @foreach($machines as $machine)
                  <option value="{{$machine->id}}">{{$machine->name}}</option>
                  @endforeach
                </select>
                <p style="font-size: 12px; color: #dd4b39;">{{App\Machines::where('id', $operationOrder->machine_id)->first()->name ?? '-'}}</p>
              </div>

              <div class="form-group col-md-4">
                <label>@lang('site.related_operation_order')</label>
                <select name="related_operat_ord_id" class="form-control  select2-js">
                  <option value="">-- اختر من القائمة --</option>
                  @foreach($relatedOperationOrders as $relatedOperationOrder)
                  <option value="{{$relatedOperationOrder->id}}" {{ $operationOrder->related_operat_ord_id == $relatedOperationOrder->id ? 'selected' : ''}}>{{$relatedOperationOrder->id}}</option>
                  @endforeach
                </select>
              </div>
            </div>{{-- end row --}}

            <div class="row">
              <div class="form-group col-md-4">
                <label>المستخدمين</label>
                <select name="user_id[]" class="form-control select2-js" multiple="">
                  <option value="">-- اختر من القائمة --</option>
                  @foreach($users as $user)
                  <option value="{{$user->id}}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                  @endforeach
                </select>
                <p style="font-size: 12px; color: #dd4b39;">{{App\User::where('id', $operationOrder->user_id)->first()->name ?? '-'}}</p>
              </div>

              <div class="form-group col-md-4">
                <label>مشرف مخزن</label>
                <select name="supervisor_store[]" class="form-control select2-js" multiple="">
                  <option value="">-- اختر من القائمة --</option>
                  @foreach($supervisor_store as $user)
                  <option value="{{$user->id}}" {{ old('supervisor_store') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                  @endforeach
                </select>
                <p style="font-size: 12px; color: #dd4b39;">{{App\User::where('id', $operationOrder->supervisor_store)->first()->name ?? '-'}}</p>
              </div>

              <div class="form-group col-md-4">
                <label>مشرف انتاج</label>
                <select name="supervisor_process[]" class="form-control select2-js" multiple="">
                  <option value="">-- اختر من القائمة --</option>
                  @foreach($admins as $employee)
                  <option value="{{$employee->id}}" {{ old('supervisor_process') == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                  @endforeach
                </select>
                <p style="font-size: 12px; color: #dd4b39;">{{App\User::where('id', $operationOrder->supervisor_process)->first()->name ?? '-'}}</p>
              </div>
            </div>{{-- end row --}}

            {{-- start table form --}}
            <div class="box">
              <div class="box-body">
                <table class="table text-center " id="table_items">
                  <thead>
                    <tr>
                      <th>تحديد للحذف</th>
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
                    @foreach($operationOrder->operationOrderDetails as $item)
                    <tr>
                      <td><input type="checkbox" name="selected_items[]" value="{{$item->id}}"></td>
                      <td>{{optional($item->item)->name}}</td>
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
              <div class="form-group col-md-5" style="position: relative">
                <label>@lang('site.date')</label>
                <input type="date" name="date" class="form-control" value="{{$operationOrder->date}}">
              </div>
              <div class="form-group col-md-12">
                <label>ملاحظات</label>
                <textarea name="notes" rows="4" class="form-control">{{$operationOrder->notes}}</textarea>
              </div>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary"><i class="fa fa-edit"></i> @lang('site.edit')</button>
            </div>
          </form>
        </div><!--end of box-body-->
      </div>
    </section>
  </section>
</div>
@endsection
@section('scripts')
<script>
    $("#machineType").change(function(){
            $.ajax({
                url: "{{ route('dashboard.machines.get_machines_by_type') }}?type=" + $(this).val(),
                method: 'GET',
                success: function(data) {
                    $('#machines').html(data.html);
                }
            });

    });
    $("#machines").change(function(){
            $.ajax({
                url: "{{ route('dashboard.operation_orders.get_items_by_machine') }}?machine_id=" + $(this).val(),
                method: 'GET',
                success: function(data) {
                    $('#items').html(data.html);
                }
            });
            $.ajax({
                url: "{{ route('dashboard.operation_orders.get_supplies_by_machine') }}?machine_id=" + $(this).val(),
                method: 'GET',
                success: function(data) {
                    $('#supplies').html(data.html);
                }
            });
    });
    $("#outGroupId").change(function(){
            $.ajax({
                url: "{{ route('dashboard.operation_orders.get_items_by_group') }}?out_group_id=" + $(this).val(),
                method: 'GET',
                success: function(data) {
                    $('#outItemId').html(data.html);
                }
            });

    });

    $('#outGroupId').on('change', function () {
        $('#name').val($('#outGroupId option:selected').text());
    });

    $("#items").change(function(){
            $.ajax({
                url: "{{ route('dashboard.operation_orders.get_machineitem_quantity') }}?item_id=" + $(this).val() + '&&machine_id=' + $('#machines').val(),
                method: 'GET',
                success: function(data) {
                    $('#oldItemQnt').attr('placeholder', '  الكمية المتاحه للآلة = ' + data.quantity);
                    $('#oldItemQnt').attr('max', data.quantity);
                }
            });
    });

    $(function(){



    });

</script>
@endsection