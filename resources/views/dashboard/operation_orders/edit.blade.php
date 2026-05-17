@extends('layouts.dashboard.app')
@section('css')
<style>
  .suppliesSelect+.select2-container {
    width: 100% !important;
  }

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
        @if($operationOrder->out_operation == 0)
        <h1>أوامر التشغيل الداخلي</h1>
        @else
        <h1>أوامر التشغيل الخارجي</h1>
        @endif
        <ol class="breadcrumb">
          <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
        @if($operationOrder->out_operation == 0)
          <li><a href="{{ route('dashboard.operation_orders.index') }}">أوامر التشغيل الداخلي</a></li>
        @else
          <li><a href="{{ route('dashboard.operation_orders.index_out') }}">أوامر التشغيل الخارجي</a></li>
        @endif
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
            <form method="post" action="{{route('dashboard.operation_orders.update', $operationOrder->id)}}">
              {{ csrf_field() }}
              {{ method_field('put') }}
              <div class="row">
                <div class="form-group col-md-12" style="position: relative">
                  <label>@lang('site.date') <span class="text-danger">*</span></label>
                  <input type="date" name="date" class="form-control datePicker" @if(!auth()->user()->hasRole('admin')) readonly @endif value="{{ $operationOrder->date }}">
                  @error('date')<span class="text-danger">{{ $message }}</span>@endif
                </div>
                <div class="form-group col-md-6">
                  <label>@lang('site.machine_types') <span class="text-danger">*</span></label>
                  <select name="machine_type_id" class="form-control select2-js" required>
                    <option value="{{$operationOrder->machine_type_id}}" selected>{{App\MachineTypes::where('id', $operationOrder->machine_type_id)->first()->name ?? '-'}}</option>
                  </select>
                  @error('machine_type_id')<span class="text-danger">{{ $message }}</span>@endif
                </div>
                <div class="form-group col-md-6">
                  <label>@lang('site.machine') <span class="text-danger">*</span></label>
                  <select name="machine_id" class="form-control select2-js" required>
                    <option value="{{$operationOrder->machine_id}}" selected>{{App\Machines::where('id', $operationOrder->machine_id)->first()->name ?? '-'}}</option>
                  </select>
                  @error('machine_id')<span class="text-danger">{{ $message }}</span>@endif
                </div>
                <div class="form-group col-md-4">
                  <label>المستخدمين <span class="text-danger">*</span></label>
                  <select name="user_id[]" class="form-control select2-js" multiple required>
                    @foreach ($users as $user)
                      <option value="{{ $user->id }}"
                        {{ collect($operationOrder->user_id)->contains($user->id) ? 'selected' : '' }}>
                        {{ $user->name }}
                      </option>
                    @endforeach
                  </select>
                  @error('user_id')<span class="text-danger">{{ $message }}</span>@endif
                </div>
                <div class="form-group col-md-4">
                  <label>@lang('site.supervisor_store') <span class="text-danger">*</span></label>
                  <select name="supervisor_store[]" class="form-control select2-js" multiple required>
                    @foreach ($supervisor_store as $employee)
                      <option value="{{ $employee->id }}"
                        {{ collect($operationOrder->supervisor_store)->contains($employee->id) ? 'selected' : '' }}>
                        {{ $employee->name }}
                      </option>
                    @endforeach
                  </select>
                  @error('supervisor_store')<span class="text-danger">{{ $message }}</span>@endif
                </div>
                <div class="form-group col-md-4">
                  <label>@lang('site.supervisor_process') <span class="text-danger">*</span></label>
                  <select name="supervisor_process[]" class="form-control select2-js" multiple required>
                    @foreach ($admins as $employee)
                      <option value="{{ $employee->id }}"
                        {{ collect($operationOrder->supervisor_process)->contains($employee->id) ? 'selected' : '' }}>
                        {{ $employee->name }}
                      </option>
                    @endforeach
                  </select>
                  @error('supervisor_process')<span class="text-danger">{{ $message }}</span>@endif
                </div>
              </div>

              <div class="row" style="border: 3px solid #33B35A; border-radius: 8px; padding:7px; margin:7px;">
                <div class="col-md-12">
                  <div class="form-group">
                    <div id="errors" hidden>
                      <div class="alert alert-danger">
                        من فضلك ادخل كل البيانات للصنف
                      </div>
                    </div>
                    <div id="quantity_error" hidden>
                      <div class="alert alert-danger">
                        انتبه: كميه المستلزم غير صحيحه
                      </div>
                    </div>
                    <div id="old_item_quantity_error" hidden>
                      <div class="alert alert-danger">
                        انتبه: لا يمكن ان تكون كمية الخامة المستخدمة اكبر من المتاحة للألة أو تساوى 0
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group col-md-4">
                        <label>@lang('site.in_group') <span class="text-danger">*</span></label>
                        <select class="form-control select2-js" id="inGroups">
                          {{--  --}}
                        </select>
                      </div>
                      <div class="form-group col-md-4">
                        <label>@lang('site.used_item') <span class="text-danger">*</span></label>
                        <select class="form-control select2-js" id="items">
                          {{--  --}}
                        </select>
                      </div>
                      <div class="form-group col-md-4">
                        <label>@lang('site.old_item_quantity') <span class="text-danger">*</span></label>
                        <input type="number" step="any" min="0" id="oldItemQnt" class="form-control">
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group col-md-4">
                        <label>@lang('site.operation_supplies') <span class="text-danger">*</span></label>
                        <select class="form-control select2-js" multiple="" id="supplies">
                          {{--  --}}
                        </select>
                      </div>
                      <div class="form-group col-md-4">
                        <label>@lang('site.out_group') <span class="text-danger">*</span></label>
                        <select class="form-control select2-js" id="outGroupId">
                          {{--  --}}
                        </select>
                      </div>
                      <div class="form-group col-md-4">
                        <label>@lang('site.add_to_old_item') <span class="text-danger">*</span></label>
                        <select class="form-control  select2-js" id="outItemId">
                          {{--  --}}
                        </select>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group col-md-2">
                        <label>@lang('site.out_quantity') <span class="text-danger">*</span></label>
                        <input type="number" step="any" min="0" id="out_quantity" class="form-control">
                      </div>
                      <div class="form-group col-md-2">
                        <label>عدد ألواح او كويل <span class="text-danger">*</span></label>
                        <input type="number" step="any" min="0" id="oldItemSuppQnt" class="form-control">
                      </div>
                      <div class="form-group col-md-4">
                        <label>@lang('site.out_name')</label>
                        <input type="text" id="name" class="form-control">
                      </div>
                      <div class="form-group col-md-4">
                        <label>@lang('site.out_price') <span class="text-danger">*</span></label>
                        <input type="number" id="out_price" min="0" readonly value="0" class="form-control">
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group col-md-4">
                        <label>طول اللوح او الكويل <span class="text-danger">*</span></label>
                        <input type="number" id="length" step="any" min="0" class="form-control">
                      </div>
                      <div class="form-group col-md-4">
                        <label>@lang('site.width') <span class="text-danger">*</span></label>
                        <input type="number" id="width" step="any" min="0" class="form-control">
                      </div>
                    </div>

                    <div class="form-group">
                      <button type="button" class="btn btn-block btn-sm btn-info" id="addItem" >
                        <i class="fa fa-chevron-down"></i>
                      </button>
                    </div>
                  </div>

                  <div class="table-responsive">
                    <table class="table text-center" id="tableItems" style="white-space: nowrap;">
                      <thead>
                        <tr>
                          <th>@lang('site.in_group')</th>
                          <th>@lang('site.used_item')</th>
                          <th>@lang('site.old_item_quantity')</th>
                          <th>@lang('site.operation_supplies')</th>
                          <th>@lang('site.out_group')</th>
                          <th>@lang('site.add_to_old_item')</th>
                          <th>@lang('site.out_quantity')</th>
                          <th>عدد ألواح او كويل</th>
                          <th>@lang('site.out_name')</th>
                          <th>@lang('site.out_price')</th>
                          <th>طول اللوح او الكويل</th>
                          <th>@lang('site.width')</th>
                          <th>عمليات</th>
                        </tr>
                      </thead>
                      <tbody class="to-append">
                        @if(!empty($operationOrder->operationOrderDetails))
                          @foreach($operationOrder->operationOrderDetails as  $operationOrderDetail)
                            @if(empty($operationOrderDetail->operationOrderResults->toArray()))
                            <?php
                              $itemName = DB::table('items')->where('id', $operationOrderDetail->out_item_id)->first();
                              $machine = DB::table('machines')->where('id', $operationOrder->machine_id)->first();
                              $itemQuantity = DB::table('quantities')->where('ownerable_type', 'App\Models\Store')
                                                                      ->where('ownerable_id', $machine->store_id)
                                                                      ->where('item_id', $operationOrderDetail->item_id)
                                                                      ->first();
                            ?>
                            <tr>
                              <td>
                                {{optional($operationOrderDetail->inGroup)->name}}
                                <input type="hidden" name="operation_order_detail_id[]" value="{{$operationOrderDetail->id}}">
                                <input type="hidden" name="group_id[]" value="{{$operationOrderDetail->group_id}}">
                              </td>
                              <td>
                                {{optional($operationOrderDetail->item)->name}}
                                <input type="hidden" name="item_id[]" value="{{$operationOrderDetail->item_id}}">
                              </td>
                              <td>
                                <input type="number" name="old_item_quantity[]" value="{{$operationOrderDetail->old_item_quantity}}" placeholder="MAX: {{$itemQuantity->quantity??0}}" step="any" min="0" max="{{$itemQuantity->quantity??0}}" required>
                              </td>

                            <td>
                              <select class="form-control select2-js suppliesSelect" name="operation_suplies_id[{{$loop->index}}][]" multiple>
                                @if(!empty($operationOrderDetail->operation_suplies_id))
                                  @foreach(explode(',', $operationOrderDetail->operation_suplies_id) as $operation_suplies_id)
                                    @php
                                      $supplie = DB::table('supplies')->where('id', $operation_suplies_id)->first();
                                      $machineSupplie = DB::table('machine_supplies')->where('machine_id', $operationOrder->machine_id)->where('supplie_id', $operation_suplies_id)->first();
                                    @endphp
                                    @if($supplie)
                                      <option value="{{ $machineSupplie->id }}" selected>
                                        {{ $supplie->name }}
                                      </option>
                                    @endif
                                  @endforeach
                                @endif
                              </select>
                            </td>


                              <td>
                                {{optional($operationOrderDetail->group)->name}}
                                <input type="hidden" name="out_group_id[]" value="{{$operationOrderDetail->out_group_id}}">
                              </td>
                              <td>
                                {{optional($itemName)->name}}
                                <input type="hidden" name="out_item_id[]" value="{{$operationOrderDetail->out_item_id}}">
                              </td>
                              <td>
                                <input type="number" name="quantity[]" value="{{$operationOrderDetail->quantity}}" step="any" min="0" required>
                              </td>
                              <td>
                                <input type="number" name="old_item_supp_quantity[]" value="{{$operationOrderDetail->old_item_supp_quantity}}" step="any" min="0" required>
                              </td>
                              <td>
                                {{$operationOrderDetail->out_name}}
                                <input type="hidden" name="out_name[]" value="{{$operationOrderDetail->out_name}}" readonly required>
                              </td>
                              <td>
                                {{$operationOrderDetail->price}}
                                <input type="hidden" name="price[]" value="{{$operationOrderDetail->price}}" readonly step="any" min="0" required>
                              </td>
                              <td>
                                <input type="number" name="length[]" value="{{$operationOrderDetail->length}}" step="any" min="0" required>
                              </td>
                              <td>
                                <input type="number" name="width[]" value="{{$operationOrderDetail->width}}" step="any" min="0" required>
                              </td>
                              <td>
                                <span><input type="checkbox" name="selected_items[]" value="{{$operationOrderDetail->id}}"> حذف</span>
                              </td>
                            </tr>
                            @endif
                          @endforeach
                        @endif
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="form-group col-md-12">
                  <label>ملاحظات</label>
                  <textarea name="notes" id="notes" class="form-control" rows="5">{{ $operationOrder->notes }}</textarea>
                </div>
              </div>

              <button type="submit" class="btn btn-success editForm" name=" , العمليه رقم {{ $operationOrder->id }}" value="{{ $operationOrder->id }}">
                <i class="fa fa-check"></i> تعديل الان
              </button>
            </form>
          </div>
        </div>
      </section>
    </section>
  </div>
