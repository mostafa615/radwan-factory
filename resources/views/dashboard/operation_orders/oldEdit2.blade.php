@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">
        <section class="content container-fluid">
            <section class="content-header">
                <h1>أوامر التشغيل</h1>
                <ol class="breadcrumb">
                    <li><a href=" {{route('dashboard.index')}}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                    <li> <a href=" {{route('dashboard.operation_orders.index')}}">أوامر التشغيل</a></li>
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
                                    <label>@lang('site.related_operation_order')</label>
                                    <select name="related_operat_ord_id" class="form-control  select2-js">
                                        <option value="">@lang('site.operation_order')</option>
                                        @foreach ($relatedOperationOrders as $relatedOperationOrder)
                                            <option value="{{$relatedOperationOrder->id}}" {{ $operationOrder->related_operat_ord_id == $relatedOperationOrder->id ? 'selected' : ''}}>{{$relatedOperationOrder->id}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>@lang('site.machine_types')</label>
                                    <select name="type" class="form-control  select2-js" id="machineType">
                                        <option value="">@lang('site.machine_types')</option>
                                        @foreach ($machineTypes as $machineType)
                                            <option value="{{$machineType->id}}" {{old('type') == $machineType->id ? 'selected' : ''}}>{{$machineType->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>@lang('site.machine')*</label>
                                    <select name="machine_id" id="machines" class="form-control  select2-js">
                                        <option value="">@lang('site.machine')</option>
                                        @foreach ($machines as $machine)
                                            <option value="{{$machine->id}}" {{$operationOrder->machine_id == $machine->id ? 'selected' : ''}}>{{$machine->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>{{-- end row --}}

                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>@lang('site.supervisor')*</label>
                                    <select name="supervisor_id" class="form-control  select2-js">
                                        <option value="">@lang('site.supervisor')</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{$employee->id}}" {{$operationOrder->supervisor_id == $employee->id ? 'selected' : ''}}>{{$employee->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>@lang('site.employees')*</label>
                                    <select name="employee_id[]" class="form-control  select2-js" multiple="">
                                        {{-- <option value="">@lang('site.employees')</option> --}}
                                        @foreach ($employees as $employee)
                                            <option value="{{$employee->id}}" {{in_array($employee->id, explode(',', $operationOrder->employee_id)) ? 'selected' : ''}}>{{$employee->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>المستخدمين*</label>
                                    <select name="user_id[]" class="form-control  select2-js" multiple="">
                                        <option value="">المستخدمين</option>
                                        @foreach ($users as $user)
                                            <option value="{{$user->id}}" {{in_array($user->id, explode(',', $operationOrder->user_id)) ? 'selected' : ''}}>{{$user->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>{{-- end row --}}


                            {{-- start table form --}}
                            <div class="box">
                                <div class="box-body">
                                    <table class="table text-center " id="table_items">
                                        <thead>
                                        <tr>
                                            <th>@lang('site.item')</th>
                                            <th>@lang('site.old_item_quantity')</th>
                                            <th>@lang('site.operation_supplies')</th>
                                            <th>@lang('site.out_group')</th>
                                            <th>@lang('site.add_to_old_item')</th>
                                            <th>@lang('site.out_name')</th>
                                            <th>@lang('site.out_price')</th>
                                            <th>@lang('site.length')</th>
                                            <th>@lang('site.width')</th>
                                            <th>@lang('site.quantity')</th>
                                            <th>@lang('site.is_special')</th>
                                            <th>حذف</th>
                                        </tr>
                                        </thead>
                                        <tbody class="">
                                            @if (!@empty($operationOrder->operationOrderDetails))
                                                @foreach ($operationOrder->operationOrderDetails as $operationOrderDetail)
                                                <?php
                                                // dd($operationOrder->operationOrderDetails);
                                                    $operationDetailSupliesIds = explode(',', $operationOrderDetail->operation_suplies_id);
                                                    $operationDetailSupliesNames = App\Supplies::whereIn('id', $operationDetailSupliesIds)->pluck('name')->toArray();
                                                    $itemName = App\Item::where('id',$operationOrderDetail->out_item_id )->first();
                                                ?>
                                                <tr>
                                                    <td>{{optional($operationOrderDetail->item)->name}}</td>
                                                    <td>{{$operationOrderDetail->old_item_quantity}}</td>
                                                    <td>{{implode(' , ',$operationDetailSupliesNames)}}</td>
                                                    <td>{{optional($operationOrderDetail->group)->name}}</td>
                                                    <td>{{optional($itemName)->name}}</td>
                                                    <td>{{$operationOrderDetail->out_name}}</td>
                                                    <td>{{$operationOrderDetail->price}}</td>
                                                    <td>{{$operationOrderDetail->length}}</td>
                                                    <td>{{$operationOrderDetail->width}}</td>
                                                    <td>{{$operationOrderDetail->quantity}}</td>
                                                    <td>{{$operationOrderDetail->is_special}}</td>
                                                    @if(Auth()->user()->id == 1)
                                                        <td><a href="{{url('dashboard/order_delete_detail/'.$operationOrderDetail->id)}}" class="btn btn-danger"><i class="fa fa-trash-o"></i> </a> </td>
                                                    @endif
                                                </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            {{-- end table form --}}


                            <div class="row">
                                <div class="form-group col-md-6" style="position: relative">
                                    <label>@lang('site.date')*</label>
                                    <input type="date" name="date" class="form-control" value="{{$operationOrder->date}}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>ملاحظات</label>
                                    <input placeholder='ملاحظات' type="text" name="notes" class="form-control" value="{{$operationOrder->notes}}">
                                </div>
                            </div>
                            {{-- end row --}}


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
