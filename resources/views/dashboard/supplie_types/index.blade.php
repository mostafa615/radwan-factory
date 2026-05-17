@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">
            <section class="content-header">
                <h1>أنواع مستلزمات التشغيل</h1>
                <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                    <li class="active"> أنواع مستلزمات التشغيل</li>
                </ol>
            </section>
            <section class="content" style='padding-bottom: 0px !important;'>

                <div class="box box-primary">

                    <div class="box-header with-border">
                        <h3 class="box-title" style="margin-bottom: 15px">أنواع مستلزمات التشغيل</h3>
                        <form action="{{ route('dashboard.supplie_types.index')}}" method="GET">
                            <div class="row">
                                {{-- <div class="col-md-4">
                                    <input type="text" name="search" class="form-control" placeholder="@lang('site.search')" value="{{ request()->search}}">
                                </div> --}}


                                <div class="col-md-4">
                                    @if (Auth::user()->can('create_supplie_types'))
                                        <a href=" {{route('dashboard.supplie_types.create')}}" class="btn btn-success"><i class="fa fa-plus"></i> @lang('site.add')</a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="supplie_typetable" style='height: 100px !important;'>
                                <thead>
                                    <tr>
                                        <th>الأسم</th>
                                        <th>الوصف</th>
                                        <th>تم الأنشاء فى</th>
                                        <th>أخر تعديل في</th>
                                        <th>العمليات</th>
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
    var supplie_typeRegisterDatatable = null;
    function setsupplie_typeRegisterDataTable() {
        var url = "{{ route('dashboard.supplie_types.supplie_types_Datatable') }}";
        supplie_typeRegisterDatatable = $('#supplie_typetable').DataTable({
                "processing": true,
                "serverSide": true,
                "pageLength": 10,
                dom: 'Bfrtip',
                buttons: [
                        'copyHtml5',
                        'excelHtml5',
                        'csvHtml5',
                        'pdfHtml5'
                ],
                "sorting": [0, 'DESC'],
                "ajax": url,
                "columns":[
                { "data": "name" },
                { "data": "description" },
                { "data": "created_at" },
                { "data": "updated_at" },
                { "data": "action" }
                ]
        });
    }

    $(function(){
        setsupplie_typeRegisterDataTable();
        $('select').select2({
            width: '100%'
        });
    });
    $(document).ready(function(){
            $('table').wrap("<div style='height: 320px;overflow-y: auto;'></div>");
    });
</script>
@endsection
