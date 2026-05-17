<?php

namespace App\DataTables;

use App\Models\Order;
use Yajra\DataTables\Services\DataTable;

class ReturnOrdersOutDataTable extends DataTable
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
            ->editColumn('date',function(Order $order){
                return optional($order->date)->toDateString();
            })
            ->addColumn('action', 'return-orders-out.actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\client $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Order $model)
    {
        $query =  $model
        ->select('orders.id','orders.created_at'
        ,'date','final_total','suppliers.name',
        'users.name as user',
        'branches.name as branch',
        'cost','rest')
        ->join('suppliers', function ($join) {
            $join->on('suppliers.id', '=', 'orders.ownerable_id')
            ->where('orders.ownerable_type','App\Models\Supplier');
        })
        ->where('type','in')
        ->where('is_return',true)
        ->leftJoin('branches','branches.id','=','orders.branch_id')
        ->leftJoin('users','users.id','=','orders.user_id')
        ->latest();


        if(auth()->user()->isOfType('branch_manager')){
            $query = $query->where('branch_id',auth()->user()->branch_id); 
        } else if(auth()->user()->isOfType('employee')) {
            $query = $query->where('user_id',auth()->user()->id); 
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
                'title'=>'رقم الفاتورة',                
            ],
            [
                'name'=>'date',
                'data'=>'date',
                'title'=>'التاريخ',                
            ],
            [
                'name'=>'suppliers.name',
                'data'=>'name',
                'title'=>'العميل',                
            ],
            [
                'name'=>'users.name',
                'data'=>'user',
                'title'=>'المستخدم',                
            ],
            [
                'name'=>'branches.name',
                'data'=>'branch',
                'title'=>'الفرع',                
            ],
            [
                'name'=>'final_total',
                'data'=>'final_total',
                'title'=>'الاجمالي',                
            ],   
            [
                'name'=>'cost',
                'data'=>'cost',
                'title'=>'المدفوع من العميل',                
            ], 
            [
                'name'=>'rest',
                'data'=>'rest',
                'title'=>'الباقي',                
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
        return 'ReturnOrdersOut_' . date('YmdHis');
    }
}
