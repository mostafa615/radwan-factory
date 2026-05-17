<?php

namespace App\DataTables;

use App\Models\Quantity;
use Yajra\DataTables\Services\DataTable;

class QuantitiesInStoreDataTable extends DataTable
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
        ->editColumn('quantity',function(Quantity $item){
            $quantity = $item->quantity;
            $id = $item->id;
            return view('stores.datatables.show.quantity',compact('quantity','id'))->render();
        })
        ->addColumn('action',function(Quantity $item){
            $id = $item->id;
            return view('stores.datatables.show.action',compact('id'))->render();
        })
        ->rawColumns(['quantity','action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\client $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Quantity $model)
    {
        return $model
        ->select('quantities.id as id','items.name','quantities.quantity','quantities.init','quantities.created_at')
        ->leftJoin('items','items.id','=','quantities.item_id')
        ->join('stores',function($join){
            $join
            ->on('quantities.ownerable_id','=','stores.id')
            ->where('quantities.ownerable_type','App\Models\Store');
        })->where('quantities.ownerable_id',request()->store->id)
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
//            [
//                'name'=>'items.name',
//                'data'=>'name',
//                'title'=>'الاسم',
//            ],
            [
                'name'=>'items.name',
                'data'=>'name',
                'title'=>'الاسم',                
            ],
             [
                'name'=>'quantities.quantity',
                'data'=>'init',
                'title'=>'الرصيد الافتتاحي',                
            ],             [
                'name'=>'quantities.quantity',
                'data'=>'quantity',
                'title'=>'الكمية',                
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
        return 'QuantitiesInStore_' . date('YmdHis');
    }
}
