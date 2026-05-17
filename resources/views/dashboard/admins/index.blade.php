@extends('layouts.dashboard.app')

@section('content')


    <div class="content-wrapper">
        <section class="content-header">

            <h1>@lang('site.admins')</h1>
            <ol class="breadcrumb">
                <li><a href="{{route('dashboard.index')}}"><i class="fa fa-dashboard"></i>@lang('site.dashboard')</a></li>
                <li class="active">@lang('site.admins')</li>
            </ol>

        </section>

        <section class="content">
        <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title" style="margin-bottom:10px">@lang('site.admins') {{--<small>{{$admins->total()}}</small>--}}</h3>

                    <form action=" {{ route('dashboard.admins.index')}} " method="get">
                        <div class="row" style="display:flex">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="@lang('site.search')"/>
                            </div>

                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> @lang('site.search')</button>
                                @if(auth()->user()->hasPermission('create_admins'))
                                    <a href="{{route('dashboard.admins.create')}} " class="btn btn-success"><i class="fa fa-plus"> </i> @lang('site.add')</a>
                                @endif
                            </div>

                        </div>

                    </form><!-- end of form -->
                </div><!-- end of box header -->

                <div class="box-body table-responsive no-padding">

                    @if($admins->count() > 0)
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('site.name')</th>
                                {{--<th>@lang('site.last_name')</th>--}}
                                <th>@lang('site.email')</th>
                                <th>@lang('site.branch')</th>
                                <th>@lang('site.action')</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($admins as $index=>$admin)
                                <tr>
                                    <td>{{$index + 1}}</td>
                                    <td>{{$admin->name}}</td>
                                    {{--<td>{{$admin->last_name}}</td>--}}
                                    <td>{{$admin->email}}</td>
                                    <td>{{$admin->branch->name ?? ''}}</td>
                                    <td>
                                        @if(auth()->user()->hasPermission('update_admins'))
                                            <a href="{{route('dashboard.admins.edit', $admin->id)}} " class="btn btn-info btn-sm"><i class="fa fa-edit"></i> @lang('site.edit')</a>
                                        @endif
                                        @if(auth()->user()->hasPermission('delete_admins'))
                                            <form action="{{route('dashboard.admins.destroy', $admin->id)}} " method="post" style="display: inline-block">
                                                {{csrf_field()}}
                                                {{method_field('delete')}}
                                                <button type="submit" class="btn btn-danger delete btn-sm"><i class="fa fa-trash"></i> @lang('site.delete')</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table><!-- end of table -->
                     {{ $admins->appends(request()->query())->links() }}
                    @else
                    <h2>@lang('site.no_data_found')</h2>
                    @endif

                </div><!-- end of box body -->
            </div>
        </section>
    </div>
@endsection
