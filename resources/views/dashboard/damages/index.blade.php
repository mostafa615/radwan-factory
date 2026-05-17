@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">
            <section class="content-header">
                <h1>التالف</h1>
                <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                    <li class="active">التالف</li>
                </ol>
            </section>
            <section class="content" style='padding-bottom: 0px !important;'>

                <div class="box box-primary">

                    <div class="box-header with-border">
                        <h3 class="box-title" style="margin-bottom: 15px">التالف</h3>
                        <form action="{{ route('dashboard.damages.index')}}" method="GET">
                            <div class="row">
                                {{-- <div class="col-md-4">
                                    <input type="text" name="search" class="form-control" placeholder="@lang('site.search')" value="{{ request()->search}}">
                                </div> --}}


                                <div class="col-md-4">


                                        <a href=" {{route('dashboard.damages.create')}}" class="btn btn-success"><i class="fa fa-plus"></i> @lang('site.add')</a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="damageTable" style='height: 100px !important;'>
                                <thead>
                                    <tr>
                                        <th>الأسم</th>
                                        <th>الكود</th>
                                        <th>المجموعة</th>
                                        <th>النوع</th>
                                        <th>السعر</th>
                                        <th>الكمية</th>
                                        <th>الطول</th>
                                        <th>العرض</th>
                                        <th>الوزن</th>
                                        <th>@lang('site.notes')</th>
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
    var damageDatatable = null;
    function setdamageDatatable() {
        var url = "{{ route('dashboard.damages.damages_Datatable') }}";
        damageDatatable = $('#damageTable').DataTable({
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
                { "data": "code" },
                { "data": "group_id" },
                { "data": "damage_type" },
                { "data": "price" },
                { "data": "quantity" },
                { "data": "length" },
                { "data": "width" },
                { "data": "weight" },
                { "data": "notes" },
                { "data": "action" }
                ]
        });
    }

    $(function(){
        setdamageDatatable();
        $('select').select2({
            width: '100%'
        });
    });
    $(document).ready(function(){
            $('table').wrap("<div style='height: 320px;overflow-y: auto;'></div>");
    });
</script>
@endsection
