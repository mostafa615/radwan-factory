@extends('layouts.dashboard.app')

@section('content')

    <div class="content-wrapper">
        <section class="content container-fluid">
            <section class="content-header">
                <h1> ناتج أمر التشغيل</h1>
                <ol class="breadcrumb">
                    <li><a href=" {{route('dashboard.index')}}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                    <li> <a href=" {{route('dashboard.operation_order_results.index')}}">ناتج أمر  التشغيل</a></li>
                    <li class="active">@lang('site.edit')</li>

                </ol>
            </section>
            <section class="content">

                <div class="box box-primary">

                    <div class="box-header">
                        <h3 class="box-title">@lang('site.edit')</h3>
                    </div>

                    <div class="row" style="margin-bottom:10px;padding: 0 10px 0 10px;">
                        <div id="opertOredInfo" class="alert alert-success row" style="padding: 0 10px 2px 10px;margin: 0 10px 0 10px;border: 2px solid #E08E0B;display:none"> </div>
                    </div>

                    <div class="box-body">
                        @include('partials._errors')
                        <form action="{{route('dashboard.operation_order_results.update', $operationOrderResult)}}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('put') }}

                            <div class="form-group">
                                <label>@lang('site.operation_order')*</label>
                                <select name="order_details_id" class="form-control  select2-js" id="opertOrdId">
                                    <option value="">@lang('site.operation_order')</option>
                                    @foreach ($operationOrdrDetails as $operationOrdrDetail)
                                        <option value="{{$operationOrdrDetail->id}}" {{ $operationOrderResult->order_details_id == $operationOrdrDetail->id ? 'selected' : ''}}>{{$operationOrdrDetail->operation_order_id.' -> '.$operationOrdrDetail->id}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>@lang('site.old_item_quantity')*</label>
                                    <input type="number" name="old_item_quantity" class="form-control" step="any" min="0" value="{{$operationOrderResult->old_item_quantity}}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>@lang('site.actual_output')*</label>
                                    <input type="number" id="actual_output" name="actual_output" class="form-control" min="{{$operationOrderResult->actual_output}}" value="{{$operationOrderResult->actual_output}}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label>*السمك</label>
                                    <input type="number" id="thickness" step="any" min="0" name="thickness" class="form-control" value="{{$operationOrderResult->thickness}}">
                                </div>

                                <div class="form-group col-md-4">
                                    <label>الوزن*</label>
                                    <input type="number" id="weight" step="any" min="0" name="weight" class="form-control" value="{{$operationOrderResult->weight}}">
                                </div>

                                <div class="form-group col-md-4">
                                    <label>اجمالي الطول المستخدم*</label>
                                    <input type="number" id="total_used_length" step="any" min="0" name="total_used_length" class="form-control" value="{{$operationOrderResult->total_used_length}}">
                                </div>
                            </div>

                            {{-- start table form --}}
                            <div class="box">
                                <div class="box-body">
                                    <table class="table text-center " id="table_items">
                                        <thead>
                                        <tr>
                                            <th>@lang('site.damage_type')</th>
                                            <th>@lang('site.add_to_old_damage')</th>
                                            <th>@lang('site.damage_name')</th>
                                            <th>@lang('site.damage_number')</th>
                                            <th>@lang('site.damage_price')</th>
                                            <th>@lang('site.length')</th>
                                            <th>@lang('site.width')</th>
                                            <th>@lang('site.damage_thickness')</th>
                                            <th>@lang('site.damage_weight')</th>
                                            <th>حذف</th>
                                        </tr>
                                        </thead>
                                        <tbody class="">
                                            @if (!@empty($operationOrderResult->orderResultDetails))
                                                @foreach ($operationOrderResult->orderResultDetails as $orderResultDetail)
                                                <tr>
                                                    <td>{{$orderResultDetail->damage_type}}</td>
                                                    <td>{{$orderResultDetail->old_damage_id}}</td>
                                                    <td>{{$orderResultDetail->damage_name}}</td>
                                                    <td>{{$orderResultDetail->damage_quantity}}</td>
                                                    <td>{{$orderResultDetail->damage_price}}</td>
                                                    <td>{{$orderResultDetail->damage_length}}</td>
                                                    <td>{{$orderResultDetail->damage_width}}</td>
                                                    <td>{{$orderResultDetail->damage_thickness}}</td>
                                                    <td>{{$orderResultDetail->damage_weight}}</td>
                                                    @if(Auth()->user()->id == 1)
                                                        <td><a href="{{url('dashboard/order_result_delete_detail/'.$orderResultDetail->id)}}" class="btn btn-danger"><i class="fa fa-trash-o"></i> </a> </td>
                                                    @endif
                                                </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            {{-- end table form --}}



                            <div class="form-group">
                                <label>ملاحظات</label>
                                <input placeholder="@lang('site.notes')" type="text" name="notes" class="form-control" value="{{$operationOrderResult->notes}}">
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-edit"></i> @lang('site.edit')</button>
                            </div>
                        </form>
                    </div><!--end of box-body-->

                </div>

            </section>
        </section>


    </div>

@endsection
@section('scripts')
<script>
    $("#opertOrdId").change(function(){
            $.ajax({
                url: "{{ route('dashboard.operation_order_results.get_opertOrderInfo') }}?operation_order_id=" + $(this).val(),
                method: 'GET',
                success: function(data) {
                    $('#opertOredInfo').css('display','block');
                    $('#opertOredInfo').html(data.html);
                }
            });

    });

    $("#actual_output").change(function(){
        $.ajax({
            url: "{{ route('dashboard.operation_order_results.get_opertOrderWeight') }}?operation_order_detail_id=" + $("#opertOrdId").val() + '&&actual_output=' + $('#actual_output').val() + '&&thickness=' + $('#thickness').val(),
            method: 'GET',
            success: function(data) {
                $('#weight').val(data.weight);
            }
        });

    });
    $("#thickness").change(function(){
        $.ajax({
            url: "{{ route('dashboard.operation_order_results.get_opertOrderWeight') }}?operation_order_detail_id=" + $("#opertOrdId").val() + '&&actual_output=' + $('#actual_output').val() + '&&thickness=' + $('#thickness').val(),
            method: 'GET',
            success: function(data) {
                $('#weight').val(data.weight);
            }
        });
    });

    $(function(){



    });

</script>

@endsection
