@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">
        <section class="content container-fluid">
            <section class="content-header">
                <h1>الألات</h1>
                <ol class="breadcrumb">
                    <li><a href=" {{route('dashboard.index')}}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                    <li> <a href=" {{route('dashboard.machines.index')}}">الألات</a></li>
                    <li class="active">@lang('site.edit')</li>

                </ol>
            </section>
            <section class="content">

                <div class="box box-primary">

                    <div class="box-header">
                        <h3 class="box-title">@lang('site.edit')</h3>
                    </div>

                    <div class="box-body">
                        @include('partials._errors')
                        <form action="{{route('dashboard.machines.update', $machines)}}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('put') }}

                            <div class="form-group">
                                <label>@lang('site.name')*</label>
                                <input placeholder="@lang('site.name')*" type="text" name="name" class="form-control" value="{{$machines->name}}">
                            </div>


                            <div class="form-group">
                                <label>النوع</label>
                                <select class="form-control" name="type" value="{{$machines->type}}">
                                    <option disabled>النوع</option>
                                    @foreach($machine_types as $types)
                                    <option value='{{$types->id}}' {{$types->id == $machines->type ? 'selected' : ''}}>
                                    {{$types->name}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>@lang('site.store')*</label>
                                <select name="store_id" class="form-control  select2-js">
                                    <option value="">@lang('site.store')</option>
                                    @foreach ($stores as $store)
                                        <option value="{{$store->id}}" {{ $machines->store_id == $store->id ? 'selected' : ''}}>{{$store->name}}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="form-group">
                                <label>الوصف</label>
                                <input placeholder='الوصف' type="text" name="description" class="form-control" value="{{$machines->description}}">
                            </div>


                            <div class="form-group">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-edit"></i> @lang('site.edit')</button>
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

</script>

@endsection
