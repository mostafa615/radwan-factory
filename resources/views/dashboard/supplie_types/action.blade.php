
@if(Auth::user()->can('update_supplie_types'))
    <a href=" {{ route('dashboard.supplie_types.edit', $supplietype->id)}}" ><i class="fa fa-edit" style="color: orange"></i></a>
@endif
@if(Auth::user()->can('delete_supplie_types'))
    <form action="{{route('dashboard.supplie_types.destroy', $supplietype->id)}}" method="POST" style="display: inline-block">
    {{ csrf_field() }}
    {{ method_field('delete')}}
    <button type="submit" class="delete" style="background-color: white; border: none"><i class="fa fa-trash" style="color: red"></i></button>
    </form>
@endif
