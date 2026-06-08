@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">
            <section class="content-header">
                <h1>التقارير</h1>
                <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                    <li class="active">التقارير</li>
                </ol>
            </section>
            <section class="content" style='padding-bottom: 0px !important;'>

                <div class="box box-primary">

                    <div class="box-header with-border">
                        <h3 class="box-title" style="margin-bottom: 15px">التقارير</h3>
                    </div>

                    <div class="box-body" style="min-height: 250px">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                @if (Auth::user()->can('read_machine_reports'))
                                <button class="btn btn-block btn-info"  data-toggle="modal" data-target="#machineCartModal">تقرير الألات</button>
                                @endif
                                <!-- Modal -->
                                <div id="machineCartModal" class="modal fade" role="dialog">
                                    <div class="modal-dialog">

                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;
                                                </button>
                                                <h4 class="modal-title">التفاصيل</h4>
                                            </div>
                                            {{-- start form --}}
                                            <form action="{{ route('dashboard.reports.machine_report') }}" method="get" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>الآلات</label>
                                                        @php
                                                            $machines= App\Machines::latest()->get();
                                                        @endphp
                                                        <select class="form-control select2-js" name="machine_id" id="machine">
                                                            @foreach ($machines as $machine)
                                                            <option value="{{$machine->id}}">{{$machine->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>من تاريخ</label>
                                                        <input type="date" name="date_from" class="form-control" style="position: relative" value="<?php
                                                        $dt = date("Y-m-d");
                                                        echo date( "Y-m-d", strtotime('-29 day', strtotime($dt)) ); ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>إلي تاريخ</label>
                                                        <input type="date" name="date_to" class="form-control" style="position: relative" value="<?php echo date('Y-m-d'); ?>">
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success">عرض</button>
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">إغلاق
                                                    </button>
                                                </div>
                                            </form>
                                            {{-- end form --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                @if (Auth::user()->can('read_machine_supplie_reports'))
                                <button class="btn btn-block btn-info"  data-toggle="modal" data-target="#machineSupplies">تقرير حركة المستلزم</button>
                                @endif
                                <!-- Modal -->
                                <div id="machineSupplies" class="modal fade" role="dialog">
                                    <div class="modal-dialog">

                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;
                                                </button>
                                                <h4 class="modal-title">التفاصيل</h4>
                                            </div>
                                            {{-- start form --}}
                                            <form action="{{ route('dashboard.reports.machine_supplies') }}" method="get" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>المستلزمات</label>
                                                        @php
                                                            $supplies= App\Supplies::latest()->get();
                                                        @endphp
                                                        <select class="form-control select2-js" name="supplie_id" id="supplie_id">
                                                            @foreach ($supplies as $supply)
                                                            <option value="{{$supply->id}}">{{$supply->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>من تاريخ</label>
                                                        <input type="date" name="date_from" class="form-control" style="position: relative" value="<?php
                                                        $dt = date("Y-m-d");
                                                        echo date( "Y-m-d", strtotime('-29 day', strtotime($dt)) ); ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>إلي تاريخ</label>
                                                        <input type="date" name="date_to" class="form-control" style="position: relative" value="<?php echo date('Y-m-d'); ?>">
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success">عرض</button>
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">إغلاق
                                                    </button>
                                                </div>
                                            </form>
                                            {{-- end form --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                @if (Auth::user()->can('read_special_damage_reports'))
                                <button class="btn btn-block btn-info" data-toggle="modal"
                                    data-target="#damageSpecialCartModal">تقرير مقاسات خاصه  </button>
                                @endif
                                <!-- Modal -->
                                <div id="damageSpecialCartModal" class="modal fade" role="dialog">
                                    <div class="modal-dialog">

                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;
                                                </button>
                                                <h4 class="modal-title">التفاصيل</h4>
                                            </div>
                                            {{-- start form --}}
                                            <form action="{{ route('dashboard.reports.damage_special') }}" method="get" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                <div class="modal-body">
                                                    {{--<div class="form-group">
                                                        <label>الآلات</label>
                                                        @php
                                                            $machines= App\Machines::latest()->get();
                                                        @endphp
                                                        <select class="form-control select2-js" name="machine_id" id="machine">
                                                            @foreach ($machines as $machine)
                                                            <option value="{{$machine->id}}">{{$machine->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>--}}
                                                    <div class="form-group">
                                                        <label>من تاريخ</label>
                                                        <input type="date" name="date_from" class="form-control" style="position: relative" value="<?php
                                                        $dt = date("Y-m-d");
                                                        echo date( "Y-m-d", strtotime('-29 day', strtotime($dt)) ); ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>إلي تاريخ</label>
                                                        <input type="date" name="date_to" class="form-control" style="position: relative" value="<?php echo date('Y-m-d'); ?>">
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success">عرض</button>
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">إغلاق
                                                    </button>
                                                </div>
                                            </form>
                                            {{-- end form --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                @if (Auth::user()->can('read_confirm_notes_reports'))
                                <button class="btn btn-block btn-info"  data-toggle="modal" data-target="#confirm_notes">تقرير ملحوظة التأكيد</button>
                                @endif
                                <!-- Modal -->
                                <div id="confirm_notes" class="modal fade" role="dialog">
                                    <div class="modal-dialog">

                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;
                                                </button>
                                                <h4 class="modal-title">التفاصيل</h4>
                                            </div>
                                            {{-- start form --}}
                                            <form action="{{ route('dashboard.reports.confirm_notes') }}" method="get" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>من تاريخ</label>
                                                        <input type="date" name="date_from" class="form-control" style="position: relative" value="<?php
                                                        $dt = date("Y-m-d");
                                                        echo date( "Y-m-d", strtotime('-29 day', strtotime($dt)) ); ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>إلي تاريخ</label>
                                                        <input type="date" name="date_to" class="form-control" style="position: relative" value="<?php echo date('Y-m-d'); ?>">
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success">عرض</button>
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">إغلاق
                                                    </button>
                                                </div>
                                            </form>
                                            {{-- end form --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                @if (Auth::user()->can('read_operation_order_results_reports'))
                                <button class="btn btn-block btn-info"  data-toggle="modal" data-target="#operation_order_results">تقرير ناتج امر التشغيل</button>
                                @endif
                                <!-- Modal -->
                                <div id="operation_order_results" class="modal fade" role="dialog">
                                    <div class="modal-dialog">

                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;
                                                </button>
                                                <h4 class="modal-title">التفاصيل</h4>
                                            </div>
                                            {{-- start form --}}
                                            <form action="{{ route('dashboard.reports.operation_order_results') }}" method="get" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>من تاريخ</label>
                                                        <input type="date" name="date_from" class="form-control" style="position: relative" value="<?php
                                                        $dt = date("Y-m-d");
                                                        echo date( "Y-m-d", strtotime('-29 day', strtotime($dt)) ); ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>إلي تاريخ</label>
                                                        <input type="date" name="date_to" class="form-control" style="position: relative" value="<?php echo date('Y-m-d'); ?>">
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success">عرض</button>
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">إغلاق
                                                    </button>
                                                </div>
                                            </form>
                                            {{-- end form --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                @if (Auth::user()->can('read_employees_performance_reports'))
                                <button class="btn btn-block btn-info"  data-toggle="modal" data-target="#employeesPerformance">تقرير اداء الموظفين</button>
                                @endif
                                <!-- Modal -->
                                <div id="employeesPerformance" class="modal fade" role="dialog">
                                    <div class="modal-dialog">

                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;
                                                </button>
                                                <h4 class="modal-title">التفاصيل</h4>
                                            </div>
                                            {{-- start form --}}
                                            <form action="{{ route('dashboard.reports.employees_performance') }}" method="get" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>الآلات</label>
                                                        @php
                                                            $machines= App\Machines::latest()->get();
                                                        @endphp
                                                        <select class="form-control select2-js" name="machine_id" id="machine">
                                                            @foreach ($machines as $machine)
                                                            <option value="{{$machine->id}}">{{$machine->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>من تاريخ</label>
                                                        <input type="date" name="date_from" class="form-control" style="position: relative" value="<?php
                                                        $dt = date("Y-m-d");
                                                        echo date( "Y-m-d", strtotime('-29 day', strtotime($dt)) ); ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>إلي تاريخ</label>
                                                        <input type="date" name="date_to" class="form-control" style="position: relative" value="<?php echo date('Y-m-d'); ?>">
                                                    </div>

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success">عرض</button>
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">إغلاق
                                                    </button>
                                                </div>
                                            </form>
                                            {{-- end form --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                @if (Auth::user()->can('read_supplies_reports'))
                                <a href=" {{route('dashboard.reports.supplies')}}" class="btn btn-block btn-info">تقرير المستلزمات</a>
                                @endif

                            </div>

                            <div class="col-md-4 mb-3">
                                @if (Auth::user()->can('read_machine_supplie_inventory_reports'))
                                <button class="btn btn-block btn-info"  data-toggle="modal" data-target="#machineSupplieInventory">تقرير جرد مستلزمات المكينة</button>
                                @endif
                                <!-- Modal -->
                                <div id="machineSupplieInventory" class="modal fade" role="dialog">
                                    <div class="modal-dialog">

                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;
                                                </button>
                                                <h4 class="modal-title">التفاصيل</h4>
                                            </div>
                                            {{-- start form --}}
                                            <form action="{{ route('dashboard.reports.machine_supplie_inventory') }}" method="get" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>الآلات</label>
                                                        @php
                                                            $machines= App\Machines::latest()->get();
                                                        @endphp
                                                        <select class="form-control select2-js" name="machine_id" id="machine">
                                                            @foreach ($machines as $machine)
                                                            <option value="{{$machine->id}}">{{$machine->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <!--
                                                    <div class="form-group">
                                                        <label>من تاريخ</label>
                                                        <input type="date" name="date_from" class="form-control" style="position: relative" value="<?php
                                                        $dt = date("Y-m-d");
                                                        echo date( "Y-m-d", strtotime('-29 day', strtotime($dt)) ); ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>إلي تاريخ</label>
                                                        <input type="date" name="date_to" class="form-control" style="position: relative" value="<?php echo date('Y-m-d'); ?>">
                                                    </div>
                                                    -->

                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success">عرض</button>
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">إغلاق
                                                    </button>
                                                </div>
                                            </form>
                                            {{-- end form --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                @if (Auth::user()->can('read_machine_supplie_inventory_reports'))
                                <button class="btn btn-block btn-info"  data-toggle="modal" data-target="#machineSupplieUsedInventory">تقرير جرد استخدام مستلزمات للمكينة</button>
                                @endif
                                <!-- Modal -->
                                <div id="machineSupplieUsedInventory" class="modal fade" role="dialog">
                                    <div class="modal-dialog">

                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;
                                                </button>
                                                <h4 class="modal-title">التفاصيل</h4>
                                            </div>
                                            {{-- start form --}}
                                            <form action="{{ route('dashboard.reports.machine_supplie_used_inventory') }}" method="get" enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>الآله</label>
                                                        @php
                                                            $machines= App\Machines::latest()->get();
                                                        @endphp
                                                        <select class="form-control select2-js" name="machine_id" id="machines">
                                                            @foreach ($machines as $machine)
                                                            <option value="{{$machine->id}}">{{$machine->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>المستلزم</label>
                                                        <select class="form-control select2-js" name="supplie_id" id="machine_supplies">
                                                        </select>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>من تاريخ</label>
                                                        <input type="date" name="date_from" class="form-control" style="position: relative" value="<?php
                                                        $dt = date("Y-m-d");
                                                        echo date( "Y-m-d", strtotime('-29 day', strtotime($dt)) ); ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>إلي تاريخ</label>
                                                        <input type="date" name="date_to" class="form-control" style="position: relative" value="<?php echo date('Y-m-d'); ?>">
                                                    </div>


                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success">عرض</button>
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">إغلاق
                                                    </button>
                                                </div>
                                            </form>
                                            {{-- end form --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
<br>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            @if (Auth::user()->can('read_special_damage_reports'))
                                <button class="btn btn-block btn-info" data-toggle="modal"
                                    data-target="#scraps_reportCartModal">تقرير الخرده </button>
                            @endif
                            <!-- Modal -->
                            <div id="scraps_reportCartModal" class="modal fade" role="dialog">
                                <div class="modal-dialog">

                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;
                                            </button>
                                            <h4 class="modal-title">التفاصيل</h4>
                                        </div>
                                        {{-- start form --}}
                                        <form action="{{ route('dashboard.reports.scraps_report') }}" method="get"
                                            enctype="multipart/form-data">
                                            {{ csrf_field() }}
                                            <div class="modal-body">

                                                <div class="form-group">
                                                    <label>من تاريخ</label>
                                                    <input type="date" name="date_from" class="form-control"
                                                        style="position: relative" value="<?php
                                                        $dt = date('Y-m-d');
                                                        echo date('Y-m-d', strtotime('-29 day', strtotime($dt))); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>إلي تاريخ</label>
                                                    <input type="date" name="date_to" class="form-control"
                                                        style="position: relative" value="<?php echo date('Y-m-d'); ?>">
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">عرض</button>
                                                <button type="button" class="btn btn-default" data-dismiss="modal">إغلاق
                                                </button>
                                            </div>
                                        </form>
                                        {{-- end form --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            @if (Auth::user()->can('read_special_damage_reports'))
                                <button class="btn btn-block btn-info" data-toggle="modal"
                                    data-target="#pieces_reportCartModal">تقرير الفضل </button>
                            @endif
                            <!-- Modal -->
                            <div id="pieces_reportCartModal" class="modal fade" role="dialog">
                                <div class="modal-dialog">

                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;
                                            </button>
                                            <h4 class="modal-title">التفاصيل</h4>
                                        </div>
                                        {{-- start form --}}
                                        <form action="{{ route('dashboard.reports.pieces_report') }}" method="get"
                                            enctype="multipart/form-data">
                                            {{ csrf_field() }}
                                            <div class="modal-body">

                                                <div class="form-group">
                                                    <label>من تاريخ</label>
                                                    <input type="date" name="date_from" class="form-control"
                                                        style="position: relative" value="<?php
                                                        $dt = date('Y-m-d');
                                                        echo date('Y-m-d', strtotime('-29 day', strtotime($dt))); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label>إلي تاريخ</label>
                                                    <input type="date" name="date_to" class="form-control"
                                                        style="position: relative" value="<?php echo date('Y-m-d'); ?>">
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">عرض</button>
                                                <button type="button" class="btn btn-default" data-dismiss="modal">إغلاق
                                                </button>
                                            </div>
                                        </form>
                                        {{-- end form --}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            @if (Auth::user()->can('read_machine_supplie_reports'))
                                <button class="btn btn-block btn-info"  data-toggle="modal" data-target="#newMachineSupplies">تقرير حركة المستلزم (جديد)</button>
                            
                                <div id="newMachineSupplies" class="modal fade" role="dialog">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;
                                                </button>
                                                <h4 class="modal-title">التفاصيل</h4>
                                            </div>
                                            <form action="{{ route('dashboard.reports.new_machine_supplies') }}" method="GET">
                                                {{ csrf_field() }}
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>المستلزم <span class="text-danger">*</span></label>
                                                        <select class="form-control select2-js" name="supplie_id" id="supplie_id">
                                                            @foreach (App\Supplies::latest()->get() as $supply)
                                                            <option value="{{ $supply->id }}">{{ $supply->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>من تاريخ <span class="text-danger">*</span></label>
                                                        <input type="date" name="date_from" class="form-control" style="position: relative" value="<?php echo date('Y-m-d'); ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>إلي تاريخ <span class="text-danger">*</span></label>
                                                        <input type="date" name="date_to" class="form-control" style="position: relative" value="<?php echo date('Y-m-d'); ?>">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success">عرض</button>
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">إغلاق
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <button class="btn btn-block btn-info"  data-toggle="modal" data-target="#machinePerformance">تقرير انتاجية الماكينة</button>
                            <!-- Modal -->
                            <div id="machinePerformance" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;
                                            </button>
                                            <h4 class="modal-title">تقرير انتاجية الماكينة</h4>
                                        </div>
                                        {{-- start form --}}
                                        <form action="{{ route('dashboard.reports.machinePerformance') }}" method="get">
                                            {{ csrf_field() }}
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>الآلة <span>*</span></label>
                                                    @php $machines = App\Machines::latest()->get(); @endphp
                                                    <select class="form-control" name="machine_id" required>
                                                        @foreach($machines as $machine)
                                                        <option value="{{$machine->id}}">{{$machine->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label>من تاريخ <span>*</span></label>
                                                    <input type="date" name="date_from" class="form-control" style="position: relative" value="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}" required>
                                                </div>

                                                <div class="form-group">
                                                    <label>إلي تاريخ <span>*</span></label>
                                                    <input type="date" name="date_to" class="form-control" style="position: relative" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">عرض</button>
                                                <button type="button" class="btn btn-default" data-dismiss="modal">إغلاق
                                                </button>
                                            </div>
                                        </form>
                                        {{-- end form --}}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <button class="btn btn-block btn-info"  data-toggle="modal" data-target="#employeePerformance">تقرير انتاجية الموظف</button>
                            <!-- Modal -->
                            <div id="employeePerformance" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;
                                            </button>
                                            <h4 class="modal-title">تقرير انتاجية الموظف</h4>
                                        </div>
                                        {{-- start form --}}
                                        <form action="{{ route('dashboard.reports.employeePerformance') }}" method="get">
                                            {{ csrf_field() }}
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>النوع <span>*</span></label>
                                                    <select class="form-control" name="type" id="selectedValue" onchange="showEmployeeOrUser()" required>
                                                        <option value="" selected disabled>-- اختر نوع الموظف --</option>
                                                        <option value="user">مستخدم</option>
                                                        <option value="employee">موظف</option>
                                                    </select>
                                                </div>

                                                <div class="form-group" id="userInput" style="display: none;">
                                                    <label>المستخدم <span>*</span></label>
                                                    @php $users = App\User::where('branch_id', 6)->get(); @endphp
                                                    <select class="form-control" name="user_id" id="userSelect">
                                                        @foreach($users as $user)
                                                        <option value="{{$user->id}}">{{$user->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group" id="employeeInput" style="display: none;">
                                                    <label>الموظف <span>*</span></label>
                                                    @php $employees = App\Employee::where('branch_id', 6)->where('job_id', 2)->where('active', 1)->get(); @endphp
                                                    <select class="form-control" name="employee_id" id="employeeSelect">
                                                        @foreach($employees as $employee)
                                                        <option value="{{$employee->id}}">{{$employee->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label>من تاريخ <span>*</span></label>
                                                    <input type="date" name="date_from" class="form-control" style="position: relative" value="{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}" required>
                                                </div>

                                                <div class="form-group">
                                                    <label>إلي تاريخ <span>*</span></label>
                                                    <input type="date" name="date_to" class="form-control" style="position: relative" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">عرض</button>
                                                <button type="button" class="btn btn-default" data-dismiss="modal">إغلاق
                                                </button>
                                            </div>
                                        </form>
                                        {{-- end form --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>

            </section>

    </div>



@endsection
@section('scripts')
<script>

    $("#machines").change(function(){
        $.ajax({
            url: "{{route('dashboard.supplies.getSuppliesByMachineReport')}}?machine_id="+$(this).val(),
            method:"GET",
            success:function(data){
                $("#machine_supplies").html(data.html);
            }
        })
    });

    $(function(){
        $('select').select2({
            width: '100%'
        });
    });
    $(document).ready(function(){
    });
    
    function showEmployeeOrUser() {
        var select = document.getElementById("selectedValue");

        var userInput = document.getElementById("userInput");
        var userSelect = document.getElementById("userSelect");

        var employeeInput = document.getElementById("employeeInput");
        var employeeSelect = document.getElementById("employeeSelect");

        var selectedValue = select.value;
        if(selectedValue == "user") {
            userInput.style.display = "block";
            userSelect.disabled = false;

            employeeInput.style.display = "none";
            employeeSelect.disabled = true;
        }
        else if(selectedValue == "employee") {
            employeeInput.style.display = "block";
            employeeSelect.disabled = false;

            userInput.style.display = "none";
            userSelect.disabled = true;
        }
    }
</script>
@endsection
