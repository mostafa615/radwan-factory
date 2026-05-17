<?php

namespace App\DataTables;

use App\Models\Employee;
use Carbon\Carbon;
use Yajra\DataTables\Services\DataTable;

class AttendanceDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query)
        ->addColumn('attendance',function(Employee $employee){
            $attendance_object=$employee->attendances->first();
            $attendance = optional($attendance_object)->attendance_time;
            $id = $employee->id;
            $disable='';
            if ($attendance_object && Auth()->user()->id != 1){
                $disable='disabled';
            }
            return view('attendance.datatbles.attendance',compact('id','attendance','disable'))->render();
        })
        ->addColumn('abandonment',function(Employee $employee){
            //$time =Carbon::parse('17:00')->format('H:i');
            $abandonment =Carbon::parse('05:00 pm')->format('H:i');
            $abandonment_object=$employee->attendances->last();
            if ($abandonment_object){
                $times = explode(":", $abandonment_object->abandonment_time);
                
                if (sizeOf($times) >= 2) {
                $t1 = isset($times[1])? $times[1] : '';
                $abandonment= $times[0] . ":" . $t1; //Carbon::parse($abandonment_object->abandonment_time)->format('H:i');
                } else {
                    $abandonment = '';
                }
            }

            $id = $employee->id;
            $disable='';
            if (!empty($abandonment)  && Auth()->user()->id != 1){
                $disable='disabled';
            }
            return view('attendance.datatbles.abandonment',compact('time','id','disable','abandonment'))->render();
        })
        ->addColumn('absence',function(Employee $employee){
            $absence_object=$employee->attendances->first();
            $absence = optional($absence_object)->absence;
            $id = $employee->id;
            $disable='';
            if (!empty($absence_object)  && Auth()->user()->id != 1){
                $disable='disabled';
            }
            return view('attendance.datatbles.absence',compact('id','disable','absence'))->render();
        })
        ->addColumn('absence_with_permission',function(Employee $employee){
            $absenceWithPermission_object=$employee->attendances->first();
            $absenceWithPermission = optional($absenceWithPermission_object)->absence_with_permission;
            $id = $employee->id;

            $disable='';
            if (!empty($absenceWithPermission_object)  && Auth()->user()->id != 1){
                $disable='disabled';
            }
            return view('attendance.datatbles.absence-with-permission',compact('id','disable','absenceWithPermission'))->render();
        })
        ->addColumn('leave_with_permission',function(Employee $employee){
            $leave_with_permission_object=$employee->attendances->first();
            $leave_with_permission = optional($leave_with_permission_object)->leave_with_permission;
            $id = $employee->id;
            $disable='';
            if (!empty($leave_with_permission_object  && Auth()->user()->id != 1)){
                $disable='disabled';
            }
            return view('attendance.datatbles.leave_with_permission',compact('id','leave_with_permission','disable'))->render();
        })
        ->addColumn('late',function(Employee $employee){
            $late_object=$employee->attendances->first();
            $late = optional($late_object)->late;
            $id = $employee->id;
            $disable='';
            if (!empty($late_object  && Auth()->user()->id != 1)){
                $disable='disabled';
            }
            return view('attendance.datatbles.late',compact('id','late','disable'))->render();
        })
        ->addColumn('late_with_permission',function(Employee $employee){
            $lateWithPermission_object=$employee->attendances->first();
            $lateWithPermission = optional($lateWithPermission_object)->late_with_permission;
            $id = $employee->id;
            $disable='';
            if (!empty($lateWithPermission_object)  && Auth()->user()->id != 1){
                $disable='disabled';
            }

            return view('attendance.datatbles.late-with-permission',compact('id','disable','lateWithPermission'))->render();
        })
        ->addColumn('absence_with_holiday',function(Employee $employee){
            $absenceWithHoliday_object=$employee->attendances->first();
            $absenceWithHoliday = optional($absenceWithHoliday_object)->absence_with_holiday;
            $id = $employee->id;

            $disable='';
            if (!empty($absenceWithHoliday_object)  && Auth()->user()->id != 1 ){
                $disable='disabled';
            }
            return view('attendance.datatbles.absence-with-holiday',compact('absenceWithHoliday','disable','id'))->render();
            
        })
        ->addColumn('action',function(Employee $employee){
            $attendance_object=$employee->attendances->first();
            $attendance = optional($attendance_object)->id;
            $disable='';
            if (!empty($attendance_object)  && Auth()->user()->id != 1){
                $disable='disabled';
            }

            return view('attendance.datatbles.actions',compact('attendance','disable'))->render();
            
        })
        ->rawColumns(['attendance','abandonment','absence','absence_with_permission','late_with_permission','late','absence_with_holiday','action', 'leave_with_permission']);
    }


    public function query(Employee $model)
    {
        \Debugbar::info(request()->all());
        \Debugbar::info(request()->date);
        $query = $model
        ->with([
            'attendances'=>function($query){
                $query->whereDate('date',request()->date);
            }
        ])
        ->select(
        'employees.id',
        'employees.name as employee',
        'jobs.name as job',
        'employees.created_at'
        )
        ->leftJoin('jobs','employees.job_id','=','jobs.id')
        ->where('active','1')
        ->latest();


        if(auth()->user()->id != 1){
            $query = $query->where('branch_id',auth()->user()->branch_id); 
        } 

        if (count($query->get()) <= 0)
            return [];
        return $query;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->columns($this->getColumns())
                    ->ajax([
                        'data'=>'function(data){
                            data.date = $date.val();
                        }'
                    ])
                    ->parameters($this->getBuilderParameters());
    }

     
    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            [
                'name'=>'employees.name',
                'data'=>'employee',
                'title'=>'الموظف',                
            ],
            [
                'name'=>'jobs.name',
                'data'=>'job',
                'title'=>'الوظيفة',                
            ], 
            [
                'name'=>'attendance',
                'data'=>'attendance',
                'title'=>'الحضور',  
                'orderable'=>false,
                'searchable'=>false,              
            ], 
            [
                'name'=>'abandonment',
                'data'=>'abandonment',
                'title'=>'الانصراف', 
                'orderable'=>false,
                'searchable'=>false,               
            ], 
            [
                'name'=>'leave_with_permission',
                'data'=>'leave_with_permission',
                'title'=>'انصراف  باذن',                
                'orderable'=>false,
                'searchable'=>false,
            ], 
            [
                'name'=>'late_with_permission',
                'data'=>'late_with_permission',
                'title'=>'تاخير  باذن',                
                'orderable'=>false,
                'searchable'=>false,
            ], 
            [
                'name'=>'late',
                'data'=>'late',
                'title'=>'تاخير بدون إذن ',
                'orderable'=>false,
                'searchable'=>false,              
            ], 
            [
                'name'=>'absence_with_permission',
                'data'=>'absence_with_permission',
                'title'=>'غياب  باذن',                
                'orderable'=>false,
                'searchable'=>false,
            ], 
            [
                'name'=>'absence',
                'data'=>'absence',
                'title'=>'غياب',  
                'orderable'=>false,
                'searchable'=>false,              
            ],
            [
                'name'=>'absence_with_holiday',
                'data'=>'absence_with_holiday',
                'title'=>'غياب من رصيد الاجازات',  
                'orderable'=>false,
                'searchable'=>false,              
            ], 
            [
                'name'=>'action',
                'data'=>'action',
                'title'=>'عمليات',   
                'exportable' => false,
                'printable' => false,
                'searchable' => false,
                'orderable' => false,
            ], 
        ];
    }


    /**
    *Get the builder parameters
    *@return array
    */
    public function getBuilderParameters()
    {
        return [
            'dom' => 'Bfrtip',
            'buttons' => ['excel', 'print', 'reset', 'reload'],
            'language' => [
                      'url' => url('/vendor/datatables/arabic.json')
            ],
            // 'filter' => true,
            // 'order' => [ [0,'desc'] ],
            // 'lengthMenu' => [10,25,50]
            
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Attendance_' . date('YmdHis');
    }
}
