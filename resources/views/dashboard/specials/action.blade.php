@if(Auth::user()->can('update_specials'))
    <a href=" {{ route('dashboard.specials.edit', $special->id)}}" ><i class="fa fa-edit" style="color: orange"></i></a>
@endif
@if(Auth::user()->can('delete_specials'))
    <form action="{{route('dashboard.specials.destroy', $special->id)}}" method="POST" style="display: inline-block">
    {{ csrf_field() }}
    {{ method_field('delete')}}
    <button type="submit" class="delete" style="background-color: white; border: none"><i class="fa fa-trash" style="color: red"></i></button>
    </form>
@endif