@endsection
@section('scripts')
<script>
  $.ajax({
    url: "{{ route('dashboard.operation_orders.get_inGroups_by_machine') }}?machine_id=" + {{ $operationOrder->machine_id }},
    method: 'GET',
    success: function(data) {
      $('#inGroups').html(data.html);
    }
  });

  $.ajax({
    url: "{{ route('dashboard.operation_orders.get_outGroups_by_machine') }}?machine_id=" + {{ $operationOrder->machine_id }},
    method: 'GET',
    success: function(data) {
      $('#outGroupId').html(data.html);
    }
  });

  $.ajax({
    url: "{{ route('dashboard.operation_orders.get_supplies_by_machine') }}?machine_id=" + {{ $operationOrder->machine_id }},
    method: 'GET',
    success: function(data) {
      $('#supplies').html(data.html);
    },
    error: function() {
      console.log('error');
    }
  });


    $.ajax({
      url: "{{ route('dashboard.operation_orders.get_supplies_by_machine') }}?machine_id=" + {{ $operationOrder->machine_id }},
      method: 'GET',
      success: function (data) {
          var selectElement = $('.suppliesSelect');

        var selectedOptions = @json($selectedSuppliesIds);

          console.log(selectedOptions);

          selectElement.append(data.html);

          selectedOptions = selectedOptions.map(String);


          selectElement.find('option').each(function() {
              if (selectedOptions.includes(String($(this).val()))) {
                  $(this).prop('selected', true);
              }
          });

          selectElement.trigger('change');
      }
  });




  $("#outGroupId").change(function() {
    $.ajax({
      url: "{{ route('dashboard.operation_orders.get_items_by_group') }}?out_group_id=" + $(this).val(),
      method: 'GET',
      success: function(data) {
        $('#outItemId').html(data.html);
      }
    });
  });

  $("#inGroups").change(function() {
    $.ajax({
      url: "{{ route('dashboard.operation_orders.get_items_by_group') }}?out_group_id=" + $(this).val(),
      method: 'GET',
      success: function(data) {
        $('#items').html(data.html);
      }
    });
  });

  $('#outGroupId').on('change', function() {
    $('#name').val($('#outGroupId option:selected').text());
  });

  $("#items").change(function() {
    $.ajax({
      url: "{{ route('dashboard.operation_orders.get_item_quantity') }}?item_id=" + $(this).val() + '&&machine_id=' + {{ $operationOrder->machine_id }},
      method: 'GET',
      success: function(data) {
        $('#oldItemQnt').attr('placeholder', 'الكمية المتاحه للآلة = ' + data.quantity);
        $('#oldItemQnt').attr('max', data.quantity);
      }
    });
  });

  $('#addItem').on('click', function() {
    var group_id = $('#inGroups').val(),
      quantity_used = document.querySelectorAll('#supplies option:checked'),
      group_name = $('#inGroups option:selected').text(),
      item_id = $('#items').val(),
      item_name = $('#items option:selected').text(),
      supplies_id = $('#supplies').val(),
      supplies_name = $('#supplies option:selected').text(),
      old_item_quantity = $('#oldItemQnt').val(),
      old_item_max_quantity = $('#oldItemQnt').attr('max'),
      old_item_supp_quantity = $('#oldItemSuppQnt').val(),
      out_group_id = $('#outGroupId').val(),
      out_group_name = $('#outGroupId option:selected').text(),
      out_item_id = $('#outItemId').val(),
      out_item_name = $('#outItemId option:selected').text(),
      out_quantity = $('#out_quantity').val(),
      out_name = $('#name').val(),
      out_price = $('#out_price').val(),
      length = $('#length').val(),
      width = $('#width').val();

    var quantityToDecrement = length * old_item_supp_quantity;

    quantity_used.forEach((e) => {
      if (quantityToDecrement > e.getAttribute('data-used')) {
        $('#quantity_error').show();
        $('#quantity_error').delay(3500).fadeOut(350);
        exit();
      }
    })

    if (group_id == 0 || group_id == null) {
      $('#errors').show();
      $('#errors').delay(3500).fadeOut(350);
      return false;
    }

    if (out_group_id == 0 || out_group_id == null) {
      $('#errors').show();
      $('#errors').delay(3500).fadeOut(350);
      return false;
    }

    if (item_id == 0 || item_id == null) {
      $('#errors').show();
      $('#errors').delay(3500).fadeOut(350);
      return false;
    }

    if (old_item_quantity == null || old_item_quantity == '') {
      $('#errors').show();
      $('#errors').delay(3500).fadeOut(350);
      return false;
    }

    if (parseFloat(old_item_quantity) == 0) {
      $('#old_item_quantity_error').show();
      $('#old_item_quantity_error').delay(3500).fadeOut(350);
      return false;
    }

    if (parseFloat(old_item_quantity) > parseFloat(old_item_max_quantity)) {
      $('#old_item_quantity_error').show();
      $('#old_item_quantity_error').delay(3500).fadeOut(350);
      return false;
    }

    if (old_item_supp_quantity == 0 || old_item_supp_quantity == null) {
      $('#errors').show();
      $('#errors').delay(3500).fadeOut(350);
      return false;
    }

    if (out_item_id == 0 || out_item_id == null) {
      out_item_id = null;
      out_item_name = '';
    }

    if (supplies_id == 0 || supplies_id == null) {
      supplies_id = null;
      supplies_name = '';
    }

    if (out_quantity <= 0 || out_quantity == null) {
      $('#errors').show();
      $('#errors').delay(3500).fadeOut(350);
      return false;
    }

    if (out_price < 0 || out_price == null) {
      $('#errors').show();
      $('#errors').delay(3500).fadeOut(350);
      return false;
    }

    if (length < 0 || length == null || length == '') {
      $('#errors').show();
      $('#errors').delay(3500).fadeOut(350);
      return false;
    }

    if (width < 0 || width == null || width == '') {
      $('#errors').show();
      $('#errors').delay(3500).fadeOut(350);
      return false;
    }

    $('<tr>').html(
      '<td><p class="">' + group_name +
      '</p><input type="hidden" name="group_id[]" value="' + group_id + '"></td>' +

      '<td><p class="">' + item_name +
      '</p><input type="hidden" name="item_id[]" value="' + item_id + '"></td>' +

      '<td><p >' + old_item_quantity +
      '</p><input type="hidden" name="old_item_quantity[]" value="' + old_item_quantity + '"></td>' +

      '<td><p class="">' + supplies_name +
      '</p><input type="hidden" name="operation_suplies_id[][]" value="' + supplies_id + '"></td>' +

      '<td><p>' + out_group_name +
      '</p><input type="hidden" name="out_group_id[]" value="' + out_group_id + '"></td>' +

      '<td><p>' + out_item_name +
      '</p><input type="hidden" name="out_item_id[]" value="' + out_item_id + '"></td>' +

      '<td><p>' + out_quantity +
      '</p><input type="hidden" name="quantity[]" value="' + out_quantity + '" id="' + out_item_id + '_' + out_group_id + '_quantity' + '"></td>' +

      '<td><p>' + old_item_supp_quantity +
      '</p><input type="hidden" name="old_item_supp_quantity[]" value="' + old_item_supp_quantity + '" id="' + out_item_id + '_' + out_group_id + '_old_item_supp_quantity' + '"></td>' +

      '<td><p>' + out_name +
      '</p><input type="hidden" name="out_name[]" value="' + out_name + '"></td>' +

      '<td><p>' + out_price +
      '</p><input type="hidden" name="price[]" value="' + out_price + '" id="' + out_item_id + '_' + out_group_id + '_price' + '"></td>' +

      '<td><p>' + length +
      '</p><input type="hidden" name="length[]" value="' + length + '" id="' + out_item_id + '_' + out_group_id + '_length' + '"></td>' +

      '<td><p>' + width +
      '</p> <input type="hidden" name="width[]" value="' + width + '" id="' + out_item_id + '_' + out_group_id + '_width' + '"></td>' +

      '<td><button onclick="$(this).closest(\'tr\').remove()" class="btn btn-block btn-danger">حذف</button></td>'
    ).appendTo('#tableItems');
  });
</script>
@endsection
