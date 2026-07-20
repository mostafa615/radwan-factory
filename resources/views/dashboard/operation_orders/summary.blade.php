@extends('layouts.dashboard.app')
@section('css')
  <link rel="stylesheet" href="{{ asset('dashboard/css/custom.css') }}">
@endsection
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
                <h3 id="stat-total">0</h3>
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
                <h3 id="stat-completed">0</h3>
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
                <h3 id="stat-pending">0</h3>
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
                <h3 id="stat-rejected">0</h3>
                <span>مرفوضة</span>
              </div>
              <div class="stat-icon">
                <i class="fa fa-times"></i>
              </div>
            </div>
          </div>
        </div>
        <div id="machines-container">
          <div class="text-center" style="padding:40px;">
            <i class="fa fa-spinner fa-spin"></i> جاري تحميل البيانات...
          </div>
        </div>
      </div>
    </section>
  </div>
@endsection
@section('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
  <script>
    (function () {
      const DATA_URL = "{{ route('dashboard.operation_orders.summaryData') }}";
      const REFRESH_INTERVAL_MS = 30000; // AJAX poll interval — raise this (e.g. 3000-5000) if the table gets large
      const ORDER_KEY_PREFIX = 'opSummaryOrder_machine_';

      let lastSnapshot = {};
      let sortableInstances = {};

      function getSavedOrder(machineId) {
        try {
          return JSON.parse(localStorage.getItem(ORDER_KEY_PREFIX + machineId)) || [];
        } catch (e) {
          return [];
        }
      }

      function saveOrder(machineId, ids) {
        localStorage.setItem(ORDER_KEY_PREFIX + machineId, JSON.stringify(ids));
      }

      function applySavedOrder(machineId, rows) {
        const saved = getSavedOrder(machineId);
        if (!saved.length) return rows;

        const byId = {};
        rows.forEach(r => { byId[r.id] = r; });

        const ordered = [];
        saved.forEach(id => {
          if (byId[id]) {
            ordered.push(byId[id]);
            delete byId[id];
          }
        });
        // new rows not part of the saved order yet go at the end
        Object.values(byId).forEach(r => ordered.push(r));

        return ordered;
      }

      function rowHtml(row) {
        return `
          <tr data-id="${row.id}" data-stage-start="${row.stage_start || ''}">
            <td>
              <span class="drag-handle"><i class="fa fa-bars"></i></span>
              <a href="${row.order_url}" target="_blank" class="order-number">#${row.order_id}</a>
            </td>
            <td>${row.type}</td>
            <td>${row.date ?? ''}</td>
            <td>${row.client_name ?? ''}</td>
            <td>${row.item_name ?? ''}</td>
            <td>${row.quantity ?? ''}</td>
            <td>${row.notes ?? ''}</td>
            <td><span class="stage-timer">--:--:--</span></td>
            <td><span class="step-badge">${row.current_step ?? ''}</span></td>
            <td><span class="status-badge ${row.status}">${row.status_label}</span></td>
          </tr>`;
      }

      function machineHtml(machine) {
        const rows = applySavedOrder(machine.machine_id, machine.rows).map(rowHtml).join('');

        return `
          <div class="machine-section" data-machine-id="${machine.machine_id}">
            <div class="machine-header">
              <div class="machine-title">
                <i class="fa fa-cogs"></i>
                ${machine.machine_name}
              </div>
            </div>
            <div class="modern-table-wrapper">
              <table class="modern-table">
                <thead>
                  <tr>
                    <td>رقم الأمر</td>
                    <td>التشغيل</td>
                    <td>التاريخ</td>
                    <td>اسم العميل</td>
                    <td>الخامة المستخدمة</td>
                    <td>عدد</td>
                    <td>ملاحظات</td>
                    <td>المدة</td>
                    <td>موقع الأمر</td>
                    <td>الحالة</td>
                  </tr>
                </thead>
                <tbody>${rows}</tbody>
              </table>
            </div>
          </div>`;
      }

      function initDragAndDrop() {
        document.querySelectorAll('.machine-section').forEach(section => {
          const machineId = section.dataset.machineId;
          const tbody = section.querySelector('tbody');

          if (sortableInstances[machineId]) {
            sortableInstances[machineId].destroy();
          }

          sortableInstances[machineId] = Sortable.create(tbody, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            dragClass: 'sortable-drag',
            onEnd: function () {
              const ids = Array.from(tbody.querySelectorAll('tr')).map(tr => tr.dataset.id);
              saveOrder(machineId, ids);
            },
          });
        });
      }

      function updateTimers() {
        document.querySelectorAll('tr[data-stage-start]').forEach(tr => {
          const startIso = tr.dataset.stageStart;
          const el = tr.querySelector('.stage-timer');
          if (!el) return;

          if (!startIso) {
            el.textContent = '--:--:--';
            return;
          }

          const start = new Date(startIso).getTime();
          const diff = Math.max(0, Math.floor((Date.now() - start) / 1000));

          const h = String(Math.floor(diff / 3600)).padStart(2, '0');
          const m = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
          const s = String(diff % 60).padStart(2, '0');
          el.textContent = `${h}:${m}:${s}`;
        });
      }

      function render(data) {
        $('#stat-total').text(data.stats.totalOrders);
        $('#stat-completed').text(data.stats.completedOrders);
        $('#stat-pending').text(data.stats.pendingOrders);
        $('#stat-rejected').text(data.stats.rejectedOrders);

        const container = document.getElementById('machines-container');

        if (!data.machines.length) {
          container.innerHTML = '<div class="text-center" style="padding:40px;">لا توجد أوامر لعرضها</div>';
          lastSnapshot = {};
          return;
        }

        container.innerHTML = data.machines.map(machineHtml).join('');

        // flash any row whose status/step changed since the last poll
        const newSnapshot = {};
        data.machines.forEach(m => m.rows.forEach(r => {
          newSnapshot[r.id] = r.status + '|' + r.current_step;
          if (lastSnapshot[r.id] && lastSnapshot[r.id] !== newSnapshot[r.id]) {
            const el = container.querySelector(`tr[data-id="${r.id}"]`);
            if (el) el.classList.add('row-flash');
          }
        }));
        lastSnapshot = newSnapshot;

        initDragAndDrop();
        updateTimers();
      }

      function loadData() {
        $.ajax({
          url: DATA_URL,
          method: 'GET',
          dataType: 'json',
          success: render,
          error: function (xhr) {
            console.error('summaryData request failed', xhr);
          },
        });
      }

      $(document).ready(function () {
        loadData();
        setInterval(loadData, REFRESH_INTERVAL_MS);
        setInterval(updateTimers, 1000);
      });
    })();
  </script>
@endsection