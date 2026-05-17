<?php

namespace App\DataTables;

use App\Models\Reposite;
use Yajra\DataTables\Services\DataTable;

class RepositesDataTable extends DataTable
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
            ->addColumn('action', 'reposites.actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\client $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Reposite $model)
    {
        $query =  $model->select('reposites.created_at','reposites.id','reposites.name','balance','users.name as user','branches.name as branch')
        ->leftJoin('branches','reposites.branch_id','=','branches.id')
        ->leftJoin('users','reposites.user_id','=','users.id')
        ->latest();

        if(auth()->user()->isOfType('branch_manager')){
            $query = $query->where('reposites.branch_id',auth()->user()->branch_id); 
        } else if(auth()->user()->isOfType('employee')) {
            $query = $query->where('reposites.user_id',auth()->user()->id); 
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
                'name'=>'reposites.name',
                'data'=>'name',
                'title'=>'الاسم',                
            ],
             [
                'name'=>'balance',
                'data'=>'balance',
                'title'=>'الرصيد',                
            ],
            [
                'name'=>'branches.name',
                'data'=>'branch',
                'title'=>'الفرع',                
            ], 
            [
                'name'=>'users.name',
                'data'=>'user',
                'title'=>'الموظف',                
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
        return 'Repsites_' . date('YmdHis');
    }
}
