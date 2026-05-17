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
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="content-wrapper">
        <section class="content container-fluid">
            <section class="content-header">
                <h1>أوامر التشغيل الداخلي</h1>
                <ol class="breadcrumb">
                    <li><a href=" {{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                    <li> <a href=" {{ route('dashboard.operation_orders.index') }}">أوامر التشغيل الداخلي</a></li>
                    <li class="active">@lang('site.add')</li>

                </ol>
            </section>
            <section class="content">

                <div class="box box-primary">

                    <div class="box-header">
                        <h3 class="box-title">@lang('site.add')</h3>
                    </div>

                    <div class="box-body">
                        @include('partials._errors')
                        <form action="{{ route('dashboard.operation_orders.store') }}" method="POST"
                            enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('post') }}

                            <div class="row">

                                <div class="form-group col-md-6">
                                    <label>@lang('site.machine_types')</label>
                                    <select name="type" class="form-control  select2-js" id="machineType">
                                        <option value="">@lang('site.machine_types')*</option>
                                        @foreach ($machineTypes as $machineType)
                                            <option value="{{ $machineType->id }}"
                                                {{ old('type') == $machineType->id ? 'selected' : '' }}>
                                                {{ $machineType->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>@lang('site.machine')*</label>
                                    <select name="machine_id" class="form-control  select2-js" id="machines">
                                        <option value="">@lang('site.machine')</option>
                                        {{-- @foreach ($machines as $machine)
                                            <option value="{{$machine->id}}" {{old('machine_id') == $machine->id ? 'selected' : ''}}>{{$machine->name}}</option>
                                        @endforeach --}}
                                    </select>
                                </div>
                            </div>{{-- end row --}}

                            <div class="row">




                                <div class="form-group col-md-4">
                                    <label>المستخدمين*</label>
                                    <select name="user_id[]" class="form-control  select2-js" multiple="">
                                        {{-- <option value="">المستخدمين</option> --}}
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>@lang('site.supervisor_store')*</label>
                                    <select name="supervisor_store[]" class="form-control select2-js" multiple="">
                                        {{-- <option value="">@lang('site.supervisor_store')</option> --}}
                                        @foreach ($supervisor_store as $employee)
                                            <option value="{{ $employee->id }}"
                                                {{ old('supervisor_process') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>@lang('site.supervisor_process')*</label>
                                    <select name="supervisor_process[]" class="form-control  select2-js" multiple="">
                                        {{-- <option value="">@lang('site.supervisor_process')</option> --}}
                                        @foreach ($admins as $employee)
                                            <option value="{{ $employee->id }}"
                                                {{ old('supervisor_process') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }}</option>
                                        @endforeach
                                    </select>
                                </div>



                            </div>{{-- end row --}}


                            {{-- start row --}}
                            <div class="form-group" style="border: 3px solid #33B35A;border-radius: 8px;padding:5px">
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
                                        <label>@lang('site.in_group')*</label>
                                        <select class="form-control  select2-js" id="inGroups">
                                            {{-- <option value="">@lang('site.group')</option>
                                            @foreach ($groups as $group)
                                                <option value="{{$group->id}}" {{old('group_id') == $group->id ? 'selected' : ''}}>{{$group->name}}</option>
                                            @endforeach --}}
                                        </select>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>@lang('site.used_item')*</label>
                                        <select class="form-control  select2-js" id="items">
                                            {{-- <option value="">@lang('site.item')</option> --}}
                                            {{-- @foreach ($items as $item)
                                                <option value="{{$item->id}}" {{old('item_id') == $item->id ? 'selected' : ''}}>{{$item->name}}</option>
                                            @endforeach --}}
                                        </select>
                                    </div>


                                    <div class="form-group col-md-4">
                                        <label>@lang('site.old_item_quantity')*</label>
                                        <input type="number" step="any" min="0" id="oldItemQnt"
                                            class="form-control" value="{{ old('old_item_quantity') }}">
                                    </div>

                                </div>


                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>@lang('site.operation_supplies')*</label>
                                        <select class="form-control  select2-js" multiple="" id="supplies">
                                            {{-- <option value="">@lang('site.operation_supplies')</option> --}}
                                            {{-- @foreach ($supplies as $supplie)
                                                <option value="{{$supplie->id}}" {{old('operation_suplies_id') == $supplie->id ? 'selected' : ''}}>{{$supplie->name}}</option>
                                            @endforeach --}}
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>@lang('site.out_group')*</label>
                                        <select class="form-control  select2-js" id="outGroupId">
                                            {{-- <option value="">@lang('site.out_group')</option>
                                            @foreach ($groups as $group)
                                                <option value="{{$group->id}}" {{old('out_group_id') == $group->id ? 'selected' : ''}}>{{$group->name}}</option>
                                            @endforeach --}}
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>@lang('site.add_to_old_item')*</label>
                                        <select class="form-control  select2-js" id="outItemId">
                                            <option value="">@lang('site.item')</option>
                                            {{-- @foreach ($items as $item)
                                                <option value="{{$item->id}}" {{old('out_item_id') == $item->id ? 'selected' : ''}}>{{$item->name}}</option>
                                            @endforeach --}}
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-2">
                                        <label>@lang('site.out_quantity')*</label>
                                        <input type="number" step="any" min="0" id="out_quantity"
                                            class="form-control" value="{{ old('quantity') }}">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label>عدد ألواح او كويل*</label>
                                        <input type="number" step="any" min="0" id="oldItemSuppQnt"
                                            class="form-control">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>@lang('site.out_name')</label>
                                        <input type="text" id="name" class="form-control"
                                            value="{{ old('out_name') }}">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>@lang('site.out_price')*</label>
                                        <input type="number" id="out_price" class="form-control"
                                            value="{{ old('price') }}">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>طول اللوح او الكويل*</label>
                                        <input type="number" id="length" step="any" min="0"
                                            class="form-control" value="{{ old('length') }}">
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>@lang('site.width')*</label>
                                        <input type="number" id="width" step="any" min="0"
                                            class="form-control" value="{{ old('width') }}">
                                    </div>
                                    {{-- <input type="hidden" name="is_special" value="0"> --}}

                                    <div class="form-group col-md-4">
                                        <label>@lang('site.special')</label>
                                        <div class="custom-control custom-switch material-switch">
                                            <input type="checkbox" class="custom-control-input" id="specialSwitch"
                                                onchange="this.checked? this.value = 1 : this.value = 0">
                                            <label class="custom-control-label" for="specialSwitch"></label>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <button type="button" class="btn btn-block btn-sm btn-info" id="add">
                                        <i class="fa  fa-chevron-down"></i>
                                    </button>
                                </div>

                            </div>{{-- end row  --}}

                            {{-- start table form --}}
                            <div class="box">
                                <div class="box-body">
                                    <table class="table text-center " id="table_items">
                                        <thead>
                                            <tr>
                                                <th>@lang('site.in_group')</th>
                                                <th>@lang('site.used_item')</th>
                                                <th>@lang('site.old_item_quantity')</th>
                                                <th>@lang('site.item_quantity_to_supplies')</th>
                                                <th>@lang('site.supplies')</th>
                                                <th>@lang('site.out_group')</th>
                                                <th>@lang('site.out_item')</th>
                                                <th>@lang('site.out_name')</th>
                                                <th>@lang('site.out_quantity')</th>
                                                <th>@lang('site.out_price')</th>
                                                <th>@lang('site.length')</th>
                                                <th>@lang('site.width')</th>
                                                <th>@lang('site.special')</th>
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
                                <div class="form-group col-md-6" style="position: relative">
                                    <label>@lang('site.date')*</label>
                                    <input type="date" name="date" class="form-control datePicker"
                                        value="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>ملاحظات</label>
                                    <input placeholder='ملاحظات' type="text" name="notes" class="form-control"
                                        value="{{ old('notes') }}">
                                </div>
                            </div>


                            <div class="form-group">
                                <button type="submit" onclick="submitForm(this);" class="btn btn-primary"><i
                                        class="fa fa-plus"></i> @lang('site.add')</button>
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
        function submitForm(btn) {
            // disable the button
            btn.disabled = true;
            // submit the form
            btn.form.submit();
        }
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
                },
                error: function() {
                    console.log('error');
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
                width = $('#width').val(),
                special = $('#specialSwitch').val();

            var quantityToDecrement = length * old_item_supp_quantity;
            //2500
            quantity_used.forEach((e) => {
                if (quantityToDecrement > e.getAttribute('data-used')) {
                    $('#quantity_error').show();
                    $('#quantity_error').delay(3500).fadeOut(350);
                    exit();
                }
            })

            if ($('#specialSwitch').val() == 'on' || $('#specialSwitch').val() == 1) {
                special = 1;
            } else {
                special = 0;
            }

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
                '<td><p class="">' + group_name + '</p><input type="hidden" name="group_id[]" value="' +
                group_id + '"></td>' +
                '<td><p class="">' + item_name +
                '</p><input type="hidden" required="required" name="item_id[]" value="' + item_id + '"></td>' +
                '<td><p >' + old_item_quantity +
                '</p><input type="hidden" required="required" name="old_item_quantity[]" value="' +
                old_item_quantity + '"></td>' +
                '<td><p >' + old_item_supp_quantity +
                '</p><input type="hidden" required="required" name="old_item_supp_quantity[]" value="' +
                old_item_supp_quantity + '"></td>' +
                '<td><p class="">' + supplies_name +
                '</p><input type="hidden" required="required" name="operation_suplies_id[][]" value="' +
                supplies_id + '"></td>' +
                '<td><p>' + out_group_name + '</p><input type="hidden" name="out_group_id[]" value="' +
                out_group_id + '"></td>' +
                '<td><p>' + out_item_name + '</p><input type="hidden" name="out_item_id[]" value="' +
                out_item_id + '"></td>' +
                '<td><p>' + out_name + '</p><input type="hidden" name="out_name[]" value="' + out_name +
                '"></td>' +
                '<td><p>' + out_quantity + '</p><input type="hidden" name="quantity[]" value="' + out_quantity +
                '" id="' + out_item_id + '_' + out_group_id + '_quantity' + '"></td>' +
                '<td><p>' + out_price + '</p><input type="hidden" name="price[]" value="' + out_price +
                '" id="' + out_item_id + '_' + out_group_id + '_price' + '"></td>' +
                '<td><p>' + length + '</p><input type="hidden" name="length[]" value="' + length + '" id="' +
                out_item_id + '_' + out_group_id + '_length' + '"></td>' +
                '<td><p>' + width + '</p> <input type="hidden" name="width[]" value="' + width + '" id="' +
                out_item_id + '_' + out_group_id + '_width' + '"></td>' +
                '<td><p>' + special + '</p> <input type="hidden" name="is_special[]" value="' + special +
                '" id="' + out_item_id + '_' + out_group_id + '_special' + '"></td>' +
                '<td ><button  onclick="$(this).closest(\'tr\').remove()"  class="btn btn-block btn-danger">حذف</button></td>'
            ).appendTo('#table_items');

            $('#inGroups').val(''),
                $('#items').val(''),
                $('#oldItemQnt').val(0),
                $('#oldItemSuppQnt').val(0),

                $('#outGroupId').val(''),
                $('#outItemId').val(''),
                $('#name').val(''),
                $('#out_quantity').val(0),
                $('#out_price').val(0),
                $('#length').val(0);
            $('#width').val(0);
        });

        $(function() {



        });
    </script>
@endsection
