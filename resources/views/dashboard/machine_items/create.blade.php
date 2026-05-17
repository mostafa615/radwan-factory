@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">
        <section class="content container-fluid">
            <section class="content-header">
                <h1>صرف اصناف للآلات</h1>
                <ol class="breadcrumb">
                    <li><a href=" {{route('dashboard.index')}}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                    <li> <a href=" {{route('dashboard.machine_items.index')}}">صرف اصناف للآلات</a></li>
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
                        <form action="{{route('dashboard.machine_items.store')}}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('post') }}

                            <div class="form-group">
                                <label>@lang('site.machine_types')*</label>
                                <select name="type" class="form-control  select2-js" id="machineType">
                                    <option value="">@lang('site.machine_types')</option>
                                    @foreach ($machineTypes as $machineType)
                                        <option value="{{$machineType->id}}" {{old('type') == $machineType->id ? 'selected' : ''}}>{{$machineType->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>@lang('site.machine')*</label>
                                <select name="machine_id" class="form-control  select2-js" id="machines">
                                    <option value="">@lang('site.machine')</option>
                                    @foreach ($machines as $machine)
                                        <option value="{{$machine->id}}" {{old('machine_id') == $machine->id ? 'selected' : ''}}>{{$machine->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>@lang('site.group')*</label>
                                <select name="group_id" class="form-control  select2-js" id="groupId">
                                    <option value="">@lang('site.group')</option>
                                    @foreach ($groups as $group)
                                        <option value="{{$group->id}}" {{old('group_id') == $group->id ? 'selected' : ''}}>{{$group->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>@lang('site.item')*</label>
                                <select name="item_id" class="form-control  select2-js" id="itemId">
                                    {{-- <option value="">@lang('site.item')</option>
                                    @foreach ($items as $item)
                                        <option value="{{$item->id}}" {{old('item_id') == $item->id ? 'selected' : ''}}>{{$item->name}}</option>
                                    @endforeach --}}
                                </select>
                            </div>

                            <div class="form-group">
                                <label>@lang('site.quantity')*</label>
                                <input type="number" id="quantity" min="0" name="quantity" class="form-control" value="{{old('quantity')}}">
                            </div>

                            <div class="form-group" style="position: relative">
                                <label>@lang('site.date')*</label>
                                <input type="date" name="date" class="form-control datePicker" value="<?php echo date('Y-m-d'); ?>">
                            </div>


                            <div class="form-group">
                                <label>ملاحظات</label>
                                <input placeholder='ملاحظات' type="text" name="notes" class="form-control" value="{{old('notes')}}">
                            </div>


                            <div class="form-group">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> @lang('site.add')</button>
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
    $("#groupId").change(function(){
            $.ajax({
                url: "{{ route('dashboard.operation_orders.get_items_by_group') }}?out_group_id=" + $(this).val(),
                method: 'GET',
                success: function(data) {
                    $('#itemId').html(data.html);
                }
            });
    });
    $("#itemId").change(function(){
            $.ajax({
                url: "{{ route('dashboard.machine_items.get_item_quantity') }}?item_id=" + $(this).val() + '&&machine_id=' + $('#machines').val(),
                method: 'GET',
                success: function(data) {
                    $('#quantity').attr('placeholder', '  الكمية المتاحه بالمخزن = ' + data.quantity);
                    $('#quantity').attr('max', data.quantity);
                }
            });
    });

    $(function(){
        $('select').select2({
            width: '100%'
        });
    });

</script>

@endsection
