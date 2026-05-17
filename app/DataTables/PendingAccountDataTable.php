<?php

namespace App\DataTables;

use App\Models\Account;

use Yajra\DataTables\Services\DataTable;

class PendingAccountDataTable extends DataTable
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
            ->editColumn('date',function(Account $account){
                return optional($account->date)->toDateString();
            })
            ->addColumn('owner',function(Account $account){
                return optional($account->ownerable)->name;
            })
            ->addColumn('action','pending-account.action');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\client $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Account $model)
    {
        $query =  $model
        ->select('accounts.id','accounts.cost','accounts.type','accounts.created_at','accounts.date','reposites.name','accounts.order_id')
        ->leftJoin('reposites','reposites.id','=','accounts.reposite_id')
        ->where('pending',true)
        ->latest();
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
                'name'=>'accounts.id',
                'data'=>'id',
                'title'=>'رقم السند',                
            ],
            
            [
                'name'=>'date',
                'data'=>'date',
                'title'=>'التاريخ',                
            ],
            [
                'name'=>'owner',
                'data'=>'owner',
                'title'=>'المسؤول',                
            ],
            [
                'name'=>'reposites.name',
                'data'=>'name',
                'title'=>'الخزنة',                
            ],
             [
                'name'=>'accounts.cost',
                'data'=>'cost',
                'title'=>'المدفوع',                
            ], 
            [
                'name'=>'order_id',
                'data'=>'order_id',
                'title'=>'رقم الفاتورة',                
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
        return 'Accounts_' . date('YmdHis');
    }

}
