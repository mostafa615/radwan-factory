@if ($type == 'confirm_notes')
@elseif ($type == 'action')
    <div style="margin-bottom: 6px;">
        @if (Auth::user()->can('reade_operation_orders'))
            <a target="_blank" href="{{ route('dashboard.operation_orders.show', $operationOrder->id) }}"
                class="btn btn-primary btn-sm"><i class="fa fa-print"></i></a>
            <a target="_blank" href="{{ route('dashboard.operation_orders.showStore', $operationOrder->id) }}"
                class="btn btn-success btn-sm"><i class="fa fa-print"></i></a>
        @endif


        @php
            // تعيين activeStatus لكل operationOrder
            $operationOrder->activeStatus = \App\OperationOrderDetail::where(
                'operation_order_id',
                $operationOrder->id
            )->value('active');
        @endphp


            @if($operationOrder->out_operation == 0 && Auth::user()->has_edit_operation_order == 1)
            <a href="{{ route('dashboard.operation_orders.edit', $operationOrder->id) }}"
                class="btn btn-warning btn-sm btn_edit  {{ $operationOrder->activeStatus == 1 ? 'disabled' : '' }}"
                id="editBtn_{{ $operationOrder->id }}"
                @if ($operationOrder->activeStatus == 1) disabled style="pointer-events: none; opacity: 0.5;" @endif>
                <i class="fa fa-edit"></i>
            </a>
            @elseif(
                $operationOrder->out_operation == 1 &&
                Auth::user()->has_edit_operation_order_out == 1 &&
                (
                    (Auth::user()->id == $operationOrder->supervisor_store && $operationOrder->is_complete == -1) ||
                    (Auth::user()->id ==$operationOrder->created_by  && $operationOrder->machine_edit == 1)
                )
            )
            <a href="{{ route('dashboard.operation_orders.edit_out', $operationOrder->id) }}"
                class="btn btn-warning btn-sm btn_edit  {{ $operationOrder->activeStatus == 1 ? 'disabled' : '' }}"
                id="editBtn_{{ $operationOrder->id }}"
                @if ($operationOrder->activeStatus == 1) disabled style="pointer-events: none; opacity: 0.5;" @endif>
                <i class="fa fa-edit"></i>
            </a>
            @endif

        @if (Auth::user()->can('delete_operation_orders'))
            <form action="{{ route('dashboard.operation_orders.destroy', $operationOrder->id) }}" method="POST"
                style="display: inline-block">
                {{ csrf_field() }}
                {{ method_field('delete') }}
                <button type="submit" class="delete btn btn-danger btn-sm"
                    name=" , العمليه رقم {{ $operationOrder->id }}  " value="{{ $operationOrder->id }}"
                    style="font-weight: bolder">x</button>
            </form>
        @endif
    </div>
    @if($operationOrder->machine_edit == 1 && $operationOrder->created_by == auth()->user()->id)
    <span class="label label-warning" style="display: block;">تعديل</span>
    @endif
@endif

@if ($operationOrder->out_operation && $operationOrder->is_complete == 0)
    @if (Auth::user()->can('complete_operation_orders') || Auth::user()->hasRole('admin'))
        <a href="{{ route('dashboard.operation_orders.showCompleteOut', $operationOrder->id) }}"
            style="display: block"><span class="label label-success ">استكمال</span></a>
    @else
    @endif
@endif
@if ($operationOrder->supervisor_store == auth()->user()->id)
@if($operationOrder->is_complete == -1)
<span class="label label-warning" style="display: block;">تعديل</span>
@if($operationOrder->is_complete == 0)
<span class="label label-warning" style="display: block;">فى انتظار استكمال مسئول الفرع</span>
@endif
@endif
@endif
@if (!$operationOrder->out_operation && !$operationOrder->machine_access)
    @if (Auth::user()->hasRole('machine_response'))
        <span class="label label-warning" style="display: block">فى انتظار الخامة</span>
    @elseif(Auth::user()->hasRole('factor_response') ||
            Auth::user()->hasRole('branch_factor_respons') ||
            Auth::user()->hasRole('safe_factor_response'))
        <span class="label label-warning" style="display: block">فى انتظار سماح مسئول المخزن</span>
    @endif
@endif

@if (Auth::user()->hasRole('store_factor_response') || Auth::user()->hasRole('admin'))
    @if (!$operationOrder->out_operation && !$operationOrder->machine_access)
        <a href="#" class="btn btn-sm {{ $operationOrder->machine_edit == 1 ? 'btn-warning' : 'btn-success' }}"
            style="display: block;" data-toggle="modal" data-target="#machineAccess{{ $i }}"
            data-id="{{ $operationOrder->id }}">سماح للماكينة</a>

        {{-- machine access modal --}}
        <div id="machineAccess{{ $i }}" class="modal fade text-right" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">السماح للماكينة بالاستكمال</h4>
                    </div>
                    <form action="{{ route('dashboard.operation_orders.machineAccess', $operationOrder->id) }}"
                        method="POST">
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <div class="form-group">
                                <label>ملاحظات</label>
                                <input type="text" name="notes2" class="form-control" style="width: 96%;" />
                            </div>

                            <div class="form-group">
                                <label>عاملين الاستلام *</label>
                                <select name="store_employees[]" class="form-control store_employees" multiple required>
                                    @foreach (App\Employee::where('job_id', 2)->where('branch_id', auth()->user()->branch_id)->get() as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success btn_complate"
                                data-id="{{ $operationOrder->id }}">تأكيد</button>
                            <button type="button" class="btn btn-default " data-dismiss="modal">إغلاق</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.store_employees').select2({
                width: '100%',
                language: 'ar',
                dir: 'rtl'
            });
        });
    </script>
@endif
