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
      <h1> التشغيل الخارجي</h1>
      <ol class="breadcrumb">
        <li><a href=" {{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
        <li> <a href=" {{ route('dashboard.operation_orders.index') }}"> التشغيل الخارجي</a></li>
        <li class="active">@lang('site.add_out_operation')</li>
      </ol>
    </section>
    <section class="content">
      <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">@lang('site.add_out_operation')</h3>
        </div>

        <div class="box-body">
          @include('partials._errors')
          <form action="{{ route('dashboard.operation_orders.storeOut') }}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            {{ method_field('post') }}
            <div class="row">

              <div class="form-group col-md-6">
                <label>@lang('site.machine_types')*</label>
                <select name="machine_type_id" class="form-control  select2-js" id="machineType" required>
                  <option value="">@lang('site.machine_types')*</option>
                  @foreach ($machineTypes as $machineType)
                  <option value="{{ $machineType->id }}" {{ old('type')==$machineType->id ? 'selected' : '' }}>{{ $machineType->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="form-group col-md-6">
                <label>@lang('site.machine')*</label>
                <select name="machine_id" class="form-control  select2-js" id="machines" required>
                  <option value="">@lang('site.machine')*</option>
                  @foreach ($machines as $machine)
                  <option value="{{ $machine->id }}" {{ old('machine_id')==$machine->id ? 'selected' : '' }}>{{ $machine->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="row">
              {{-- readonly --}}
              <div class="form-group col-md-4">
                <label>المستخدمين</label>
                <input type="text" class="form-control" readonly>
              </div>

              {{-- readonly --}}
              <div class="form-group col-md-4">
                <label>@lang('site.supervisor_store')</label>
                <select name="supervisor_store" class="form-control" readonly>
                  <option value="{{auth()->user()->id}}" selected>{{auth()->user()->name}}</option>
                </select>
              </div>

              {{-- readonly --}}
              <div class="form-group col-md-4">
                <label>@lang('site.supervisor_process')</label>
                <input type="text" class="form-control" readonly>
              </div>

              <!--<div class="form-group col-md-3">-->
              <!--    <label>@lang('site.employees')</label>-->
              <!--    <select name="employee_id[]" class="form-control  select2-js" multiple="">-->
              <!--        {{-- <option value="">@lang('site.employees')</option> --}}-->
              <!--        @foreach ($employees as $employee)
    -->
              <!--            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>-->
              <!--
    @endforeach-->
              <!--    </select>-->
              <!--</div>-->
            </div>

            <div class="row">
              <div class="form-group col-md-4">
                <label>إسم العميل*</label>
                <select name="client_name" class="form-control  select2-js">
                  <option value="" disabled>العميل</option>
                  @foreach ($clients as $client)
                  <option value="{{ $client->name }}" {{ old('client_name')==$client->name ? 'selected' : '' }}> {{ $client->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="form-group col-md-4">
                <label>وحدة الخامة المستخدمة*</label>
                <input type="text" id="old_item_unit" name="old_item_unit" class="form-control" value="{{ old('old_item_unit') }}">
              </div>

              <div class="form-group col-md-4">
                <label>وحدة الخامة الناتجة*</label>
                <input type="text" id="out_item_unit" name="out_item_unit" class="form-control" value="{{ old('out_item_unit') }}">
              </div>
            </div>


            {{-- start box --}}
            <div class="form-group" style="border: 3px solid #33B35A;border-radius: 8px;padding:5px">
              <div id="errors" hidden>
                <div class="alert alert-danger">
                  من فضلك ادخل كل البيانات للصنف
                </div>
              </div>

              <div id="quantity_error" hidden>
                <div class="alert alert-danger">
                  كميه المستلزم غير صحيحه
                </div>
              </div>

              <div class="row">
                <div class="form-group col-md-4">
                  <label>@lang('site.operation_supplies')</label>
                  <select class="form-control" readonly>
                    {{-- <option value="">@lang('site.operation_supplies')</option> --}}
                    {{-- @foreach ($supplies as $supplie)
                    <option value="{{$supplie->id}}" {{old('operation_suplies_id')==$supplie->id ? 'selected' :
                      ''}}>{{$supplie->name}}</option>
                    @endforeach --}}
                  </select>
                </div>

                <div class="form-group  col-md-4">
                  <label>@lang('site.used_item')*</label>
                  <input type="text" id="item_name" class="form-control">
                </div>

                <div class="form-group col-md-4">
                  <label>@lang('site.old_item_quantity')*</label>
                  <input type="number" id="old_item_quantity" class="form-control">
                </div>
              </div>{{-- end row --}}

              <div class="row">
                <div class="form-group  col-md-4">
                  <label>@lang('site.out_item_name')*</label>
                  <input type="text" id="out_item_name" class="form-control">
                </div>

                <div class="form-group col-md-4">
                  <label>عدد ألواح او كويل*</label>
                  <input type="number" id="out_quantity" class="form-control">
                </div>

                <div class="form-group col-md-4">
                  <label>@lang('site.out_quantity')*</label>
                  <input type="number" id="oldItemSuppQnt" class="form-control">
                </div>
              </div>{{-- end row --}}

              <div class="row">
                <div class="form-group col-md-4">
                  <label>طول ألواح او كويل*</label>
                  <input type="number" step="any" min="0" id="length" class="form-control">
                </div>

                <div class="form-group col-md-4">
                  <label>@lang('site.width')*</label>
                  <input type="number" step="any" min="0" id="width" class="form-control">
                </div>
              </div>

              <div class="form-group">
                <button type="button" class="btn btn-block btn-sm btn-info" id="add">
                  <i class="fa  fa-chevron-down"></i>
                </button>
              </div>
            </div>
            {{-- end box --}}

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
                      <th>@lang('site.out_quantity')</th>
                      <th>@lang('site.length')</th>
                      <th>@lang('site.width')</th>
                      <th>حذف</th>
                    </tr>
                  </thead>
                  <tbody class="to-append">
                  </tbody>
                </table>
              </div>
            </div>
            {{-- end table form --}}

            <div class="row">
              <div class="form-group col-md-6">
                <label>عاملين الاستلام*</label>
                <select name="store_employees[]" class="form-control select2-js" multiple="" required>
                  <option disabled>-- اختر من القائمة --</option>
                  @foreach(App\Employee::where('job_id', 2)->where('branch_id', auth()->user()->branch_id)->get() as $employee)
                  <option value="{{$employee->id}}">{{ $employee->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="form-group col-md-6" style="position: relative">
                <label>@lang('site.date')*</label>
                <input type="date" name="date" class="form-control datePicker" value="<?php echo date('Y-m-d'); ?>">
              </div>
            </div>

            <div class="row">
              <div class="form-group col-md-12">
                <label>ملاحظات</label>
                <textarea name="notes2" class="form-control" rows="3"></textarea>
              </div>
            </div>

            <div class="form-group">
              <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i>
                @lang('site.add')</button>
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
        $("#machineType").change(function() {
            $.ajax({
                url: "{{ route('dashboard.machines.get_machines_by_type') }}?type=" + $(this).val(),
                method: 'GET',
                success: function(data) {
                    $('#machines').html(data.html);
                }
            });

        });
        $("#machines").change(function() {
            // $.ajax({
            //     url: "{{ route('dashboard.operation_orders.get_items_by_machine') }}?machine_id=" + $(this).val(),
            //     method: 'GET',
            //     success: function(data) {
            //         $('#items').html(data.html);
            //     }
            // });
            $.ajax({
                url: "{{ route('dashboard.operation_orders.get_inGroups_by_machine') }}?machine_id=" + $(
                    this).val(),
                method: 'GET',
                success: function(data) {
                    $('#inGroups').html(data.html);
                }
            });
            $.ajax({
                url: "{{ route('dashboard.operation_orders.get_outGroups_by_machine') }}?machine_id=" + $(
                    this).val(),
                method: 'GET',
                success: function(data) {
                    $('#outGroupId').html(data.html);
                }
            });
            $.ajax({
                url: "{{ route('dashboard.operation_orders.get_supplies_by_machine') }}?machine_id=" + $(
                    this).val(),
                method: 'GET',
                success: function(data) {
                    $('#supplies').html(data.html);
                }
            });
        });
        $("#outGroupId").change(function() {
            $.ajax({
                url: "{{ route('dashboard.operation_orders.get_items_by_group') }}?out_group_id=" + $(this)
                    .val(),
                method: 'GET',
                success: function(data) {
                    $('#outItemId').html(data.html);
                }
            });
        });
        $("#inGroups").change(function() {
            $.ajax({
                url: "{{ route('dashboard.operation_orders.get_items_by_group') }}?out_group_id=" + $(this)
                    .val(),
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
                url: "{{ route('dashboard.operation_orders.get_item_quantity') }}?item_id=" + $(this)
                    .val() + '&&machine_id=' + $('#machines').val(),
                method: 'GET',
                success: function(data) {
                    $('#oldItemQnt').attr('placeholder', '  الكمية المتاحه للآلة = ' + data.quantity);
                    $('#oldItemQnt').attr('max', data.quantity);
                }
            });
        });


        $('#add').on('click', function() {

            var supplies_id = $('#supplies').val(),
                quantity_used = document.querySelectorAll('#supplies option:checked'),
                supplies_name = $('#supplies option:selected').text(),
                item_name = $('#item_name').val(),
                old_item_quantity = $('#old_item_quantity').val(),
                old_item_supp_quantity = $('#oldItemSuppQnt').val(),
                out_item_name = $('#out_item_name').val(),
                out_quantity = $('#out_quantity').val(),
                length = $('#length').val(),
                width = $('#width').val();

            console.log(out_quantity);
            var quantityToDecrement = length * out_quantity;
            //2500
            quantity_used.forEach((e) => {
                if (quantityToDecrement > e.getAttribute('data-used')) {
                    $('#quantity_error').show();
                    $('#quantity_error').delay(3500).fadeOut(350);
                    exit();
                }
            })
            // if (supplies_id == 0 || supplies_id == null) {
            //     $('#errors').show();
            //     $('#errors').delay(3500).fadeOut(350);
            //     return false;
            // }
            if (old_item_quantity <= 0 || old_item_quantity == null) {
                $('#errors').show();
                $('#errors').delay(3500).fadeOut(350);
                return false;
            }
            if (item_name == '' || item_name == null) {
                $('#errors').show();
                $('#errors').delay(3500).fadeOut(350);
                return false;
            }
            if (out_quantity <= 0 || out_quantity == null) {
                $('#errors').show();
                $('#errors').delay(3500).fadeOut(350);
                return false;
            }
            if (old_item_supp_quantity <= 0 || old_item_supp_quantity == null) {
                $('#errors').show();
                $('#errors').delay(3500).fadeOut(350);
                return false;
            }
            if (out_item_name == '' || out_item_name == null) {
                $('#errors').show();
                $('#errors').delay(3500).fadeOut(350);
                return false;
            }
            if (length <= 0 || length == null) {
                $('#errors').show();
                $('#errors').delay(3500).fadeOut(350);
                return false;
            }
            if (width <= 0 || width == null) {
                $('#errors').show();
                $('#errors').delay(3500).fadeOut(350);
                return false;
            }

            $('<tr>').html(
                '<td><p class="">' + supplies_name +
                '</p><input type="hidden" required="required" name="operation_suplies_id[][]" value="' +
                supplies_id + '"></td>' +
                '<td><p>' + item_name + '</p><input type="hidden" name="item_name[]" value="' + item_name +
                '"></td>' +
                '<td><p>' + old_item_quantity + '</p><input type="hidden" name="old_item_quantity[]" value="' +
                old_item_quantity + '"></td>' +
                '<td><p>' + out_item_name + '</p><input type="hidden" name="out_item_name[]" value="' +
                out_item_name + '"></td>' +
                '<td><p>' + out_quantity + '</p><input type="hidden" name="quantity[]" value="' + out_quantity +
                '"></td>' +
                '<td><p>' + old_item_supp_quantity + '</p><input type="hidden" name="out_quantity[]" value="' +
                old_item_supp_quantity + '"></td>' +
                '<td><p>' + length + '</p><input type="hidden" name="length[]" value="' + length + '"></td>' +
                '<td><p>' + width + '</p> <input type="hidden" name="width[]" value="' + width + '"></td>' +
                '<td ><button  onclick="$(this).closest(\'tr\').remove()"  class="btn btn-block btn-danger">حذف</button></td>'
            ).appendTo('#table_items');

            $('#supplies').val(''),
                $('#out_item_name').val(''),
                $('#item_name').val(''),
                $('#oldItemSuppQnt').val(0),
                $('#out_quantity').val(0),
                $('#old_item_quantity').val(0),
                $('#length').val(0);
            $('#width').val(0);
        });

        $(function() {



        });
    </script>
@endsection
