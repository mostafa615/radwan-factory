
    @if (Auth::user()->can('update_machine_types'))
    <a href=" {{ route('dashboard.machine_types.edit', $machinetype->id)}}" ><i class="fa fa-edit" style="color: orange"></i></a>
    @endif
    @if (Auth::user()->can('delete_machine_types'))
        <form action="{{route('dashboard.machine_types.destroy', $machinetype->id)}}" method="POST" style="display: inline-block">
        {{ csrf_field() }}
        {{ method_field('delete')}}
        <button type="submit" class="delete" style="background-color: white; border: none"><i class="fa fa-trash" style="color: red"></i></button>
        </form>
    @endif
