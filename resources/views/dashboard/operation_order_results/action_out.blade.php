@if ($type == 'confirm_notes')
    @if (Auth::user()->can('confirm_operation_order_results'))
        @if (
            $operationOrderResult->confirmed != 1 &&
                (Auth::user()->hasRole('factor_response') || Auth::user()->hasRole('admin')))
            <input type="textarea" value="{{ $operationOrderResult->confirm_notes ?? '' }}" title="ملاحظة مشرف الانتاج"
                name="resource[{{ $operationOrderResult->id }}][confirm_notes]"
                id="confirm_notes{{ $operationOrderResult->id }}" @if (
                    $operationOrderResult->operationOrder->supervisor_process != auth()->user()->id &&
                        !auth()->user()->hasRole('admin')) disabled @endif />
            <input type="hidden" name="resource[{{ $operationOrderResult->id }}][itemId]"
                value="{{ $operationOrderResult->id }}">
        @endif

        @if (
            $operationOrderResult->store_confirm != 1 &&
                (Auth::user()->hasRole('store_factor_response') || Auth::user()->hasRole('admin')))
            @if ($operationOrderResult->confirmed == 1)
                <div class="form-group">
                    <input type="textarea" value="{{ $operationOrderResult->store_confirm_notes ?? '' }}"
                        title="ملاحظة مسئول المخزن"
                        name="resource[{{ $operationOrderResult->id }}][store_confirm_notes]"
                        id="store_confirm_notes{{ $operationOrderResult->id }}"
                        {{ $operationOrderResult->operationOrder->supervisor_store != auth()->user()->id && !auth()->user()->hasRole('admin') ? 'disabled' : '' }} />
                    <input type="hidden" name="resource[{{ $operationOrderResult->id }}][itemId]"
                        value="{{ $operationOrderResult->id }}">
                </div>
                <div class="form-group">
                    <select name="resource[{{ $operationOrderResult->id }}][store_employees][]"
                        class="form-control store_employees" multiple required>
                        @foreach (App\Employee::where('job_id', 2)->where('branch_id', auth()->user()->branch_id)->get() as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- @else
                <span class="label label-warning">فى انتظار موافقة مشرف الانتاج</span> --}}
            @endif
        @endif
    @elseif(Auth::user()->can('edit_confirm_notes'))
        <input type="textarea" value="{{ $operationOrderResult->confirm_notes ?? '' }}" title="تأكيد مشرف الانتاج"
            name="resource[{{ $operationOrderResult->id }}][confirm_notes]"
            id="confirm_notes{{ $operationOrderResult->id }}" />
        <input type="hidden" name="resource[{{ $operationOrderResult->id }}][itemId]"
            value="{{ $operationOrderResult->id }}">
    @else
        <p title="ملاحظة مشرف الانتاج">{{ $operationOrderResult->confirm_notes }}</p>
        <br><br>
        <p title="ملاحظة مسئول المخزن">{{ $operationOrderResult->store_confirm_notes }}</p>
    @endif
@elseif ($type == 'action')
    @if (Auth::user()->can('confirm_operation_order_results'))
        @if (
            $operationOrderResult->confirmed != 1 &&
                (Auth::user()->hasRole('factor_response') || Auth::user()->hasRole('admin')))
            <input type="checkbox" title="تأكيد مشرف الانتاج"
                name="resource[{{ $operationOrderResult->id }}][confirmed]"
                id="confirmed{{ $operationOrderResult->id }}" @if (
                    $operationOrderResult->operationOrder->supervisor_process != auth()->user()->id &&
                        !auth()->user()->hasRole('admin')) disabled @endif />
            <input type="hidden" name="resource[{{ $operationOrderResult->id }}][itemId]"
                value="{{ $operationOrderResult->id }}">
        @endif

        @if (
            $operationOrderResult->store_confirm != 1 &&
                (Auth::user()->hasRole('store_factor_response') || Auth::user()->hasRole('admin')))
            @if ($operationOrderResult->confirmed == 1)
                <input type="checkbox" title="تأكيد مسئول المخزن"
                    name="resource[{{ $operationOrderResult->id }}][store_confirm]"
                    id="store_confirm{{ $operationOrderResult->id }}"
                    {{ $operationOrderResult->operationOrder->supervisor_store != auth()->user()->id && !auth()->user()->hasRole('admin') ? 'disabled' : '' }} />
                <input type="hidden" name="resource[{{ $operationOrderResult->id }}][itemId]"
                    value="{{ $operationOrderResult->id }}">
            @else
                <span class="label label-warning" style="display: block;">فى انتظار موافقة مشرف الانتاج</span>
            @endif
        @endif
    @endif

    @if (
        (Auth::user()->hasRole('branch_factor_respons') || Auth::user()->hasRole('safe_factor_response')) &&
            $operationOrderResult->confirmed != 1)
        <span class="label label-warning" style="display: block;">فى انتظار موافقة مشرف الانتاج</span>
    @endif

    @if (
        (Auth::user()->hasRole('branch_factor_respons') || Auth::user()->hasRole('safe_factor_response')) &&
            $operationOrderResult->confirmed == 1 &&
            $operationOrderResult->store_confirm != 1)
        <span class="label label-warning" style="display: block;">فى انتظار موافقة مسئول المخزن</span>
    @endif

    @if (Auth::user()->hasRole('factor_response') &&
            $operationOrderResult->confirmed == 1 &&
            $operationOrderResult->store_confirm != 1)
        <span class="label label-warning" style="display: block;">فى انتظار موافقة مسئول المخزن</span>
    @endif

    @if (Auth::user()->can('reade_operation_order_results'))
        <a target="_blank" title="print"
            href="{{ route('dashboard.operation_order_results.show_out', $operationOrderResult->id) }}"><i
                class="fa fa-print" style="color: #00ACD6"></i></a>
        <a target="_blank" title="print"
            href="{{ route('dashboard.operation_order_results.showOutStore', $operationOrderResult->id) }}"><i
                class="fa fa-print" style="color: #22a74a"></i></a>
    @endif

    @if (Auth::user()->can('update_operation_order_results'))
        <a title="update" href=" {{ route('dashboard.operation_order_results.edit', $operationOrderResult->id) }}"><i
                class="fa fa-edit" style="color: orange"></i></a>
    @endif

    @if ($operationOrderResult->confirmed != 1 && $operationOrderResult->operationOrder->supervisor_process == auth()->user()->id)
        <form action="{{ route('dashboard.operation_order_results.destroy', $operationOrderResult->id) }}"
            method="POST" style="display: inline-block">
            {{ csrf_field() }}
            {{ method_field('delete') }}
            <button title="delete" type="submit" class="delete btn btn-danger btn-sm" style="font-weight: bolder"
                name=" , العمليه رقم {{ $operationOrderResult->operation_order_id }}  ">X</button>
        </form>
    @elseif($operationOrderResult->confirmed == 1 && $operationOrderResult->operationOrder->supervisor_store == auth()->user()->id && $operationOrderResult->store_confirm != 1)
        <form action="{{ route('dashboard.operation_order_results.destroy', $operationOrderResult->id) }}"
            method="POST" style="display: inline-block">
            {{ csrf_field() }}
            {{ method_field('delete') }}
            <button title="delete" type="submit" class="delete btn btn-danger btn-sm" style="font-weight: bolder"
                name=" , العمليه رقم {{ $operationOrderResult->operation_order_id }}  ">X</button>
        </form>
    @endif
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
