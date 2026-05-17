@extends('layouts.dashboard.app')

@section('content')
    <div class="content-wrapper">
        <section class="content container-fluid">
            <section class="content-header">
                <h1>ناتج أمر التشغيل الخارجي</h1>
                <ol class="breadcrumb">
                    <li><a href=" {{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                    <li> <a href=" {{ route('dashboard.operation_order_results.index') }}">ناتج امر التشغيل الخارجي</a></li>
                    <li class="active">@lang('site.add')</li>

                </ol>
            </section>
            <section class="content">

                <div class="box box-primary">

                    <div class="box-header">
                        <h3 class="box-title">@lang('site.add')</h3>
                    </div>

                    <div class="row" style="margin-bottom:10px;padding: 0 10px 0 10px;">
                        <div id="opertOredInfo" class="alert alert-success row"
                            style="padding: 0 10px 2px 10px;margin: 0 10px 0 10px;border: 2px solid #E08E0B;display:none">
                        </div>
                    </div>

                    <div class="box-body">
                        @include('partials._errors')
                        <form action="{{ route('dashboard.operation_order_results.store_out') }}" method="POST"
                            enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('post') }}

                            <div id="supply_errors" hidden>
                                <div class="alert alert-danger">
                                    كميه المستلزم غير مناسبه
                                </div>
                            </div>
                            <div class="form-group">
                                <label>@lang('site.operation_order')*</label>
                                <select name="order_details_id" class="form-control  select2-js" id="opertOrdId">
                                    <option value="">@lang('site.operation_order')</option>
                                    @foreach ($operationOrdrDetails as $operationOrdrDetail)
                                        <option @if (!$operationOrdrDetail->operationOrder->machine_access) disabled @endif
                                         value="{{ $operationOrdrDetail->id }}"
                                            {{ old('order_details_id') == $operationOrdrDetail->id ? 'selected' : '' }}>
                                            {{ $operationOrdrDetail->operation_order_id . '->' . $operationOrdrDetail->id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>@lang('site.old_item_quantity')*</label>
                                    <input type="number" step="any" min="0" id="old_item_quantity"
                                        name="old_item_quantity" class="form-control" value="{{ old('old_item_quantity') }}"
                                        required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>@lang('site.actual_output')*</label>
                                    <input type="number" step="any" min="0" id="actual_output"
                                        name="actual_output" class="form-control" value="{{ old('actual_output') }}"
                                        required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>*السمك</label>
                                    <input type="number" id="thickness" step="any" min="0" name="thickness"
                                        class="form-control" value="{{ old('thickness') }}" required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>الوزن*</label>
                                    <input type="number" id="weight" step="any" min="0" name="weight"
                                        class="form-control" value="{{ old('weight') }}" required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>عدد ألواح او كويل*</label>
                                    <input type="number" id="supply_quantity" step="any" min="0"
                                        name="supply_quantity" class="form-control" value="{{ old('supply_quantity') }}"
                                        required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label>طول اللوح او الكويل*</label>
                                    <input type="number" id="supply_length" step="any" min="0"
                                        name="supply_length" class="form-control" value="{{ old('supply_length') }}"
                                        required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>اجمالي الطول المستخدم*</label>
                                    <input type="number" id="total_used_length" step="any" min="0"
                                        name="total_used_length" class="form-control"
                                        value="{{ old('total_used_length') }}" readonly required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>@lang('site.employees')*</label>
                                    <select name="employee_id[]" class="form-control  select2-js" multiple="">
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}"
                                                {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            {{-- pieces --}}
                            <div class="form-group" style="border: 3px solid #33B35A;border-radius: 8px;padding:5px">
                                <div id="errors" hidden>
                                    <div class="alert alert-danger">
                                        من فضلك ادخل كل البيانات للصنف
                                    </div>
                                </div>
                                <div id="damage_name_found_error" hidden>
                                    <div class="alert alert-danger">
                                        للا يجوز لانه مكود قبل ذلك
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>اضافة الي خرده سابقه</label>
                                        <select class="form-control  select2-js" id="old_damage_id">
                                            <option value="">@lang('site.damage')</option>
                                            @foreach ($scrap as $damage)
                                                <option value="{{ $damage->id }}">{{ $damage->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>عدد الخرده</label>
                                        <input type="number" id="damage_quantity" class="form-control">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>@lang('site.length')*</label>
                                        <input type="number" id="damage_length" step="any" min="0"
                                            class="form-control">
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label>وزن الخرده*</label>
                                        <input type="number" id="damage_weight" step="any" min="0"
                                            class="form-control">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>إسم الخرده*</label>
                                        <input type="text" id="damage_name" class="form-control">
                                        <div id="damage_name_list"
                                            style="position: absolute;
                                        max-height: 145px;
                                        overflow-y: scroll;
                                        z-index: 1;
                                        background: white;
                                        width: 94%;">
                                        </div>
                                        <input type="hidden" name="" id="damage_name_found">
                                    </div>

                                    <div class="form-group col-md-4" hidden>
                                        <label>@lang('site.damage_price')</label>
                                        <input type="number" hidden value="0" id="damage_price"
                                            class="form-control">
                                    </div>
                                    <div class="form-group col-md-4" hidden>
                                        <label>*نوع الفضل</label>
                                        <select class="form-control" id="damage_type">
                                            <option value="scrap">خرده</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            {{-- pieces --}}

                            <div class="form-group">
                                <button type="button" class="btn btn-block btn-sm btn-info" id="add">
                                    <i class="fa  fa-chevron-down"></i>
                                </button>
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
                                                {{-- <th>@lang('site.width')</th> --}}
                                                {{-- <th>@lang('site.damage_thickness')</th> --}}
                                                <th>@lang('site.damage_weight')</th>
                                                <th>حذف</th>
                                            </tr>
                                        </thead>
                                        <tbody class="to-append">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            {{-- end table form --}}

                            <div class="form-group">
                                <label>ملاحظات</label>
                                <input placeholder="@lang('site.notes')" type="text" name="notes"
                                    class="form-control">
                            </div>


                            <div class="form-group">
                                <button type="submit" onclick="submitForm(this);" class="btn btn-primary"><i
                                        class="fa fa-plus"></i> @lang('site.add')</button>
                                <!--<a id="del_edit" class="btn btn-warning"><i class="fa fa-edit"></i>-->
                                <!--    @lang('site.edit')</a>-->

                                <a id="del_edit" class="btn btn-warning"
                                    style="font-weight: bolder;
                                            font-size: 29px;
                                            justify-content: center;
                                            height: 36px;
                                            display: inline-flex;
                                            align-items: center;">x
                                </a>
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
        $("#damage_name").keyup(function() {
            $.ajax({
                url: `{{ route('dashboard.auto_complete_second') }}?term=` + $(this).val(),
                type: 'GET',
                success: function(data) {
                    if (data.length < 1) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                    // Assuming data is an array of items
                    var listHtml = "<ul>"; // Opening <ul> tag

                    // Iterate over the data array
                    for (var i = 0; i < data.length; i++) {
                        // Create an <li> element for each item
                        listHtml += "<li>" + data[i] + "</li>";
                    }
                    if ($("#damage_name").val() != '' && data.length != 0) {
                        $("#damage_name_found").val('true');
                    } else if ($("#damage_name").val() != '' && data.length == 0) {
                        $("#damage_name_found").val('false');
                    }
                    listHtml += "</ul>"; // Closing </ul> tag
                    $("#damage_name_list").html(listHtml);


                },
                error: function() {
                    console.log('error');
                }
            })
        })
    </script>
    <script>
        var used = [];

        // function submitForm(btn) {
        //     // disable the button
        //     btn.disabled = true;
        //     // submit the form
        //     btn.form.submit();
        // }
        $("#opertOrdId").change(function() {
            let route =
                `{{ route('dashboard.operation_order_results.del_edit', ':id') }}`; // قالب الرابط مع متغير وهمي
            route = route.replace(':id', $(this).val()); // استبدال المتغير بالقيمة المختارة

            document.getElementById("del_edit").setAttribute("href", route);
            $.ajax({
                url: "{{ route('dashboard.operation_order_results.get_opertOrderInfo') }}?operation_order_detail_id=" +
                    $(this).val(),
                method: 'GET',
                success: function(data) {
                    $('#opertOredInfo').css('display', 'block');
                    $('#opertOredInfo').html(data.html);
                    used = data.used;
                }
            });

            $.ajax({
                url: "{{ route('dashboard.operation_order_results.get_opertOrderWeight') }}?operation_order_detail_id=" +
                    $(this).val() + '&&actual_output=' + $('#actual_output').val() + '&&thickness=' + $(
                        '#thickness').val(),
                method: 'GET',
                success: function(data) {
                    $('#weight').val(data.weight);
                }
            });

            $.ajax({
                url: "{{ route('dashboard.operation_order_results.get_damageWeight') }}?operation_order_detail_id=" +
                    $(this).val() + '&&damage_quantity=' + $('#damage_quantity').val() +
                    '&&damage_length=' + $('#damage_length').val() + '&&thickness=' + $('#thickness').val(),
                method: 'GET',
                success: function(data) {
                    $('#damage_weight').val(data.weight);
                }
            });
            $.ajax({
                url: "{{ route('dashboard.operation_order_results.get_damageWeight') }}?operation_order_detail_id=" +
                    $(this).val() + '&&damage_quantity=' + $('#damage_quantity1').val() +
                    '&&damage_length=' + $('#damage_length1').val() + '&&thickness=' + $('#thickness')
                    .val(),
                method: 'GET',
                success: function(data) {
                    $('#damage_weight1').val(data.weight);
                }
            });

        });

        $("#del_edit").on('click', function(event) {
            event.preventDefault();
            const link = document.getElementById('del_edit');
            if (link.getAttribute('href')) {
                var choice = confirm('هل انت متأكد من الحذف');

                if (choice) {
                    window.location.href = this.getAttribute('href');
                }
            }
        })

        $("#actual_output").change(function() {
            $.ajax({
                url: "{{ route('dashboard.operation_order_results.get_opertOrderWeight') }}?operation_order_detail_id=" +
                    $("#opertOrdId").val() + '&&actual_output=' + $('#actual_output').val() +
                    '&&thickness=' + $('#thickness').val(),
                method: 'GET',
                success: function(data) {
                    $('#weight').val(data.weight);
                }
            });

        });
        $("#thickness").change(function() {
            $.ajax({
                url: "{{ route('dashboard.operation_order_results.get_opertOrderWeight') }}?operation_order_detail_id=" +
                    $("#opertOrdId").val() + '&&actual_output=' + $('#actual_output').val() +
                    '&&thickness=' + $('#thickness').val(),
                method: 'GET',
                success: function(data) {
                    $('#weight').val(data.weight);
                }
            });

            $.ajax({
                url: "{{ route('dashboard.operation_order_results.get_damageWeight') }}?operation_order_detail_id=" +
                    $("#opertOrdId").val() + '&&damage_quantity=' + $('#damage_quantity').val() +
                    '&&damage_length=' + $('#damage_length').val() + '&&thickness=' + $('#thickness').val(),
                method: 'GET',
                success: function(data) {
                    $('#damage_weight').val(data.weight);
                }
            });
            $.ajax({
                url: "{{ route('dashboard.operation_order_results.get_damageWeight') }}?operation_order_detail_id=" +
                    $("#opertOrdId").val() + '&&damage_quantity=' + $('#damage_quantity1').val() +
                    '&&damage_length=' + $('#damage_length1').val() + '&&thickness=' + $('#thickness')
                    .val(),
                method: 'GET',
                success: function(data) {
                    $('#damage_weight1').val(data.weight);
                }
            });

        });
        $("#damage_length").change(function() {
            $.ajax({
                url: "{{ route('dashboard.operation_order_results.get_damageWeight') }}?operation_order_detail_id=" +
                    $("#opertOrdId").val() + '&&damage_quantity=' + $('#damage_quantity').val() +
                    '&&damage_length=' + $('#damage_length').val() + '&&thickness=' + $('#thickness').val(),
                method: 'GET',
                success: function(data) {
                    $('#damage_weight').val(data.weight);
                }
            });
        });
        $("#damage_length1").change(function() {
            $.ajax({
                url: "{{ route('dashboard.operation_order_results.get_damageWeight') }}?operation_order_detail_id=" +
                    $("#opertOrdId").val() + '&&damage_quantity=' + $('#damage_quantity1').val() +
                    '&&damage_length=' + $('#damage_length1').val() + '&&thickness=' + $('#thickness')
                    .val(),
                method: 'GET',
                success: function(data) {
                    console.log('success');
                    $('#damage_weight1').val(data.weight);
                },
                error: function() {
                    console.log('error');
                }
            });
        });


        $("#damage_quantity").change(function() {
            $.ajax({
                url: "{{ route('dashboard.operation_order_results.get_damageWeight') }}?operation_order_detail_id=" +
                    $("#opertOrdId").val() + '&&damage_quantity=' + $('#damage_quantity').val() +
                    '&&damage_length=' + $('#damage_length').val() + '&&thickness=' + $('#thickness').val(),
                method: 'GET',
                success: function(data) {
                    $('#damage_weight').val(data.weight);
                }
            });

        });
        $("#damage_quantity1").change(function() {

            $.ajax({
                url: "{{ route('dashboard.operation_order_results.get_damageWeight') }}?operation_order_detail_id=" +
                    $("#opertOrdId").val() + '&&damage_quantity=' + $('#damage_quantity1').val() +
                    '&&damage_length=' + $('#damage_length1').val() + '&&thickness=' + $('#thickness')
                    .val(),
                method: 'GET',
                success: function(data) {
                    $('#damage_weight1').val(data.weight);
                }
            });
        });

        $("#supply_length").keyup(() => {
            $('#total_used_length').val($("#supply_length").val() * $("#supply_quantity").val());
        })

        $("#supply_quantity").keyup(() => {
            $('#total_used_length').val($("#supply_quantity").val() * $("#supply_length").val());
        })

        $('#add').on('click', function() {

            var damage_type_value = $('#damage_type').val(),
                damage_type_name = $('#damage_type option:selected').text(),
                old_damage_id = $('#old_damage_id').val(),
                old_damage_name = $('#old_damage_id option:selected').text(),

                damage_quantity = $('#damage_quantity').val(),
                damage_name = $('#damage_name').val(),
                damage_price = $('#damage_price').val(),
                damage_length = $('#damage_length').val(),
                // damage_width = $('#damage_width').val(),
                damage_thickness = $('#damage_thickness').val(),
                damage_weight = $('#damage_weight').val(),
                damage_name_found = $("#damage_name_found").val(),

                supply_quantity = $('#supply_quantity').val(),
                supply_length = $('#supply_length').val(),


                damage_type = $('#damage_type').val();

            used.forEach((e) => {
                if ((supply_length * supply_quantity) > e) {
                    $('#supply_errors').show();
                    $('#supply_errors').delay(3500).fadeOut(350);
                    exit();
                }
            })
            if (damage_quantity == 0 || damage_quantity == null) {
                $('#errors').show();
                $('#errors').delay(3500).fadeOut(350);
                return false;
            }
            // if (damage_name == 0 || damage_name == null) {
            //     $('#errors').show();
            //     $('#errors').delay(3500).fadeOut(350);
            //     return false;
            // }
            if (damage_name_found == 'true') {
                if (damage_name != '') {
                    $('#damage_name_found_error').show();
                    $('#damage_name_found_error').delay(3500).fadeOut(350);
                    return false;
                }
            }
            if (damage_price < 0 || damage_price == null) {
                $('#errors').show();
                $('#errors').delay(3500).fadeOut(350);
                return false;
            }
            if (damage_length <= 0 || damage_length == null) {
                $('#errors').show();
                $('#errors').delay(3500).fadeOut(350);
                return false;
            }
            // if (damage_width <= 0 || damage_width == null) {
            //     $('#errors').show();
            //     $('#errors').delay(3500).fadeOut(350);
            //     return false;
            // }
            // if (damage_thickness <= 0 || damage_thickness == null) {
            //     $('#errors').show();
            //     $('#errors').delay(3500).fadeOut(350);
            //     return false;
            // }
            if (damage_weight <= 0 || damage_weight == null) {
                $('#errors').show();
                $('#errors').delay(3500).fadeOut(350);
                return false;
            }
            // if (damage_type_value <= 0 || damage_type_value == null) {
            //     $('#errors').show();
            //     $('#errors').delay(3500).fadeOut(350);
            //     return false;
            // }
            if (old_damage_id == 0 || old_damage_id == null) {
                // old_damage_id=null;
                old_damage_name = '';
            }

            $('<tr>').html(
                '<td><p class="">' + damage_type_name +
                '</p><input type="hidden" name="damage_type[]" value="' + damage_type_value + '"></td>' +
                '<td><p class="">' + old_damage_name +
                '</p><input type="hidden" required="required" name="old_damage_id[]" value="' + old_damage_id +
                '"></td>' +
                '<td><p>' + damage_name + '</p><input type="hidden" name="damage_name[]" value="' +
                damage_name + '"></td>' +
                '<td><p>' + damage_quantity + '</p><input type="hidden" name="damage_quantity[]" value="' +
                damage_quantity + '"></td>' +
                '<td><p>' + damage_price + '</p><input type="hidden" name="damage_price[]" value="' +
                damage_price + '"></td>' +
                '<td><p>' + damage_length + '</p><input type="hidden" name="damage_length[]" value="' +
                damage_length + '"></td>' +
                // '<td><p>' + damage_width + '</p> <input type="hidden" name="damage_width[]" value="' + damage_width + '"></td>' +
                // '<td><p>' + damage_thickness + '</p> <input type="hidden" name="damage_thickness[]" value="' + damage_thickness + '"></td>' +
                '<td><p>' + damage_weight + '</p> <input type="hidden" name="damage_weight[]" value="' +
                damage_weight + '"></td>' +
                '<td ><button  onclick="$(this).closest(\'tr\').remove()"  class="btn btn-block btn-danger">حذف</button></td>'
            ).appendTo('#table_items');

            $('#damage_type').val(''),
                $('#old_damage_id').val(''),

                $('#damage_name').val(''),
                $('#damage_quantity').val(0),
                $('#damage_price').val(0),
                $('#damage_length').val(0);
            // $('#damage_width').val(0);
            // $('#damage_thickness').val(0);
            $('#damage_weight').val(0);
        });

        $(function() {



        });

        function submitForm(btn) {

            event.preventDefault();
            var supply_quantity = $('#supply_quantity').val(),
                check = true,
                supply_length = $('#supply_length').val();
            used.forEach((e) => {
                if ((supply_length * supply_quantity) > e) {
                    check = false;
                    $('#supply_errors').show();
                    $('#supply_errors').delay(3500).fadeOut(350);
                    return false;
                    exit();
                } else {
                    check = true;
                }
            })
            if (check) {
                btn.disabled = true;
                // submit the form
                btn.form.submit();
            }
        }
    </script>
@endsection
