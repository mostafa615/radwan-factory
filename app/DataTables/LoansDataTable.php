<?php

namespace App\DataTables;

use App\Models\Loan;
use Yajra\DataTables\Services\DataTable;

class LoansDataTable extends DataTable
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
            ->editColumn('date',function(Loan $loan){
                return optional($loan->date)->toDateString();
            })
            ->addColumn('action', 'loans.actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\client $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Loan $model)
    {
        return $model
        ->select(
        'loans.created_at',
        'loans.id',
        'loans.cost',
        'loans.notes','reposites.name as reposite',
        'employees.name  as employee'
        ,'date')
        ->leftJoin('reposites','reposites.id','=','loans.reposite_id')
        ->leftJoin('employees','employees.id','=','loans.employee_id')
        ->latest();
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
                'name'=>'date',
                'data'=>'date',
                'title'=>'التاريخ',                
            ], 
            [
                'name'=>'employees.name',
                'data'=>'employee',
                'title'=>'الموظف',                
            ],
            [
                'name'=>'reposites.name',
                'data'=>'reposite',
                'title'=>'الخزنة',                
            ],
            [
                'name'=>'cost',
                'data'=>'cost',
                'title'=>'القيمة',                
            ],
            [
                'name'=>'loans.notes',
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
        return 'Loans_' . date('YmdHis');
    }
}
