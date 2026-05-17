
@if ($type == 'action')
    @if(Auth::user()->can('update_machine_supplies'))
        @php
            // dd($machineSupplie);
        @endphp
        <a href=" {{ route('dashboard.machine_supplies.edit', $machineSupplie->machine_supplie_id)}}" ><i class="fa fa-edit" style="color: orange"></i></a>
    @endif
    @if(Auth::user()->can('delete_machine_supplies'))
        <form action="{{route('dashboard.machine_supplies.destroy', $machineSupplie->machine_supplie_id)}}" method="POST" style="display: inline-block">
        {{ csrf_field() }}
        {{ method_field('delete')}}
        <button type="submit" class="delete" style="background-color: white; border: none"><i class="fa fa-trash" style="color: red"></i></button>
        </form>
    @endif
@endif
