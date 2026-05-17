@extends('layouts.dashboard.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>أوامر التشغيل</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active"> أوامر التشغيل</li>
            </ol>
        </section>
        <section class="content" style='padding-bottom: 0px !important;'>

            <div class="box box-primary">

                <div class="box-header with-border">
                    <h3 class="box-title" style="margin-bottom: 15px">أوامر التشغيل</h3>
                    <form action="{{ route('dashboard.operation_orders.index') }}" method="GET">
                        <div class="row">
                            {{-- <div class="col-md-4">
                                    <input type="text" name="search" class="form-control" placeholder="@lang('site.search')" value="{{ request()->search}}">
                                </div> --}}


                            <div class="col-md-4">
                                
                                @if (Auth::user()->hasRole('machine_response'))
                                    <a href=" {{ route('dashboard.operation_order_results.create') }}"
                                        class="btn btn-success"><i class="fa fa-plus"></i> إضافه ناتج امر تشغيل داخلى</a>
                                @endif
                                @if (Auth::user()->can('create_operation_orders'))
                                    <a href=" {{ route('dashboard.operation_orders.create') }}" class="btn btn-success"><i
                                            class="fa fa-plus"></i> إضافة أمر شغل داخلي</a>
                                    {{-- <a href=" {{ route('dashboard.operation_orders.createOut') }}" class="btn btn-info"><i
                                            class="fa fa-plus"></i> @lang('site.add_out_operation')</a> --}}
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="operationOrderTable" style=''>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <!--<th>أمر شغل سابق</th>-->
                                    <th>الآلة</th>
                                    <th>المستخدمين</th>
                                    <th>مشرف المخزن</th>
                                    <th>مشرف الانتاج</th>
                                    <!--<th>الموظفين</th>-->
                                    {{-- <th>الخامة</th>
                                        <th>مستلزمات التشغيل</th>
                                        <th>مجموعة الناتج</th>
                                        <th>اسم الناتج</th>
                                        <th>الكمية</th>
                                        <th>الطول</th>
                                        <th>العرض</th> --}}
                                    <th>التاريخ</th>
                                    <th>ملاحظات</th>
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
        var operationOrderDatatable = null;

        function setOperationOrderDatatable() {
            var url = "{{ route('dashboard.operation_orders.operation_orders_Datatable') }}?type=not_all";
            operationOrderDatatable = $('#operationOrderTable').DataTable({
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
                "columns": [{
                        "data": "id"
                    },
                    // {
                    //     "data": "related_operat_ord_id"
                    // },
                    {
                        "data": "machine_id",
                        "name": "machine.name"
                    },
                    {
                        "data": "user_id",
                        "name": "user.name"
                    },
                    {
                        "data": "supervisor_store"
                    },
                    {
                        "data": "supervisor_process"
                    },
                    // {
                    //     "data": "employee_id",
                    //     "name": "employee.name"
                    // },
                    {
                        "data": "date"
                    },
                    {
                        "data": "notes"
                    },
                    {
                        "data": "action"
                    }
                ]
            });
        }

        $(function() {
            setOperationOrderDatatable();
            $('select').select2({
                width: '100%'
            });
        });
        $(document).ready(function() {
            $('table').wrap("<div style='height: auto;overflow-y: auto;'></div>");
        });
    </script>
@endsection
