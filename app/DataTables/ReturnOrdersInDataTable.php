<?php

namespace App\DataTables;

use App\Models\Order;
use Yajra\DataTables\Services\DataTable;

class ReturnOrdersInDataTable extends DataTable
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
            ->addColumn('buyer',function(Order $order){
                return optional($order->ownerable)->name;
            })
            ->addColumn('action', 'return-orders-in.actions');
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
        ->select('orders.id','orders.final_total'
        ,'orders.created_at',
        'users.name as user',
        'branches.name as branch',
        'ownerable_id','ownerable_type',
        'date'
        ,'cost','rest'
        )
        ->where('is_return',true)
        ->where('type','out')
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
                'searchable'=>false,
                'name'=>'buyer',
                'data'=>'buyer',
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
            ],            [
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
        return 'ReturnOrdersIn_' . date('YmdHis');
    }
}
