<?php

namespace App\DataTables;

use App\Models\OrderDetail;
use Yajra\DataTables\Services\DataTable;

class OrderDetailsDataTable extends DataTable
{
     /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables($query);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\client $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(OrderDetail $model)
    {

        return $model
        ->select('order_details.id','items.name','stores.name as store','unite_price','quantity','discount')
        ->leftJoin('stores','stores.id','order_details.store_id')
        ->where('order_id',optional($this->request()->Order)->id)
        ->leftJoin('items','items.id','=','order_details.item_id');
        
       
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
                'name'=>'items.name',
                'data'=>'name',
                'title'=>'اسم الصنف',                
            ],
            [
                'name'=>'stores.name',
                'data'=>'store',
                'title'=>'المخزن',                
            ],
            [
                'name'=>'unite_price',
                'data'=>'unite_price',
                'title'=>'سعر الوحدة',                
            ],
             [
                'name'=>'quantity',
                'data'=>'quantity',
                'title'=>'الكمية',                
            ], 
            [
                'name'=>'discount',
                'data'=>'discount',
                'title'=>'الخصم',                
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
        return 'OrderDetailDetails_' . date('YmdHis');
    }
}
