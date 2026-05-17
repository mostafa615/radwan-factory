
    @if(Auth::user()->can('update_machine_groups'))
    <a href=" {{ route('dashboard.machine_groups.edit', $machineGroup->id)}}" ><i class="fa fa-edit" style="color: orange"></i></a>
    @endif
    @if(Auth::user()->can('delete_machine_groups'))
    <form action="{{route('dashboard.machine_groups.destroy', $machineGroup->id)}}" method="POST" style="display: inline-block">
        {{ csrf_field() }}
        {{ method_field('delete')}}
        <button type="submit" class="delete" style="background-color: white; border: none"><i class="fa fa-trash" style="color: red"></i></button>
    </form>
    @endif
