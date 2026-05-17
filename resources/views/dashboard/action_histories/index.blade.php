@extends('layouts.dashboard.app')

@section('css')
    <style>
        #actionHistoryTable>tbody>tr>td:nth-child(8){
            max-width: 20px !important;
            overflow: auto !important;
        }
        td, th {
        display: table-cell !important;
        height:15px !important;
        max-height:15px !important;
        }
    </style>
@endsection
@section('content')

    <div class="content-wrapper">
            <section class="content-header">
                <h1>@lang('site.action_history')</h1>
                <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                    <li class="active"> @lang('site.action_history')</li>
                </ol>
            </section>
            <section class="content" style='padding-bottom: 0px !important;'>

                <div class="box box-primary">

                    <div class="box-header with-border">
                        <h3 class="box-title" style="margin-bottom: 15px">@lang('site.action_history')</h3>
                        <form action="{{ route('dashboard.action_histories.index')}}" method="GET">
                            <div class="row">
                                <div class="row col-md-10">
                                    <div class="col-md-3">
                                        <label>@lang('site.user')</label>
                                        <select name="user_id" class="form-control  select2-js" id="users">
                                            <option value="">@lang('site.user')</option>
                                            @foreach ($users as $user)
                                                <option value="{{$user->id}}" class="departoption" style="display: none">
                                                    {{$user->name}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>@lang('site.action')</label>
                                        <select name="action" class="form-control  select2-js" id="action">
                                            <option value="">@lang('site.action')</option>
                                            <option value="create" class="departoption">{{__('site.create')}}</option>
                                            <option value="update" class="departoption">{{__('site.update')}}</option>
                                            <option value="delete" class="departoption">{{__('site.delete')}}</option>
                                            <option value="login" class="departoption">{{__('site.login')}}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3" style="position: relative">
                                        <label>@lang('site.date_from')</label>
                                        <input id="date_from" type="date" name="date_from" class="form-control datePicker" value="<?php
                                        $dt = date("Y-m-d");
                                        echo date( "Y-m-d", strtotime('-29 day', strtotime($dt)) ); ?>">
                                    </div>
                                    <div class="col-md-3" style="position: relative">
                                        <label>@lang('site.date_to')</label>
                                        <input id="date_to" type="date" name="date_to" class="form-control datePicker" value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-1" style="margin: 22px 0;">
                                    <button type="button" onclick="reloadData( $('#users').val(),$('#action').val(),$('#date_from').val(),$('#date_to').val()  )" class="btn btn-primary"><i class="fa fa-search"></i> @lang('site.search')</button>
                                </div>

                            </div>
                        </form>
                    </div>

                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="actionHistoryTable" style=''>
                                <thead>
                                    <tr>
                                        <th>@lang('site.user')</th>
                                        <th>@lang('site.system')</th>
                                        <th>@lang('site.user_action')</th>
                                        <th>@lang('site.model')</th>
                                        <th>@lang('site.model_id')</th>
                                        <th>@lang('site.date')</th>
                                        <th>@lang('site.time')</th>
                                        <th style="max-width: 30px">@lang('site.properties')</th>
                                        <th>@lang('site.notes')</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>

            </section>

    </div>



@endsection
@section('scripts')
<script>
    function reloadData(user, action, dateFrom, dateTo) {
            var url = "{{ route('dashboard.action_histories.datatable') }}?user_id="+user +"&action="+action +"&date_from="+dateFrom +"&date_to="+dateTo;
            actionHistoryDatatable.ajax.url(url).load();
    }
    var actionHistoryDatatable = null;
    function setactionHistoryDatatable() {
        var url = "{{ route('dashboard.action_histories.datatable') }}";
        actionHistoryDatatable = $('#actionHistoryTable').DataTable({
                "processing": true,
                "serverSide": true,
                "pageLength": 10,
                dom: 'Bfrtip',
                buttons: ['copyHtml5','excelHtml5','csvHtml5','pdfHtml5'],
                "sorting": [0, 'DESC'],
                "ajax": url,
                "columns":[
                { "data": "user_id" },
                { "data": "system" },
                { "data": "action"},
                { "data": "model_type"},
                { "data": "model_id"},
                { "data": "date" },
                { "data": "time"},
                { "data": "properties"},
                { "data": "notes"}
                ]
        });
    }

    $(function(){
        setactionHistoryDatatable();
        $('select').select2({
            width: '100%'
        });
    });
    $(document).ready(function(){
            $('table').wrap("<div></div>");
            $('table tbody tr').css({
                'max-height': '20px',
                'overflow': 'scroll'
            });
    });
</script>
@endsection
