
    @if(Auth::user()->can('update_machines'))
    <a href=" {{ route('dashboard.machines.edit', $machines->id)}}" ><i class="fa fa-edit" style="color: orange"></i></a>
    @endif
    @if(Auth::user()->can('delete_machines'))
        <form action="{{route('dashboard.machines.destroy', $machines->id)}}" method="POST" style="display: inline-block">
        {{ csrf_field() }}
        {{ method_field('delete')}}
        <button type="submit" class="delete" style="background-color: white; border: none"><i class="fa fa-trash" style="color: red"></i></button>
        </form>
    @endif
