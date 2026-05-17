@extends('layouts.dashboard.app')

@section('content')
    <div class="content-wrapper">
        <section class="content container-fluid">
            <section class="content-header">
                <h1>تحويل المستلزمات بين الآلات</h1>
                <ol class="breadcrumb">
                    <li><a href=" {{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
                    <li> <a href=" {{ route('dashboard.supplies.index') }}"> المستلزمات
                        </a></li>
                    <li> تحويل بين
                        الآلات</li>

                </ol>
            </section>
            <section class="content">

                <div class="box box-primary">

                    <div class="box-header">
                        <h3 class="box-title">تحويل المستلزمات بين الآلات</h3>
                    </div>

                    <div class="box-body">
                        @include('partials._errors')
                        <form action="{{ route('dashboard.supplies.exchange_update_machine_supplies', $data->id) }}"
                            method="POST" enctype="multipart/form-data" id="form">
                            {{ csrf_field() }}
                            {{ method_field('post') }}

                            <div class="row">


                                <div class="form-group col-md-4 col-sm-12">
                                    <label>من*</label>
                                    <select name="old_machine_id" class="form-control  select2-js" id="machines" required>
                                        <option value="">@lang('site.machine')</option>
                                        @foreach ($machines as $machine)
                                            <option value="{{ $machine->id }}"
                                                {{ $data->old_machine_id == $machine->id ? 'selected' : '' }}>
                                                {{ $machine->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4 col-sm-12">
                                    <label>مستلزم التشغيل*</label>
                                    <select name="supplie_id" class="form-control  select2-js" id="supplies" required>
                                        <option value="">المستلزمات</option>
                                        @foreach ($supplies as $supply)
                                            <option value="{{ $supply->id }}" data-quantity='{{ $supply->quantity }}'
                                                data-used='{{ $supply->used }}'
                                                {{ $data->supplie_id == $supply->Supplie->id ? 'selected' : '' }}>
                                                {{ $supply->Supplie->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4 col-sm-12">
                                    <label>الى*</label>
                                    <select name="new_machine_id" class="form-control  select2-js" id="" required>
                                        <option value="">@lang('site.machine')</option>
                                        @foreach ($machines as $machine)
                                            <option value="{{ $machine->id }}"
                                                {{ $data->new_machine_id == $machine->id ? 'selected' : '' }}>
                                                {{ $machine->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">

                                <div class="form-group col-md-4 col-sm-12">
                                    <label>الكميه*</label>
                                    <input type="number" name="quantity" value="{{ $data->transferred_quantity }}"
                                        class="form-control" placeholder="الكميه" id="quantity" required>
                                </div>
                                <div class="form-group col-md-4 col-sm-12">
                                    <label>التاريخ*</label>
                                    <input type="date" name="date" value="{{ $data->date }}" class="form-control"
                                        placeholder="التاريخ" id="date">
                                </div>
                                <div class="form-group col-md-4 col-sm-12">
                                    <label>الملاحظه*</label>
                                    <input type="text" name="notes" value="{{ $data->notes }}" class="form-control"
                                        placeholder="الملاحظه" id="notes">
                                </div>
                            </div>






                            <div class="form-group">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-exchange"></i> تعديل</button>
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
        $("#machines").change(function() {
            $.ajax({
                url: "{{ route('dashboard.supplies.getSuppliesByMachine') }}?machine_id=" + $(this).val(),
                method: 'GET',
                success: function(data) {
                    $('#supplies').html(data.html);
                }
            });
        });


        $('#supplies').change(function() {
            var selectedOption = $('#supplies option:selected');
            var selectedValue = selectedOption[0].getAttribute('data-used');
            $("#quantity").attr('placeholder', 'الكيمه المتاحه = ' + selectedValue);
            $("#quantity").attr('max', selectedValue);
            $("#quantity").attr('min', 1);
        });

        $('#form').on('submit', () => {

        })


        // $("form").submit(function(){
        //     $.ajax({
        //         url:"{{ route('dashboard.supplies.exchange_machine_supplies') }}"
        //     })
        // })

        $(function() {
            $('select').select2({
                width: '100%'
            });
        });
    </script>
@endsection
