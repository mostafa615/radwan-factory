@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">
        <section class="content container-fluid">
            <section class="content-header">
                <h1>صرف مجموعات للآلات</h1>
                <ol class="breadcrumb">
                    <li><a href=" {{route('dashboard.index')}}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                    <li> <a href=" {{route('dashboard.machine_groups.index')}}">صرف مجموعات للآلات</a></li>
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
                        <form action="{{route('dashboard.machine_groups.update', $machineGroup)}}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('put') }}

                            <div class="form-group">
                                <label>@lang('site.machine_types')*</label>
                                <select name="type" class="form-control  select2-js" id="machineType">
                                    <option value="">@lang('site.machine_types')</option>
                                    @foreach ($machineTypes as $machineType)
                                        <option value="{{$machineType->id}}" {{old('type') == $machineType->id ? 'selected' : ''}}>{{$machineType->name}}</option>
                                    @endforeach
                                </select>
                            </div>


                            <div class="form-group">
                                <label>@lang('site.machine')*</label>
                                <select name="machine_id" class="form-control  select2-js" id="machines">
                                    <option value="">@lang('site.machine')</option>
                                    @foreach ($machines as $machine)
                                        <option value="{{$machine->id}}" {{$machineGroup->machine_id == $machine->id ? 'selected' : ''}}>{{$machine->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                             @php
                                $groupIds = explode(',', $machineGroup->group_id);
                                $gGroups = App\Group::whereIn('id', $groupIds)->get();
                                // dd($gGroups);
                            @endphp
                            <div class="form-group">
                                <label>@lang('site.group')*</label>
                                <select name="group_id[]" class="form-control  select2-js" id="groupId" multiple="">
                                    <option value="">@lang('site.group')</option>
                                    @foreach ($groups as $group)
                                        <option value="{{$group->id}}" {{$gGroups->contains($group) ? 'selected' : ''}}>{{$group->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>@lang('site.type')*</label>
                                <select name="type" class="form-control  select2-js" id="typeId">
                                    <option value="">@lang('site.type')</option>
                                    <option value="in" {{$machineGroup->type == 'in' ? 'selected' : ''}}>مجموعة دخل</option>
                                    <option value="out" {{$machineGroup->type == 'out' ? 'selected' : ''}}>مجموعة خرج</option>
                                </select>
                            </div>



                            <div class="form-group">
                                <label>ملاحظات</label>
                                <input placeholder='ملاحظات' type="text" name="notes" class="form-control" value="{{$machineGroup->notes}}">
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
    $("#machineType").change(function(){
            $.ajax({
                url: "{{ route('dashboard.machines.get_machines_by_type') }}?type=" + $(this).val(),
                method: 'GET',
                success: function(data) {
                    $('#machines').html(data.html);
                }
            });

    });


    $(function(){
        $('select').select2({
            width: '100%'
        });
    });

</script>

@endsection
