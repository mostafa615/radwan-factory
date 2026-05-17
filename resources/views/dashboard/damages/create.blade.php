@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">
        <section class="content container-fluid">
            <section class="content-header">
                <h1>التالف</h1>
                <ol class="breadcrumb">
                    <li><a href=" {{route('dashboard.index')}}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                    <li> <a href=" {{route('dashboard.damages.index')}}">التالف</a></li>
                    <li class="active">@lang('site.add')</li>

                </ol>
            </section>
            <section class="content">

                <div class="box box-primary">

                    <div class="box-header">
                        <h3 class="box-title">@lang('site.add')</h3>
                    </div>

                    <div class="box-body">
                        @include('partials._errors')
                        <form action="{{route('dashboard.damages.store')}}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('post') }}


                            <div class="form-group">
                                <label>@lang('site.name')*</label>
                                <input placeholder="@lang('site.name')*" type="text" name="name" id="name" class="form-control" value="فضله " required>
                                <div id="name_list" style="position: absolute; max-height: 100px; overflow-y: scroll; z-index: 1; background: white; width: 98.4%;">
                                    {{--  --}}
                                </div>
                                <input type="hidden" id="name_found">
                            </div>


                            <div class="form-group">
                                <label>@lang('site.out_group')*</label>
                                <select name="group_id" class="form-control  select2-js" required>
                                    @foreach ($groups as $group)
                                        <option value="{{$group->id}}" {{old('group_id') == $group->id ? 'selected' : ''}}>{{$group->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>@lang('site.price')*</label>
                                <input type="number" name="price" class="form-control" value="{{old('price')}}" required>
                            </div>



                            <div class="form-group">
                                <label>@lang('site.notes')</label>
                                <input placeholder="@lang('site.notes')"" type="text" name="notes" class="form-control" value="{{old('notes')}}">
                            </div>


                            <div class="form-group">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> @lang('site.add')</button>
                            </div>
                        </form>
                    </div><!--end of box-body-->

                </div>

            </section>
        </section>


    </div>

@endsection

@section('scripts')
<script>


    $(function(){
        $('select').select2({
            width: '100%'
        });
    });

    $("#name").keyup(function() {
        $.ajax({
            url: `{{ route('dashboard.damages.auto_complete_first') }}?name=` + $(this).val(),
            type: 'GET',
            success: function(data) {
                if (data.length < 1) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
                var listHtml = "<ul>";
                for(var i = 0; i < data.length; i++) {
                    listHtml += "<li>" + data[i] + "</li>";
                }
                if($("#name").val() != '' && data.length != 0) {
                    $("#name_found").val('true');
                } else if($("#name").val() != '' && data.length == 0) {
                    $("#name_found").val('false');
                }
                listHtml += "</ul>";
                $("#name_list").html(listHtml);
            },
            error: function() {
                console.log('error');
            }
        })
    })
</script>

@endsection
