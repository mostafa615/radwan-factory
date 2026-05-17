@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">
            <section class="content-header">
                <h1>ناتج التشغيل الخارجي</h1>
                <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                    <li class="active">ناتج التشغيل الخارجي</li>
                </ol>
            </section>
            <section class="content" style='padding-bottom: 0px !important;'>

                <div class="box box-primary">

                    <div class="box-header with-border">
                        <h3 class="box-title" style="margin-bottom: 15px">ناتج التشغيل الخارجي</h3>
                        <form action="{{ route('dashboard.operation_order_results.index_out')}}" method="GET">
                            <div class="row">
                                {{-- <div class="col-md-4">
                                    <input type="text" name="search" class="form-control" placeholder="@lang('site.search')" value="{{ request()->search}}">
                                </div> --}}
                                <div class="col-md-4">
                                    @if (Auth::user()->can('create_operation_order_results'))
                                        <a href=" {{route('dashboard.operation_order_results.create_out')}}" class="btn btn-success"><i class="fa fa-plus"></i> @lang('site.add')</a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="box-body">
                        <form action="{{ route('dashboard.operation_order_results.updateConfirmOut')}}" method="Post" id="updateForm">
                            @csrf
                            <div class="table-responsive">
                                <div class="col-md-4">
                                    <span> @lang('site.select-all')</span>
                                    <input type="checkbox" name="select-all" id="select-all" />
                                </div>
                                <table class="table table-hover" id="operatOrdResTable" style="">

                                    <thead>
                                        <tr>
                                            <th>أمر التشغيل</th>
                                            <th>التاريخ</th>
                                            <!--<th>المشرف</th>-->
                                            <th>المستخدمين</th>
                                            <th>الآله</th>
                                            <th>الخامة المستخدمة</th>
                                            <th>اسم العميل</th>
                                            <th>الناتج الفعلي</th>
                                            <th>ناتج الخامه المستخدمة</th>
                                            <th>الملاحظات</th>
                                            <th>التأكيد</th>
                                            <th>الموظفين</th>
                                            <th>ملاحظة التأكيد</th>
                                            <th>العمليات</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                                @if(Auth::user()->can('confirm_operation_order_results'))
                                    <button id="updateBtn" type="submit" class="btn btn-success col-md-12">تأكيد</button>
                                @endif
                            </div>
                        </form>

                    </div>

                </div>

            </section>

    </div>



@endsection
@section('scripts')
<script>
    var operationOrderResultDatatable = null;
    function setOperationOrderResultDataTable() {
        var url = "{{ route('dashboard.operation_order_results.operation_order_results_Datatable_out') }}";
        operationOrderResultDatatable = $('#operatOrdResTable').DataTable({
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
                { "data": "operation_order_id" },
                { "data": "date" , "name":"operationOrder.date"},
                // { "data": "supervisor_name",  "name":"operationOrder.supervisor.name"},
                { "data": "user_id"},
                { "data": "machine_name"},
                { "data": "item" },
                // { "data": "suplies_name" },
                { "data": "client_name" },
                { "data": "actual_output" },
                { "data": "old_item_quantity" },
                // { "data": "weight" },
                // { "data": "damage"},
                // { "data": "damage_price"},
                { "data": "notes" },
                { "data": "supervisor_process"},
                {"data": "employee_id"},
                {"data": "confirm_notes"},
                { "data": "action" }
                ]
        });
    }
    $('#select-all').click(function(event) {
        if(this.checked) {
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

    $(function(){
        setOperationOrderResultDataTable();
        $('select').select2({
            width: '100%'
        });

        $('#updateBtn').on('click', function (){
                $('#updateBtn').attr("disabled", true);
                $("#updateForm").submit();
        })
    });
    $(document).ready(function(){
            $('#operatOrdResTable').wrap("<div style='height: auto;overflow-y: auto;'></div>");
    });
</script>
@endsection
