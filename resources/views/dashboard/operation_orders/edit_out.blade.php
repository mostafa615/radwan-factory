@extends('layouts.dashboard.app')
@section('css')
<style>
  #supplies+.select2-container {
    overflow: auto !important;
    height: 40px !important;
  }
</style>
@endsection
@section('content')
<div class="content-wrapper">
  <section class="content container-fluid">
    <section class="content-header">
      <h1>تعديل أمر تشغيل خارجي</h1>
      <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> @lang('site.dashboard')</a></li>
        <li><a href="{{ route('dashboard.operation_orders.index_out') }}">التشغيل الخارجي</a></li>
        <li class="active">@lang('site.edit')</li>
      </ol>
    </section>
    <section class="content">
      <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">@lang('site.edit')</h3>
        </div>
        <div class="box-body">
          @include('partials._errors')
          <form action="{{ route('dashboard.operation_orders.update_out', $operationOrder->id) }}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            {{ method_field('post') }}

            @php
            $currentUserId = auth()->user()->id;
            $isOwner = $currentUserId == $operationOrder->created_by;
            $supervisorStoreIds = explode(',', $operationOrder->supervisor_store);
            $isSupervisorStore = in_array($currentUserId, $supervisorStoreIds);
            @endphp

            @if($isOwner)

              {{-- Simplified view like complete_out for the owner --}}
              <div class="row">
                {{-- readonly --}}
                <div class="form-group col-md-6">
                  <label>@lang('site.machine_types')</label>
                  <input type="text" class="form-control" value="{{$operationOrder->machine->MachineType->name}}" readonly>
                  <input type="hidden" name="machine_type_id" value="{{$operationOrder->machine_type_id}}">
                </div>

                {{-- readonly --}}
                <div class="form-group col-md-6">
                  <label>@lang('site.machine')</label>
                  <input type="text" class="form-control" value="{{$operationOrder->machine->name}}" readonly>
                  <input type="hidden" name="machine_id" value="{{$operationOrder->machine_id}}">
                </div>
              </div>

              <div class="row">
                <div class="form-group col-md-4">
                  <label>المستخدمين*</label>
                  <select name="user_id[]" class="form-control select2-js" multiple="">
                    <option disabled>-- اختر من القائمة --</option>
                    @foreach ($users as $user)
                      <option value="{{ $user->id }}" {{ in_array($user->id, explode(',', $operationOrder->user_id)) ? 'selected' : '' }}>
                        {{ $user->name }}
                      </option>
                    @endforeach
                  </select>
                </div>

                {{-- readonly --}}
                <div class="form-group col-md-4">
                  <label>@lang('site.supervisor_store')</label>
                  <select name="supervisor_store[]" class="form-control" readonly>
                    <option value="{{App\User::where('id', $operationOrder->supervisor_store)->first()->id ?? ''}}" selected>{{App\User::where('id', $operationOrder->supervisor_store)->first()->name ?? ''}}</option>
                  </select>
                </div>

                <div class="form-group col-md-4">
                  <label>@lang('site.supervisor_process')*</label>
                  <select name="supervisor_process[]" class="form-control select2-js" multiple="">
                    <option disabled>-- اختر من القائمة --</option>
                    @foreach ($admins as $admin)
                      <option value="{{ $admin->id }}" {{ in_array($admin->id, explode(',', $operationOrder->supervisor_process)) ? 'selected' : '' }}>
                        {{ $admin->name }}
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="row">
                {{-- readonly --}}
                <div class="form-group col-md-4">
                  <label>إسم العميل</label>
                  <input type="text" name="client_name" class="form-control" value="{{$operationOrder->client_name}}" readonly>
                </div>

                {{-- readonly --}}
                <div class="form-group col-md-4">
                  <label>وحدة الخامة المستخدمة</label>
                  <input type="text" name="old_item_unit" class="form-control" value="{{$operationOrder->old_item_unit}}" readonly>
                </div>

                {{-- readonly --}}
                <div class="form-group col-md-4">
                  <label>وحدة الخامة الناتجة</label>
                  <input type="text" name="out_item_unit" class="form-control" value="{{$operationOrder->out_item_unit}}" readonly>
                </div>
              </div>

              {{-- start table form --}}
              <div class="box">
                <div class="box-body">
                  <table class="table text-center " id="table_items">
                    <thead>
                      <tr>
                        <th>@lang('site.operation_supplies')</th>
                        <th>@lang('site.used_item')</th>
                        <th>@lang('site.old_item_quantity')</th>
                        <th>@lang('site.out_item_name')</th>
                        <th>كميه المستلزم</th>
                        <th>@lang('site.length')</th>
                        <th>@lang('site.width')</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($operationOrderDetails as $item)
                      <input type="text" name="operation_order_detail_id[]" value="{{$item->id}}" hidden>
                      <tr>
                        <td min-width="200px">
                          <div class="form-group">
                            <select name="operation_suplies_id[{{$item->id}}][]" class="form-control select2-js" multiple>
                                @foreach($machineSupplies as $supply)
                                <option value="{{$supply->id}}" {{ in_array($supply->supplie_id, explode(',', $item->operation_suplies_id)) ? 'selected' : '' }}>{{$supply->Supplie->name}} : المتبقي {{number_format($supply->used, 2, '.', '')}}</option>
                                @endforeach
                            </select>
                          </div>
                        </td>
                        <td><input type="text" name="item_name[]" id="" value="{{$item->item_name}}" readonly></td>
                        <td><input type="text" name="old_item_quantity[]" id="" value="{{$item->old_item_quantity}}" readonly></td>
                        <td><input type="text" name="out_item_name[]" id="" value="{{$item->out_item_name}}" readonly></td>
                        <td><input type="number" name="quantity[]" id="" value="{{$item->quantity}}" readonly></td>
                        <td><input type="number" name="length[]" id="" value="{{$item->length}}" readonly></td>
                        <td><input type="number" name="width[]" id="" value="{{$item->width}}" readonly></td>

                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
              {{-- end table form --}}

              <div class="row">
                <div class="form-group col-md-6" style="position: relative">
                  <label>@lang('site.date')</label>
                  <input type="date" name="date" class="form-control" value="{{$operationOrder->date}}" readonly>
                </div>

                <div class="form-group col-md-6">
                  <label>ملاحظات المخزن</label>
                  <input type="text" name="notes2" class="form-control" value="{{$operationOrder->notes2}}" readonly>
                </div>
              </div>

              <div class="row">
                <div class="form-group col-md-12">
                  <label>ملاحظات</label>
                  <textarea name="notes" class="form-control" rows="3"></textarea>
                  @if($operationOrder->store_employees)
                    @foreach(explode(',', $operationOrder->store_employees) as $employeeId)
                      <input type="hidden" name="store_employees[]" value="{{ $employeeId }}">
                    @endforeach
                  @endif
                </div>
              </div>

              <div class="form-group">
                <button type="submit" class="btn btn-success editForm" name=" , العمليه رقم {{ $operationOrder->id }}" value="{{ $operationOrder->id }}">
                  <i class="fa fa-check"></i> تعديل الان
                </button>
                <a href="{{route('dashboard.operation_orders.update_is_complete', $operationOrder->id)}}" class="btn btn-danger">تراجع</a>
              </div>

            @elseif($isSupervisorStore)
              {{-- Supervisor store: show create_out style, but pre-filled with old data --}}
              <div class="row">
                <div class="form-group col-md-6">
                  <label>@lang('site.machine_types')*</label>
                  <select name="machine_type_id" class="form-control select2-js" id="machineType" required>
                    <option value="">@lang('site.machine_types')*</option>
                    @foreach ($machineTypes as $machineType)
                    <option value="{{ $machineType->id }}" {{ $operationOrder->machine_type_id == $machineType->id ? 'selected' : '' }}>{{ $machineType->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group col-md-6">
                  <label>@lang('site.machine')*</label>
                  <select name="machine_id" class="form-control select2-js" id="machines" required>
                    <option value="">@lang('site.machine')*</option>
                    @foreach ($machines as $machine)
                    <option value="{{ $machine->id }}" {{ $operationOrder->machine_id == $machine->id ? 'selected' : '' }}>{{ $machine->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-4">
                  <label>المستخدمين</label>
                  <input type="text" class="form-control" value="@foreach($users as $user){{ in_array($user->id, explode(',', $operationOrder->user_id)) ? $user->name : '' }} @endforeach" readonly>
                  @foreach(explode(',', $operationOrder->user_id) as $userId)
                    <input type="hidden" name="user_id[]" value="{{ $userId }}">
                  @endforeach
                </div>
                <div class="form-group col-md-4">
                  <label>@lang('site.supervisor_store')</label>
                  <input type="text" class="form-control" value="@foreach($supervisor_store as $sup){{ in_array($sup->id, explode(',', $operationOrder->supervisor_store)) ? $sup->name : '' }} @endforeach" readonly>
                  @foreach(explode(',', $operationOrder->supervisor_store) as $supervisorId)
                    <input type="hidden" name="supervisor_store[]" value="{{ $supervisorId }}">
                  @endforeach
                </div>
                <div class="form-group col-md-4">
                  <label>@lang('site.supervisor_process')</label>
                  <input type="text" class="form-control" value="@foreach($admins as $admin){{ in_array($admin->id, explode(',', $operationOrder->supervisor_process)) ? $admin->name : '' }} @endforeach" readonly>
                  @foreach(explode(',', $operationOrder->supervisor_process) as $processId)
                    <input type="hidden" name="supervisor_process[]" value="{{ $processId }}">
                  @endforeach
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-4">
                  <label>إسم العميل</label>
                  <select name="client_name" class="form-control select2-js">
                    <option value="{{ $operationOrder->client_name }}" selected>{{ $operationOrder->client_name }}</option>
                    @foreach ($clients as $client)
                    <option value="{{ $client->name }}" {{ $operationOrder->client_name == $client->name ? 'selected' : '' }}> {{ $client->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group col-md-4">
                  <label>وحدة الخامة المستخدمة</label>
                  <input type="text" name="old_item_unit" class="form-control" value="{{ $operationOrder->old_item_unit }}">
                </div>
                <div class="form-group col-md-4">
                  <label>وحدة الخامة الناتجة</label>
                  <input type="text" name="out_item_unit" class="form-control" value="{{ $operationOrder->out_item_unit }}">
                </div>
              </div>
              {{-- start box --}}
              <div class="form-group" style="border: 3px solid #33B35A;border-radius: 8px;padding:5px">
                  <div id="errors" hidden>
                    <div class="alert alert-danger">
                      من فضلك ادخل كل البيانات للصنف
                    </div>
                  </div>

                  <div id="quantity_error" hidden>
                    <div class="alert alert-danger">
                      كميه المستلزم غير صحيحه
                    </div>
                  </div>

                  <div class="row">
                    <div class="form-group col-md-4">
                      <label>@lang('site.operation_supplies')</label>
                      <select class="form-control" readonly>
                        {{-- supplies select, can be filled as needed --}}
                      </select>
                    </div>

                    <div class="form-group  col-md-4">
                      <label>@lang('site.used_item')*</label>
                      <input type="text" id="item_name" class="form-control">
                    </div>

                    <div class="form-group col-md-4">
                      <label>@lang('site.old_item_quantity')*</label>
                      <input type="number" id="old_item_quantity" class="form-control">
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group  col-md-4">
                      <label>@lang('site.out_item_name')*</label>
                      <input type="text" id="out_item_name" class="form-control">
                    </div>

                    <div class="form-group col-md-4">
                      <label>عدد ألواح او كويل*</label>
                      <input type="number" id="out_quantity" class="form-control">
                    </div>

                    <div class="form-group col-md-4">
                      <label>@lang('site.out_quantity')*</label>
                      <input type="number" id="oldItemSuppQnt" class="form-control">
                    </div>
                  </div>
                  <div class="row">
                    <div class="form-group col-md-4">
                      <label>طول ألواح او كويل*</label>
                      <input type="number" step="any" min="0" id="length" class="form-control">
                    </div>

                    <div class="form-group col-md-4">
                      <label>@lang('site.width')*</label>
                      <input type="number" step="any" min="0" id="width" class="form-control">
                    </div>
                  </div>

                  <div class="form-group">
                    <button type="button" class="btn btn-block btn-sm btn-info" id="add">
                      <i class="fa  fa-chevron-down"></i>
                    </button>
                  </div>
                </div>
                {{-- end box --}}
                {{-- start table form --}}
                <div class="box">
                  <div class="box-body">
                    <table class="table text-center" id="table_items">
                      <thead>
                        <tr>
                          <th>@lang('site.operation_supplies')</th>
                          <th>@lang('site.used_item')</th>
                          <th>@lang('site.old_item_quantity')</th>
                          <th>@lang('site.out_item_name')</th>
                          <th>كميه المستلزم</th>
                          <th>@lang('site.out_quantity')</th>
                          <th>@lang('site.length')</th>
                          <th>@lang('site.width')</th>
                          <th>حذف</th>
                        </tr>
                      </thead>
                      <tbody class="to-append">
                        @foreach($operationOrderDetails as $index => $detail)
                        <tr>
                            <td>
                               <select class="form-control select2-js" multiple disabled>
                                   @foreach($machineSupplies as $supply)
                                   <option value="{{$supply->supplie_id}}" {{ in_array($supply->supplie_id, explode(',', $detail->operation_suplies_id)) ? 'selected' : '' }}>{{$supply->Supplie->name}} : المتبقي {{number_format($supply->used, 2, '.', '')}}</option>
                                   @endforeach
                               </select>
                               <input type="hidden" name="operation_order_detail_id[]" value="{{$detail->id}}">
                               @if(!empty($detail->operation_suplies_id))
                                 @foreach(explode(',', $detail->operation_suplies_id) as $supplyId)
                                   <input type="hidden" name="operation_suplies_id[{{$index}}][]" value="{{$supplyId}}">
                                 @endforeach
                               @endif
                             </td>
                            <td><input type="text" name="item_name[]" class="form-control" value="{{$detail->item_name}}" ></td>
                            <td><input type="number" name="old_item_quantity[]" class="form-control" value="{{$detail->old_item_quantity}}" ></td>
                            <td><input type="text" name="out_item_name[]" class="form-control" value="{{$detail->out_item_name}}" ></td>
                            <td><input type="number" name="out_quantity[]" class="form-control" value="{{$detail->out_quantity}}" ></td>
                            <td><input type="number" name="quantity[]" class="form-control" value="{{$detail->quantity}}" ></td>
                            <td><input type="number" name="length[]" class="form-control" value="{{$detail->length}}" ></td>
                            <td><input type="number" name="width[]" class="form-control" value="{{$detail->width}}" ></td>
                            <td><input type="checkbox" name="selected_items[]" value="{{$detail->id}}"> حذف</td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
                {{-- end table form --}}

                <div class="row">
                  <div class="form-group col-md-6">
                    <label>عاملين الاستلام*</label>
                    <select name="store_employees[]" class="form-control select2-js" multiple required>
                      <option disabled>-- اختر من القائمة --</option>
                      @foreach(App\Employee::where('job_id', 2)->where('branch_id', auth()->user()->branch_id)->get() as $employee)
                      <option value="{{$employee->id}}" {{ in_array($employee->id, explode(',', $operationOrder->store_employees ?? '')) ? 'selected' : '' }}>{{ $employee->name }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group col-md-6" style="position: relative">
                    <label>@lang('site.date')*</label>
                    <input type="date" name="date" class="form-control datePicker" value="{{ $operationOrder->date ?? date('Y-m-d') }}">
                  </div>
                </div>

                <div class="row">
                  <div class="form-group col-md-12">
                    <label>ملاحظات</label>
                    <textarea name="notes2" class="form-control" rows="3">{{ $operationOrder->notes2 ?? '' }}</textarea>
                  </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success editForm" name=" , العمليه رقم {{ $operationOrder->id }}" value="{{ $operationOrder->id }}">
                        <i class="fa fa-check"></i> تعديل الان
                      </button>
                </div>
              @else
                {{-- Original complex edit form for non-owners --}}
                <div class="row">

                  <div class="row">
                    {{-- نوع المعدة --}}
                    <div class="form-group col-md-6">
                      <label>@lang('site.machine_types')*</label>
                      <select class="form-control select2-js" id="machineType" name="machine_type_id" required>
                        <option value="">@lang('site.machine_types')*</option>
                        @foreach ($machineTypes as $machineType)
                          <option value="{{ $machineType->id }}" {{ $operationOrder->machine_type_id == $machineType->id ? 'selected' : '' }}>
                            {{ $machineType->name }}
                          </option>
                        @endforeach
                      </select>
                    </div>

                    {{-- المعدة --}}
                    <div class="form-group col-md-6">
                      <label>@lang('site.machine')*</label>
                      <select class="form-control select2-js" id="machines" name="machine_id" required>
                        @foreach ($machines as $machine)
                          <option value="{{ $machine->id }}" {{ $operationOrder->machine_id == $machine->id ? 'selected' : '' }}>
                            {{ $machine->name }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  @php
                  $currentUserId = auth()->user()->id;
                  $supervisorStoreIds = explode(',', $operationOrder->supervisor_store);
                  $isSupervisor = in_array($currentUserId, $supervisorStoreIds);
                  $isOwner = $currentUserId == $operationOrder->created_by;
                  $shouldDisable = !$isOwner && $isSupervisor;
                  @endphp

                  <div class="row">
                    {{-- المستخدمين --}}
                    <div class="form-group col-md-4">
                      <label>المستخدمين*</label>
                      <select class="form-control select2-js" multiple name="user_id[]" required>
                        @foreach ($users as $user)
                          <option value="{{ $user->id }}" {{ in_array($user->id, explode(',', $operationOrder->user_id)) ? 'selected' : '' }}>
                            {{ $user->name }}
                          </option>
                        @endforeach
                      </select>
                    </div>

                    {{-- مشرف العملية --}}
                    <div class="form-group col-md-4">
                      <label>@lang('site.supervisor_process')</label>
                      <select class="form-control select2-js" multiple name="supervisor_process[]" required>
                        @foreach ($admins as $admin)
                          <option value="{{ $admin->id }}" {{ in_array($admin->id, explode(',', $operationOrder->supervisor_process)) ? 'selected' : '' }}>
                            {{ $admin->name }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="row">
                    <div class="form-group col-md-4">
                      <label>إسم العميل*</label>
                      <select name="client_name" class="form-control select2-js">
                        <option value="" disabled>العميل</option>
                        @foreach ($clients as $client)
                        <option value="{{ $client->name }}" {{ $operationOrder->client_name == $client->name ? 'selected' : '' }}> {{ $client->name }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="form-group col-md-4">
                      <label>وحدة الخامة المستخدمة*</label>
                      <input type="text" id="old_item_unit" name="old_item_unit" class="form-control" value="{{ $operationOrder->old_item_unit }}">
                    </div>
                    <div class="form-group col-md-4">
                      <label>وحدة الخامة الناتجة*</label>
                      <input type="text" id="out_item_unit" name="out_item_unit" class="form-control" value="{{ $operationOrder->out_item_unit }}">
                    </div>
                  </div>

                  {{-- start box --}}
                  <div class="form-group" style="border: 3px solid #33B35A;border-radius: 8px;padding:5px">
                      <div id="errors" hidden>
                        <div class="alert alert-danger">
                          من فضلك ادخل كل البيانات للصنف
                        </div>
                      </div>

                      <div id="quantity_error" hidden>
                        <div class="alert alert-danger">
                          كميه المستلزم غير صحيحه
                        </div>
                      </div>

                      <div class="row">
                        <div class="form-group col-md-4">
                          <label>@lang('site.operation_supplies')</label>
                          <select class="form-control" readonly>
                            {{-- <option value="">@lang('site.operation_supplies')</option> --}}
                            {{-- @foreach ($supplies as $supplie)
                            <option value="{{$supplie->id}}" {{old('operation_suplies_id')==$supplie->id ? 'selected' :
                              ''}}>{{$supplie->name}}</option>
                            @endforeach --}}
                          </select>
                        </div>

                        <div class="form-group  col-md-4">
                          <label>@lang('site.used_item')*</label>
                          <input type="text" id="item_name" class="form-control">
                        </div>

                        <div class="form-group col-md-4">
                          <label>@lang('site.old_item_quantity')*</label>
                          <input type="number" id="old_item_quantity" class="form-control">
                        </div>
                      </div>{{-- end row --}}

                      <div class="row">
                        <div class="form-group  col-md-4">
                          <label>@lang('site.out_item_name')*</label>
                          <input type="text" id="out_item_name" class="form-control">
                        </div>

                        <div class="form-group col-md-4">
                          <label>عدد ألواح او كويل*</label>
                          <input type="number" id="out_quantity" class="form-control">
                        </div>

                        <div class="form-group col-md-4">
                          <label>@lang('site.out_quantity')*</label>

                        </div>
                      </div>{{-- end row --}}

                      <div class="row">
                        <div class="form-group col-md-4">
                          <label>طول ألواح او كويل*</label>
                          <input type="number" step="any" min="0" id="length" class="form-control">
                        </div>

                        <div class="form-group col-md-4">
                          <label>@lang('site.width')*</label>
                          <input type="number" step="any" min="0" id="width" class="form-control">
                        </div>
                      </div>

                      <div class="form-group">
                        <button type="button" class="btn btn-block btn-sm btn-info" id="add">
                          <i class="fa  fa-chevron-down"></i>
                        </button>
                      </div>
                    </div>
                    {{-- end box --}}

                  <div class="box">
                    <div class="box-body">
                      <table class="table text-center" id="table_items">
                        <thead>
                          <tr>
                            <th>@lang('site.operation_supplies')</th>
                            <th>@lang('site.used_item')</th>
                            <th>@lang('site.old_item_quantity')</th>
                            <th>@lang('site.out_item_name')</th>
                            <th>كميه المستلزم</th>
                            <th>@lang('site.out_quantity')</th>
                            <th>@lang('site.length')</th>
                            <th>@lang('site.width')</th>
                            <th>حذف</th>
                          </tr>
                        </thead>
                        <tbody class="to-append">
                          @foreach($operationOrderDetails as $index => $detail)
                          <tr>
                            <td>
                              <select name="operation_suplies_id[{{$index}}][]" class="form-control select2-js" multiple>
                                @foreach(App\MachineSupplie::where('machine_id', $operationOrder->machine_id)->get() as $supply)
                                <option value="{{$supply->id}}" {{ in_array($supply->id, explode(',', $detail->operation_suplies_id)) ? 'selected' : '' }}>{{$supply->Supplie->name}} : المتبقي {{number_format($supply->used, 2, '.', '')}}</option>
                                @endforeach
                              </select>
                              <input type="hidden" name="operation_order_detail_id[]" value="{{$detail->id}}">
                            </td>
                            <td><input type="text" name="item_name[]" class="form-control" value="{{$detail->item_name}}"></td>
                            <td><input type="number" name="old_item_quantity[]" class="form-control" value="{{$detail->old_item_quantity}}" ></td>
                            <td><input type="text" name="out_item_name[]" class="form-control" value="{{$detail->out_item_name}}" ></td>
                            <td><input type="number" name="out_quantity[]" class="form-control" value="{{$detail->out_quantity}}" ></td>
                            <td><input type="number" name="quantity[]" class="form-control" value="{{$detail->quantity}}" ></td>
                            <td><input type="number" name="length[]" class="form-control" value="{{$detail->length}}" ></td>
                            <td><input type="number" name="width[]" class="form-control" value="{{$detail->width}}" ></td>
                            <td><input type="checkbox" name="selected_items[]" value="{{$detail->id}}"> حذف</td>
                          </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <div class="row">
                    <div class="form-group col-md-6">
                      <label>عاملين الاستلام*</label>
                      <select name="store_employees[]" class="form-control select2-js" multiple required>
                        <option disabled>-- اختر من القائمة --</option>
                        @foreach(App\Employee::where('job_id', 2)->where('branch_id', auth()->user()->branch_id)->get() as $employee)
                        <option value="{{$employee->id}}" {{ in_array($employee->id, explode(',', $operationOrder->store_employees ?? '')) ? 'selected' : '' }}>{{ $employee->name }}</option>
                        @endforeach
                      </select>
                    </div>

                    <div class="form-group col-md-6" style="position: relative">
                      <label>@lang('site.date')*</label>
                      <input type="date" name="date" class="form-control datePicker" value="{{ $operationOrder->date ?? date('Y-m-d') }}">
                    </div>
                  </div>

                  <div class="row">
                    <div class="form-group col-md-12">
                      <label>ملاحظات</label>
                      <textarea name="notes2" class="form-control" rows="3">{{ $operationOrder->notes2 ?? '' }}</textarea>
                    </div>
                  </div>

                  <div class="form-group">
                      <button type="submit" class="btn btn-success editForm" name=" , العمليه رقم {{ $operationOrder->id }}" value="{{ $operationOrder->id }}">
                          <i class="fa fa-check"></i> تعديل الان
                        </button>
                  </div>
                @endif
              </form>
            </div>
          </div>
        </section>
      </section>
    </div>
@endsection
@section('scripts')
    <script>
        $("#machineType").change(function() {
            $.ajax({
                url: "{{ route('dashboard.machines.get_machines_by_type') }}?type=" + $(this).val(),
                method: 'GET',
                success: function(data) {
                    $('#machines').html(data.html);
                }
            });

        });
        $("#machines").change(function() {
            // $.ajax({
            //     url: "{{ route('dashboard.operation_orders.get_items_by_machine') }}?machine_id=" + $(this).val(),
            //     method: 'GET',
            //     success: function(data) {
            //         $('#items').html(data.html);
            //     }
            // });
            $.ajax({
                url: "{{ route('dashboard.operation_orders.get_inGroups_by_machine') }}?machine_id=" + $(
                    this).val(),
                method: 'GET',
                success: function(data) {
                    $('#inGroups').html(data.html);
                }
            });
            $.ajax({
                url: "{{ route('dashboard.operation_orders.get_outGroups_by_machine') }}?machine_id=" + $(
                    this).val(),
                method: 'GET',
                success: function(data) {
                    $('#outGroupId').html(data.html);
                }
            });
            $.ajax({
                url: "{{ route('dashboard.operation_orders.get_supplies_by_machine') }}?machine_id=" + $(
                    this).val(),
                method: 'GET',
                success: function(data) {
                    $('#supplies').html(data.html);
                }
            });
        });
        $("#outGroupId").change(function() {
            $.ajax({
                url: "{{ route('dashboard.operation_orders.get_items_by_group') }}?out_group_id=" + $(this)
                    .val(),
                method: 'GET',
                success: function(data) {
                    $('#outItemId').html(data.html);
                }
            });
        });
        $("#inGroups").change(function() {
            $.ajax({
                url: "{{ route('dashboard.operation_orders.get_items_by_group') }}?out_group_id=" + $(this)
                    .val(),
                method: 'GET',
                success: function(data) {
                    $('#items').html(data.html);
                }
            });
        });

        $('#outGroupId').on('change', function() {
            $('#name').val($('#outGroupId option:selected').text());
        });

        $("#items").change(function() {
            $.ajax({
                url: "{{ route('dashboard.operation_orders.get_item_quantity') }}?item_id=" + $(this)
                    .val() + '&&machine_id=' + $('#machines').val(),
                method: 'GET',
                success: function(data) {
                    $('#oldItemQnt').attr('placeholder', '  الكمية المتاحه للآلة = ' + data.quantity);
                    $('#oldItemQnt').attr('max', data.quantity);
                }
            });
        });


        $('#add').on('click', function() {

            var supplies_id = $('#supplies').val(),
                quantity_used = document.querySelectorAll('#supplies option:checked'),
                supplies_name = $('#supplies option:selected').text(),
                item_name = $('#item_name').val(),
                old_item_quantity = $('#old_item_quantity').val(),
                old_item_supp_quantity = $('#oldItemSuppQnt').val(),
                out_item_name = $('#out_item_name').val(),
                out_quantity = $('#out_quantity').val(),
                length = $('#length').val(),
                width = $('#width').val();

            console.log(out_quantity);
            var quantityToDecrement = length * out_quantity;
            //2500
            quantity_used.forEach((e) => {
                if (quantityToDecrement > e.getAttribute('data-used')) {
                    $('#quantity_error').show();
                    $('#quantity_error').delay(3500).fadeOut(350);
                    exit();
                }
            })
            // if (supplies_id == 0 || supplies_id == null) {
            //     $('#errors').show();
            //     $('#errors').delay(3500).fadeOut(350);
            //     return false;
            // }
            if (old_item_quantity <= 0 || old_item_quantity == null) {
                $('#errors').show();
                $('#errors').delay(3500).fadeOut(350);
                return false;
            }
            if (item_name == '' || item_name == null) {
                $('#errors').show();
                $('#errors').delay(3500).fadeOut(350);
                return false;
            }
            if (out_quantity <= 0 || out_quantity == null) {
                $('#errors').show();
                $('#errors').delay(3500).fadeOut(350);
                return false;
            }
            if (old_item_supp_quantity <= 0 || old_item_supp_quantity == null) {
                $('#errors').show();
                $('#errors').delay(3500).fadeOut(350);
                return false;
            }
            if (out_item_name == '' || out_item_name == null) {
                $('#errors').show();
                $('#errors').delay(3500).fadeOut(350);
                return false;
            }
            if (length <= 0 || length == null) {
                $('#errors').show();
                $('#errors').delay(3500).fadeOut(350);
                return false;
            }
            if (width <= 0 || width == null) {
                $('#errors').show();
                $('#errors').delay(3500).fadeOut(350);
                return false;
            }

            var row = '<tr>' +
                '<td><select name="operation_suplies_id[]" class="form-control select2-js" multiple>' +
                '@foreach(App\MachineSupplie::where("machine_id", $operationOrder->machine_id)->get() as $supply)' +
                '<option value="{{$supply->id}}">{{$supply->Supplie->name}} : المتبقي {{number_format($supply->used, 2, ".", "")}}</option>' +
                '@endforeach' +
                '</select></td>' +
                '<td><input type="text" name="item_name[]" class="form-control" value="' + item_name + '" readonly></td>' +
                '<td><input type="number" name="old_item_quantity[]" class="form-control" value="' + old_item_quantity + '" readonly></td>' +
                '<td><input type="text" name="out_item_name[]" class="form-control" value="' + out_item_name + '" readonly></td>' +
                '<td><input type="number" name="out_quantity[]" class="form-control" value="' + out_quantity + '" readonly></td>' +
                '<td><input type="number" name="quantity[]" class="form-control" value="' + quantityToDecrement + '" readonly></td>' +
                '<td><input type="number" name="length[]" class="form-control" value="' + length + '" readonly></td>' +
                '<td><input type="number" name="width[]" class="form-control" value="' + width + '" readonly></td>' +
                '<td><input type="checkbox" name="selected_items[]" value="new"> حذف</td>' +
                '</tr>';

            $('.to-append').append(row);

            // Clear form fields
            $('#item_name').val('');
            $('#old_item_quantity').val('');
            $('#out_item_name').val('');
            $('#out_quantity').val('');
            $('#length').val('');
            $('#width').val('');

            // Reinitialize select2 for new row
            $('.to-append tr:last-child select').select2();
        });
    </script>
@endsection
