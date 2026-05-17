@extends('layouts.dashboard.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>صرف مستلزمات للآلات</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">صرف مستلزمات للآلات</li>
            </ol>
        </section>
        <section class="content" style='padding-bottom: 0px !important;'>

            <div class="box box-primary">

                <div class="box-header with-border">
                    <h3 class="box-title" style="margin-bottom: 15px">صرف مستلزمات للآلات</h3>
                    <form action="{{ route('dashboard.machine_supplies.index') }}" method="GET">
                        <div class="row">
                            {{-- <div class="col-md-4">
                                    <input type="text" name="search" class="form-control" placeholder="@lang('site.search')" value="{{ request()->search}}">
                                </div> --}}


                            <div class="col-md-4">
                                @if (Auth::user()->can('create_machine_supplies'))
                                    <a href=" {{ route('dashboard.machine_supplies.create') }}" class="btn btn-success"><i
                                            class="fa fa-plus"></i> @lang('site.add')</a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                <div class="box-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="machineSuppliestable" style='height: 100px !important;'>
                            <thead>
                                <tr>
                                    <th>الأله</th>
                                    <th>المستلزمات</th>
                                    <th>الكمية المصروفه</th>
                                    <th>الاستخدام المصروف</th>
                                    <th>التاريخ</th>
                                    <th>باقي الاستخدام</th>
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
        var machineSupplieDatatable = null;

        function setmachineSupplieDatatable() {
            var url = "{{ route('dashboard.machine_supplies.machine_supplies_Datatable') }}";
            machineSupplieDatatable = $('#machineSuppliestable').DataTable({
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
                        "data": "machine_name",
                        "name": "machine.name"
                    },
                    {
                        "data": "supplie_name",
                        "name": "supplie.name"
                    },
                    {
                        "data": "transfer_quantity"
                    },
                    {
                        "data": "supplie_used"
                    },
                    {
                        "data": "date"
                    },
                    {
                        "data": "used"
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
            setmachineSupplieDatatable();
            $('select').select2({
                width: '100%'
            });
        });
        $(document).ready(function() {
            // $('table').wrap("<div style='height: 320px;overflow-y: auto;'></div>");
        });
    </script>
@endsection
