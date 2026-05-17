@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">
        <section class="content container-fluid">
            <section class="content-header">
                <h1>مستلزمات التشغيل</h1>
                <ol class="breadcrumb">
                    <li><a href=" {{route('dashboard.index')}}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                    <li> <a href=" {{route('dashboard.supplies.index')}}">مستلزمات التشغيل</a></li>
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
                        <form action="{{route('dashboard.supplies.store')}}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('post') }}


                            <div class="form-group">
                                <label>@lang('site.name')*</label>
                                <input placeholder="@lang('site.name')*" type="text" name="name" class="form-control" value="{{old('name')}}">
                            </div>


                            <div class="form-group">
                                <label>النوع</label>
                                <select class="form-control" name="type" value="{{old('type')}}">
                                    <option disabled selected>النوع</option>
                                    @foreach($supplie_types as $types)
                                    <option value='{{$types->id}}'>
                                    {{$types->name}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>الطول *</label>
                                <input placeholder="الطول" type="number" step="any" min="0" name="height" class="form-control" value="{{old('height')}}" required>
                            </div>
                            <div class="form-group">
                                <label>العرض *</label>
                                <input placeholder="العرض" type="number" step="any" min="0" name="width" class="form-control" value="{{old('width')}}" required>
                            </div>
                            <div class="form-group">
                                <label>الكمية *</label>
                                <input placeholder="الكمية" type="number" step="any" min="0" name="quantity" class="form-control" value="{{old('quantity')}}" required>
                            </div>
                            <div class="form-group">
                                <label>رصيد البداية *</label>
                                <input placeholder="رصيد البداية" type="number" step="any" min="0" name="init_quantity" class="form-control" value="{{old('init_quantity')}}" required>
                            </div>

                            <div class="form-group">
                                <label>@lang('site.used')*</label>
                                <input type="number" name="used" class="form-control" value="{{old('used')}}">
                            </div>

                            <div class="form-group">
                                <label>الفرع</label>
                                <select class="form-control" name="store_id" value="{{old('store_id')}}">
                                    <option disabled selected>الفرع</option>
                                    @foreach($stores as $store)
                                    <option value='{{$store->id}}'>
                                    {{$store->name}}
                                    </option>
                                    @endforeach
                                </select>
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
        $('select').select2({
            width: '100%'
        });
    });

</script>

@endsection
