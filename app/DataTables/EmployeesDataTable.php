<?php

namespace App\DataTables;

use App\Models\Employee;
use Yajra\DataTables\Services\DataTable;

class EmployeesDataTable extends DataTable
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
        ->editColumn('date_of_appointment',function(Employee $loan){
            return optional($loan->date_of_appointment)->toDateString();
             })
            ->addColumn('action', 'employees.actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\client $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Employee $model)
    {
        $query =  $model
        ->select(
                'employees.name',
                'branches.name as branch',
                'employees.date_of_appointment',
                'employees.id','employees.phone_1',
                'jobs.name as job','employees.created_at'
        )
        ->leftJoin('branches','branches.id','=','employees.branch_id')
        ->leftJoin('jobs','jobs.id','=','employees.job_id')
        ->latest();

        if (auth()->user()->id != 1){
            if(!auth()->user()->isOfType('manager')){
                $query = $query->where('branch_id',auth()->user()->branch_id);
            }
        }

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
                    ->minifiedAjax()
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
                'name'=>'id',
                'data'=>'id',
                'title'=>'الكود',                
            ],
            [
                'name'=>'name',
                'data'=>'name',
                'title'=>'الاسم',                
            ],
            [
                'name'=>'branches.name',
                'data'=>'branch',
                'title'=>'الفرع',                
            ],
            
             [
                'name'=>'phone_1',
                'data'=>'phone_1',
                'title'=>'تليفون 1',                
            ], 
            
            [
                'name'=>'date_of_appointment',
                'data'=>'date_of_appointment',
                'title'=>'تاريخ التعيين',                
            ], 
            [
                'name'=>'jobs.name',
                'data'=>'job',
                'title'=>'الوظيفة',                
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
            // 'responsive' => true,
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
        return 'Employees_' . date('YmdHis');
    }
}
