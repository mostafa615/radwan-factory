@extends('layouts.dashboard.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>أوامر التشغيل الخارجية</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active"> استكمال أوامر التشغيل الخارجية</li>
            </ol>
        </section>
        <section class="content" style='padding-bottom: 0px !important;'>

            <div class="box box-primary">

                <div class="box-header with-border">
                    <h3 class="box-title" style="margin-bottom: 15px"> استكمال أوامر التشغيل الخارجية</h3>
                    <div class="row">
                        <div class="col-md-4">
                            @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('store_factor_response'))
                                <a href=" {{route('dashboard.operation_orders.createOut')}}" class="btn btn-success"><i class="fa fa-plus"></i> @lang('site.add_out_operation')</a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="table-responsive">
                        <div class="col-md-4">
                        </div>
                        <table class="table table-hover" id="operationOrderTable" style=''>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <!--<th>أمر شغل سابق</th>-->
                                    <th>اسم العميل</th>
                                    <th>الآلة</th>
                                    <th>المستخدمين</th>
                                    <th>مشرف المخزن</th>
                                    <th>مشرف الانتاج</th>
                                    <!--<th>الموظفين</th>-->
                                    <th>التاريخ</th>
                                    <th>اجمالي الطول المستخدم</th>
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
            var url = "{{ route('dashboard.operation_orders.operation_orders_Datatable_out') }}?type=complete";
            operationOrderDatatable = $('#operationOrderTable').DataTable({
                "processing": true,
                "serverSide": true,
                "pageLength": 5,
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
                        "data": "client_name"
                    },
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
                        "data": "total_used_length"
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

        $('#select-all').click(function(event) {
            if (this.checked) {
                // Iterate each checkbox
                $(':checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $(':checkbox').each(function() {
                    this.checked = false;
                });
            }
        });

        $(function() {
            setOperationOrderDatatable();
            $('select').select2({
                width: '100%'
            });
            $('#updateConfirmBtn').on('click', function() {
                $('#updateConfirmBtn').attr("disabled", true);
                $("#updateConfirmForm").submit();
            })
        });
        $(document).ready(function() {
            $('table').wrap("<div style='height: auto;overflow-y: auto;'></div>");
        });
    </script>
@endsection
