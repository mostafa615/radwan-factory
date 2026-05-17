@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">
        <section class="content container-fluid">
            <section class="content-header">
                <h1>أنواع الألات</h1>
                <ol class="breadcrumb">
                    <li><a href=" {{route('dashboard.index')}}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                    <li> <a href=" {{route('dashboard.machine_types.index')}}">أنواع الألات</a></li>
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
                        <form action="{{route('dashboard.machine_types.store')}}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('post') }}


                            <div class="form-group">
                                <label>@lang('site.name')*</label>
                                <input placeholder="@lang('site.name')*" type="text" name="name" class="form-control" value="{{old('name')}}">
                            </div>




                            <div class="form-group">
                                <label>الوصف</label>
                                <input placeholder='الوصف' type="text" name="description" class="form-control">
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



    });

</script>

@endsection
