<?php

namespace App\DataTables;

use App\Models\Transport;
use Yajra\DataTables\Services\DataTable;

class TransportsDataTable extends DataTable
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
            ->editColumn('date',function(Transport $transport){
                return optional($transport->date)->toDateString();
            })
            ->addColumn('action', 'transports.actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\client $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Transport $model)
    {
        $query =  $model->select('employees.name as employee','transports.id','transports.created_at','cost','transports.notes','transports.date')
        ->leftJoin('employees','employees.id','=','transports.employee_id')
        ->latest();


        
        if(auth()->user()->isOfType('branch_manager')){
            $query = $query->where('transports.branch_id',auth()->user()->branch_id); 
        } else if(auth()->user()->isOfType('employee')) {
            $query = $query->where('transports.user_id',auth()->user()->id); 
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
                'name'=>'employees.name',
                'data'=>'employee',
                'title'=>'الموظف',                
            ],
            [
                'name'=>'date',
                'data'=>'date',
                'title'=>'التاريخ',                
            ],
            [
                'name'=>'cost',
                'data'=>'cost',
                'title'=>'التكلفة',                
            ],
            [
                'name'=>'notes',
                'data'=>'notes',
                'title'=>'ملاحظات',                
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
            
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Transports_' . date('YmdHis');
    }
}
