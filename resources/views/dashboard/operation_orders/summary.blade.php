@extends('layouts.dashboard.app')
@section('content')
  <div class="content-wrapper summary-ui">
    <section class="content-header">
      <ol class="breadcrumb">
        <li>
          <a href="{{ route('dashboard.index') }}">
            <i class="fa fa-dashboard"></i>
            @lang('site.dashboard')
          </a>
        </li>
        <li class="active">
          حركات التشغيل الداخلية
        </li>
      </ol>
    </section>
    <section class="content">
      <div class="summary-wrapper">
        <div class="stats-bar">
          <div class="stat-card primary">
            <div class="stat-top">
              <div>
                <h3>{{ $stats['totalOrders'] }}</h3>
                <span>إجمالي الأوامر</span>
              </div>
              <div class="stat-icon">
                <i class="fa fa-align-justify"></i>
              </div>
            </div>
          </div>
          <div class="stat-card success">
            <div class="stat-top">
              <div>
                <h3>{{ $stats['completedOrders'] }}</h3>
                <span>مكتملة</span>
              </div>
              <div class="stat-icon">
                <i class="fa fa-check"></i>
              </div>
            </div>
          </div>
          <div class="stat-card warning">
            <div class="stat-top">
              <div>
                <h3>{{ $stats['pendingOrders'] }}</h3>
                <span>قيد المتابعة</span>
              </div>
              <div class="stat-icon">
                <i class="fa fa-clock-o"></i>
              </div>
            </div>
          </div>
          <div class="stat-card danger">
            <div class="stat-top">
              <div>
                <h3>{{ $stats['rejectedOrders'] }}</h3>
                <span>مرفوضة</span>
              </div>
              <div class="stat-icon">
                <i class="fa fa-times"></i>
              </div>
            </div>
          </div>
        </div>
        @php
          $groupedOrders = $operationOrders->groupBy('machine_id');
          $stepLabels = [
            'warehouse_supervisor' => 'سماح الماكينة',
            'machine_manager' => 'مشرف الماكينة',
            'production_manager' => 'مشرف الإنتاج',
            'store_manager' => 'مشرف المخزن',
          ];
        @endphp
        @foreach ($groupedOrders as $machineId => $machineOrders)
          @php
            $machine = optional($machineOrders->first())->machine;
          @endphp
          <div class="machine-section">
            <div class="machine-header">
              <div class="machine-title">
                <i class="fa fa-cogs"></i>
                {{ $machine ? $machine->name : '' }}
              </div>
            </div>
            <div class="modern-table-wrapper">
              <table class="modern-table">
                <thead>
                  <tr>
                    <td>#</td>
                    <td>رقم الأمر</td>
                    <td>التاريخ</td>
                    <td>اسم العميل</td>
                    <td>ملاحظات</td>
                    <td>موقع الأمر</td>
                    <td>الحالة</td>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($machineOrders as $order)
                    @php
                        $tracks = $order->tracks->sortBy('id');
                        $currentTrack = $tracks->firstWhere('status', 'rejected');
                        if (!$currentTrack) {
                          $currentTrack = $tracks->firstWhere('status', 'pending');
                        }
                        if (!$currentTrack) {
                          $currentTrack = $tracks->last();
                        }
                        $currentStep = isset($stepLabels[$currentTrack->step_name]) ? $stepLabels[$currentTrack->step_name] : '';
                        if ($tracks->where('status', 'rejected')->isNotEmpty()) {
                          $status = 'rejected';
                          $statusLabel = 'مرفوض';
                        } elseif ($tracks->where('status', 'pending')->isEmpty() && $tracks->where('status', 'approved')->isNotEmpty()) {
                          $status = 'approved';
                          $statusLabel = 'مكتمل';
                        } else {
                          $status = 'pending';
                          $statusLabel = 'في الانتظار';
                        }
                    @endphp
                    <tr>
                      <td>
                        <a href="{{ route('dashboard.operation_orders.show', $order->id) }}" target="_blank" class="order-number">
                          #{{ $order->id }}
                        </a>
                      </td>
                      <td>{{ $order->out_operation ? 'خارجي' : 'داخلي' }}</td>
                      <td>{{ $order->date }}</td>
                      <td>{{ $order->client_name }}</td>
                      <td>{{ $currentTrack->notes ?? '' }}</td>
                      <td>
                        <span class="step-badge">{{ $currentStep }}</span>
                      </td>
                      <td>
                        <span class="status-badge {{ $status }}">{{ $statusLabel }}</span>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        @endforeach
      </div>
    </section>
  </div>
@endsection
@section('css')
  <link rel="stylesheet" href="{{ asset('dashboard/css/custom.css') }}">
@endsection
