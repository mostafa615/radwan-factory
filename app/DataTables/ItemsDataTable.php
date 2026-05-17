<?php

namespace App\DataTables;

use App\Models\Item;
use Yajra\DataTables\Services\DataTable;

class ItemsDataTable extends DataTable
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
            ->addColumn('action', 'items.actions')
            ->addColumn('action', 'items.actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\client $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Item $model)
    {
        return $model
        ->select('items.name','items.id','code','price','groups.name as group','items.created_at')
        ->leftJoin('groups','items.group_id','=','groups.id')
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
                'name'=>'code',
                'data'=>'code',
                'title'=>'الكود',                
            ],
            [
                'name'=>'items.name',
                'data'=>'name',
                'title'=>'الاسم',                
            ],
            [
                'name'=>'price',
                'data'=>'price',
                'title'=>'سعر البيع',                
            ],
            [
                'name'=>'groups.name',
                'data'=>'group',
                'title'=>'المجموعة',                
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
        return 'Items_' . date('YmdHis');
    }
}
