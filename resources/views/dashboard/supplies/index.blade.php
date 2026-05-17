@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">
            <section class="content-header">
                <h1>مستلزمات التشغيل</h1>
                <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                    <li class="active">مستلزمات التشغيل</li>
                </ol>
            </section>
            <section class="content" style='padding-bottom: 0px !important;'>

                <div class="box box-primary">

                    <div class="box-header with-border">
                        <h3 class="box-title" style="margin-bottom: 15px">مستلزمات التشغيل</h3>
                        <form action="{{ route('dashboard.supplies.index')}}" method="GET">
                            <div class="row">
                                {{-- <div class="col-md-4">
                                    <input type="text" name="search" class="form-control" placeholder="@lang('site.search')" value="{{ request()->search}}">
                                </div> --}}


                                <div class="col-md-4">
                                    @if (Auth::user()->can('create_supplies'))
                                        <a href=" {{route('dashboard.supplies.create')}}" class="btn btn-success"><i class="fa fa-plus"></i> @lang('site.add')</a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="suppliestable" style='height: 100px !important;'>
                                <thead>
                                    <tr>
                                        <th>الأسم</th>
                                        <th>النوع</th>
                                        <th>الطول</th>
                                        <th>العرض</th>
                                        <th>رصيد البداية</th>
                                        <th>الكمية</th>
                                        <th>عدد مرات الاستخدام</th>
                                        <th>المخزن</th>
                                        <th>الوصف</th>
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
    var suppliesRegisterDatatable = null;
    function setsuppliesRegisterDataTable() {
        var url = "{{ route('dashboard.supplies.supplies_Datatable') }}";
        suppliesRegisterDatatable = $('#suppliestable').DataTable({
                "processing": true,
                "serverSide": true,
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, 100,200,300 , 500 , 1000 , 2000 , 5000 , 10000], [10, 25, 50, 100,200,300 , 500 , 1000 , 2000 , 5000 , 10000]],
                dom: 'lBfrtip',
                buttons: [
                        'copyHtml5',
                        'excelHtml5',
                        'csvHtml5',
                        'print',
                        'pdfHtml5'
                ],
                "sorting": [0, 'DESC'],
                "ajax": url,
                "columns":[
                { "data": "name" },
                { "data": "type" },
                { "data": "height" },
                { "data": "width" },
                { "data": "init_quantity" },
                { "data": "quantity" },
                { "data": "used" },
                { "data": "store_id" },
                { "data": "description" },
                { "data": "action" }
                ]
        });
    }

    $(function(){
        setsuppliesRegisterDataTable();
        $('select').select2({
            width: '100%'
        });
    });
    $(document).ready(function(){
            // $('table').wrap("<div style='height: 320px;overflow-y: auto;'></div>");
    });
</script>
@endsection
