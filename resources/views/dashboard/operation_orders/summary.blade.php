@extends('layouts.dashboard.app')
@section('css')
  <link rel="stylesheet" href="{{ asset('dashboard/css/custom.css') }}?v={{ filemtime(public_path('dashboard/css/custom.css')) }}">
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
          <div class="stat-card grey">
            <div class="stat-top">
              <div>
                <h3 id="stat-awaiting">0</h3>
                <span>أوامر منتظرة السماح</span>
              </div>
              <div class="stat-icon">
                <i class="fa fa-lock"></i>
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
      const SAVE_URL = "{{ route('dashboard.operation_orders.saveScheduleOrder') }}";
      const TOGGLE_URL = "{{ route('dashboard.operation_orders.toggleMachineVisibility') }}";
      const CSRF = "{{ csrf_token() }}";
      // متطلب 1: التحكم/إعادة الترتيب متاح فقط لصاحب صلاحية "التحكم في جدول التصنيع"
      const CAN_CONTROL = {{ auth()->user()->can('control_manufacturing_schedule') ? 'true' : 'false' }};
      const REFRESH_INTERVAL_MS = 30000; // AJAX poll interval — raise this (e.g. 3000-5000) if the table gets large
      const CLIENT_WORD_LIMIT = 2; // اسم العميل: كلمتان كحد أقصى (الباقي في Tooltip)
      const NOTES_WORD_LIMIT = 3;  // الملاحظات: ثلاث كلمات كحد أقصى (الباقي في Tooltip)

      let lastSnapshot = {};
      let sortableInstances = {};

      // متطلب 4: هروب آمن للنص لعرضه داخل الخلية أو سمة title
      function esc(s) {
        return String(s ?? '')
          .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
      }

      // عرض أول (limit) كلمة فقط، والنص الكامل يظهر عند الوقوف بالماوس (Tooltip)
      function clampCell(text, limit) {
        const full = String(text ?? '').trim();
        if (!full) return '<td></td>';
        const words = full.split(/\s+/);
        if (words.length <= limit) {
          return `<td>${esc(full)}</td>`;
        }
        const short = words.slice(0, limit).join(' ') + '…';
        return `<td title="${esc(full)}" style="cursor:help;">${esc(short)}</td>`;
      }

      // متطلب 5: حفظ الترتيب في قاعدة البيانات بدل localStorage
      function persistOrder(tbody) {
        const ids = Array.from(tbody.querySelectorAll('tr')).map(tr => tr.dataset.id);
        $.ajax({
          url: SAVE_URL,
          method: 'POST',
          data: { _token: CSRF, ids: ids },
          error: function (xhr) { console.error('saveScheduleOrder failed', xhr); },
        });
      }

      function rowHtml(row) {
        const handle = CAN_CONTROL ? '<span class="drag-handle"><i class="fa fa-bars"></i></span> ' : '';
        // checkbox "عرض للماكينة" — يظهر لصاحب صلاحية التحكم فقط
        const checkCell = CAN_CONTROL
          ? `<td class="machine-check-cell"><input type="checkbox" class="machine-check" data-id="${row.id}" ${row.show_to_machine ? 'checked' : ''}></td>`
          : '';
        return `
          <tr data-id="${row.id}" data-stage-start="${row.stage_start || ''}">
            ${checkCell}
            <td>
              ${handle}<a href="${row.order_url}" target="_blank" class="order-number">#${row.order_id}</a>
            </td>
            <td>${esc(row.type)}</td>
            <td>${esc(row.date)}</td>
            ${clampCell(row.client_name, CLIENT_WORD_LIMIT)}
            <td class="material-cell">${esc(row.item_name)}</td>
            <td class="material-cell">${esc(row.output_item)}</td>
            <td>${esc(row.quantity)}</td>
            ${clampCell(row.notes, NOTES_WORD_LIMIT)}
            <td><span class="stage-timer">--:--:--</span></td>
            <td><span class="step-badge ${row.status}">${esc(row.current_step)}</span></td>
            <td><span class="status-badge ${row.status}">${esc(row.status_label)}</span></td>
          </tr>`;
      }

      function machineHtml(machine) {
        // متطلب 5: الصفوف تأتي مرتّبة من السيرفر (قاعدة البيانات) وموحّدة لكل المستخدمين
        const rows = machine.rows.map(rowHtml).join('');

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
                    ${CAN_CONTROL ? '<td class="machine-check-cell">للماكينة</td>' : ''}
                    <td>رقم الأمر</td>
                    <td>التشغيل</td>
                    <td>التاريخ</td>
                    <td>اسم العميل</td>
                    <td class="material-cell">الخامة المستخدمة</td>
                    <td class="material-cell">الخامة الناتجة</td>
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
        if (!CAN_CONTROL) return; // متطلب 1: إعادة الترتيب متاحة لصاحب صلاحية التحكم فقط

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
              persistOrder(tbody); // متطلب 5: حفظ الترتيب في قاعدة البيانات
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
        $('#stat-awaiting').text(data.stats.awaitingOrders);
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

      // حفظ حالة "عرض للماكينة" عند تعليم/إلغاء الـ checkbox (delegated لأن الصفوف تُعاد كل 30 ث)
      $(document).on('change', '.machine-check', function () {
        const id = $(this).data('id');
        const checked = this.checked;
        $.ajax({
          url: TOGGLE_URL,
          method: 'POST',
          data: { _token: CSRF, id: id, checked: checked ? 1 : 0 },
          error: function (xhr) { console.error('toggleMachineVisibility failed', xhr); },
        });
      });

      $(document).ready(function () {
        loadData();
        setInterval(loadData, REFRESH_INTERVAL_MS);
        setInterval(updateTimers, 1000);
      });
    })();
  </script>
@endsection