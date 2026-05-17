@if(Auth::user()->can('update_supplies'))
    <a href=" {{ route('dashboard.supplies.edit', $supplies->id)}}" ><i class="fa fa-edit" style="color: orange"></i></a>
@endif
@if(Auth::user()->can('delete_supplies'))
    <form action="{{route('dashboard.supplies.destroy', $supplies->id)}}" method="POST" style="display: inline-block">
    {{ csrf_field() }}
    {{ method_field('delete')}}
    <button type="submit" class="delete" style="background-color: white; border: none"><i class="fa fa-trash" style="color: red"></i></button>
    </form>
@endif
