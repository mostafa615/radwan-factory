
    <a href=" {{ route('dashboard.damages.edit', $damage->id)}}" ><i class="fa fa-edit" style="color: orange"></i></a>

        <form action="{{route('dashboard.damages.destroy', $damage->id)}}" method="POST" style="display: inline-block">
        {{ csrf_field() }}
        {{ method_field('delete')}}
        <button type="submit" class="delete" style="background-color: white; border: none"><i class="fa fa-trash" style="color: red"></i></button>
        </form>
