@extends('layouts.dashboard.app')
@section('css')
    <style>
        td a {
            color: rgb(48, 136, 136);
            text-decoration: rgb(48, 136, 136);
            font-weight: bold;
            text-decoration: underline;
        }

        @media print {
            #printBtn {
                display: none;
            }
        }
    </style>
@endsection
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>تقرير المستلزمات </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                <li class="active">تقرير المستلزمات </li>
            </ol>
        </section>
        <section class="content" style='padding-bottom: 0px !important;'>

            <div class="box box-primary">

                <div class="box-header with-border">

                    <h3 class="box-title" style="margin-bottom: 15px">تقرير المستلزمات </h3>
                    <br>
                    <button id="printBtn" class="btn btn-info" onclick="window.print()">طباعة</button>


                </div>

                <div class="box-body" style="min-height: 250px">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="nav-tabs-custom">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="operationOrderResult">
                                        <h4 style="text-align: center!important">المستلزمات</h4>
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="date-table">
                                                <thead>
                                                    <tr>
                                                        <td>اسم المستلزم</td>
                                                        <td>الكمية</td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($supplies as $supplie)
                                                        <tr>
                                                            <td>{{ $supplie->name }}</td>
                                                            <td>{{ $supplie->quantity }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <br>
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
        $(function() {
            $('select').select2({
                width: '100%'
            });
        });

        $("#date-table").DataTable({
            paging: false,
            serverSide: false,
            dom: 'f',
        })
        $(document).ready(function() {});
    </script>
@endsection



{{-- @extends('layouts.dashboard.app')
@section('css')
    <style>
    td a{
        color: rgb(48, 136, 136);
        text-decoration: rgb(48, 136, 136);
        font-weight: bold;
        text-decoration: underline;
    }
    @media print {
        #printBtn{
            display: none;
        }
    }
    </style>
@endsection
@section('content')

    <div class="content-wrapper">
            <section class="content-header">
                <h1>تقرير المستلزمات </h1>
                <ol class="breadcrumb">
                <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                    <li class="active">تقرير المستلزمات </li>
                </ol>
            </section>
            <section class="content" style='padding-bottom: 0px !important;'>

                <div class="box box-primary">

                    <div class="box-header with-border">

                        <h3 class="box-title" style="margin-bottom: 15px">تقرير المستلزمات </h3>
                        <br>
                        <button id="printBtn" class="btn btn-info" onclick="window.print()">طباعة</button>


                    </div>

                    <div class="box-body" style="min-height: 250px">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="nav-tabs-custom">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="operationOrderResult">
                                            <h4 style="text-align: center!important">المستلزمات</h4>
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <td>اسم المستلزم</td>
                                                            <td>الكمية</td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($supplies as $supplie)
                                                        <tr>
                                                            <td>{{$supplie->name}}</td>
                                                            <td>{{$supplie->quantity}}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <br>
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


    $(function(){
        $('select').select2({
            width: '100%'
        });
    });
    $(document).ready(function(){
    });
</script>
@endsection --}}