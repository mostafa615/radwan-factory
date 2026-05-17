@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">
        <section class="content container-fluid">
            <section class="content-header">
                <h1>مستلزمات التشغيل</h1>
                <ol class="breadcrumb">
                    <li><a href=" {{route('dashboard.index')}}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                    <li> <a href=" {{route('dashboard.supplies.index')}}">مستلزمات التشغيل</a></li>
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
                        <form action="{{route('dashboard.supplies.update', $supplies)}}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('put') }}

                            <div class="form-group">
                                <label>@lang('site.name')*</label>
                                <input placeholder="@lang('site.name')*" type="text" name="name" class="form-control" value="{{$supplies->name}}">
                            </div>


                            <div class="form-group">
                                <label>النوع</label>
                                <select class="form-control" name="type" value="{{$supplies->type}}">
                                    <option disabled>النوع</option>
                                    @foreach($supplie_types as $types)
                                    <option value='{{$types->id}}' {{$types->id == $supplies->type ? 'selected' : ''}}>
                                    {{$types->name}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>الطول</label>
                                <input placeholder="الطول" type="number" name="height" class="form-control" value="{{$supplies->height}}">
                            </div>
                            <div class="form-group">
                                <label>العرض *</label>
                                <input placeholder="العرض" type="number" name="width" class="form-control" value="{{$supplies->width}}">
                            </div>
                            <div class="form-group">
                                <label>الكمية *</label>
                                <input placeholder="الكمية" type="number" name="quantity" class="form-control" value="{{$supplies->quantity}}">
                            </div>
                            <div class="form-group">
                                <label>رصيد البداية *</label>
                                <input placeholder="رصيد البداية" type="number" name="init_quantity" class="form-control" value="{{$supplies->init_quantity}}">
                            </div>
                            <div class="form-group">
                                <label>عدد مرات الاستخدام *</label>
                                <input placeholder="عدد مرات الاستخدام" type="number" name="used" class="form-control" value="{{$supplies->used}}">
                            </div>
                            <div class="form-group">
                                <label>الفرع</label>
                                <select class="form-control" name="store_id" value="{{$supplies->store_id}}">
                                    <option disabled>الفرع</option>
                                    @foreach($stores as $store)
                                    <option value='{{$store->id}}' {{$store->id == $supplies->store_id ? 'selected' : ''}}>
                                    {{$store->name}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="form-group">
                                <label>الوصف</label>
                                <input placeholder='الوصف' type="text" name="description" class="form-control" value="{{$supplies->description}}">
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
