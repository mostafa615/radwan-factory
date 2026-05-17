@extends('layouts.dashboard.app')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>تحويل مستلزمات التشغيل</h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">تحويل مستلزمات التشغيل</li>
            </ol>
        </section>
        <section class="content" style='padding-bottom: 0px !important;'>

            <div class="box box-primary">

                <div class="box-header with-border">
                    <h3 class="box-title" style="margin-bottom: 15px"> تحويل مستلزمات التشغيل</h3>
                    <form action="{{ route('dashboard.supplies.index') }}" method="GET">
                        <div class="row">
                            {{-- <div class="col-md-4">
                                    <input type="text" name="search" class="form-control" placeholder="@lang('site.search')" value="{{ request()->search}}">
                                </div> --}}


                            <div class="col-md-4">
                                @if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('branch_factor_respons') || Auth::user()->hasRole('safe_factor_response'))
                                    <a href=" {{ route('dashboard.supplies.exchange_machine_supplies_create_view') }}"
                                        class="btn btn-success"><i class="fa fa-exchange"></i> تحويل</a>
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
                                    <th>المستلزم</th>
                                    <th>كميه الاستخدام المحوله</th>
                                    <th>الآله القديمه</th>
                                    <th>كميه استخدام الآله القديمه قبل </th>
                                    <th>كميه استخدام الآله القديمه بعد </th>
                                    <th>الآله الجديده</th>
                                    <th>كميه استخدام الآله الجديده قبل </th>
                                    <th>كميه استخدام الآله الجديده بعد </th>
                                    <th>التاريخ</th>
                                    <th>الملاحظات</th>
                                    <th>العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $d)
                                    <tr>
                                        <td>{{ $d->supplies->name }}</td>
                                        <td>{{ $d->transferred_quantity }}</td>
                                        <td>{{ $d->old_machine->name }}</td>
                                        <td>{{ $d->old_machine_pre_used }}</td>
                                        <td>{{ $d->old_machine_used }}</td>
                                        <td>{{ $d->new_machine->name }}</td>
                                        <td>{{ $d->new_machine_pre_used }}</td>
                                        <td>{{ $d->new_machine_used }}</td>
                                        <td>{{ $d->date }}</td>
                                        <td>{{ $d->notes }}</td>
                                        <td>
                                            <!--<a-->
                                            <!--    href="{{ route('dashboard.supplies.exchange_machine_supplies_edit_view', $d->id) }}"><i-->
                                            <!--        class="fa fa-edit" style="color: orange"></i></a>-->
                                            @if(Auth::user()->hasRole('admin'))
                                                <a
                                                    href="{{ route('dashboard.supplies.exchange_delete_machine_supplies', $d->id) }}"><i
                                                        class="fa fa-trash" style="color: red"></i></a>
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach
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
            suppliesRegisterDatatable = $('#suppliestable').DataTable({
                // "processing": true,
                // "serverSide": true,
                "pageLength": 10,
                "lengthMenu": [
                    [10, 25, 50, 100, 200, 300, 500, 1000, 2000, 5000, 10000],
                    [10, 25, 50, 100, 200, 300, 500, 1000, 2000, 5000, 10000]
                ],
                dom: 'lBfrtip',
                // buttons: [
                //     'copy', 'csv', 'excel', 'pdf', 'print'
                // ],
                buttons: [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5',
                    'print',
                    'pdfHtml5'
                ],

            });
        }

        $(function() {
            setsuppliesRegisterDataTable();
            $('select').select2({
                width: '100%'
            });
        });
        $(document).ready(function() {
            // $('table').wrap("<div style='height: 320px;overflow-y: auto;'></div>");
        });
    </script>
@endsection
