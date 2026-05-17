<?php

namespace App\DataTables;

use App\Models\Order;
use Yajra\DataTables\Services\DataTable;

class PendingPriceDataTable extends DataTable
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
            ->addColumn('buyer',function(Order $order){
                return optional($order->ownerable)->name;
            })
            ->addColumn('action', 'pending-price.action');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\client $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Order $model)
    {
        return $model
        ->select('orders.created_at','orders.id','users.name as user','branches.name as branch','orders.ownerable_id','orders.ownerable_type')
        ->leftJoin('branches','orders.branch_id','=','branches.id')
        ->leftJoin('users','orders.user_id','=','users.id')
        ->whereHas('orderDetails',function($query){
            $query->where('price_pending',true);
        })
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
                'name'=>'orders.id',
                'data'=>'id',
                'title'=>'رقم الفاتورة',                
            ],
            [
                'name'=>'branches.name',
                'data'=>'branch',
                'title'=>'الفرع',                
            ], 
            [
                'name'=>'users.name',
                'data'=>'user',
                'title'=>'المستخدم',                
            ], 

            [
                'name'=>'buyer',
                'data'=>'buyer',
                'title'=>'المشتري',                
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
        return 'PendingPrice_' . date('YmdHis');
    }
}
