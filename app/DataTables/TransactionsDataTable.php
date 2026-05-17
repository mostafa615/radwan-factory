<?php

namespace App\DataTables;

use App\Models\Transaction;
use Yajra\DataTables\Services\DataTable;

class TransactionsDataTable extends DataTable
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
            ->editColumn('date',function(Transaction $transaction){
                return optional($transaction->date)->toDateString();
            })
            ->addColumn('from',function(Transaction $transaction){
                return optional($transaction->from)->name;  
            })
            ->addColumn('to',function(Transaction $transaction){
                return optional($transaction->to)->name;  
            })
            ->addColumn('from',function(Transaction $transaction){
                return optional($transaction->from)->name;  
            })
            ->addColumn('action', 'transactions.actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\client $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Transaction $model)
    {
        $query =  $model
        ->select('transactions.id',
        'from_id','to_id',
        'date',
        'cost','notes','users.name',
        'pending','transactions.created_at')
        ->leftJoin('users','users.id','=','transactions.user_id')
        ->where(function ($q){
            if (Auth()->user()->id != 1){
                $q->where('user_id',auth()->user()->id);
            }
        })
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
                    ->minifiedAjax()
                    ->columns($this->getColumns())
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
                'title'=>'رقم العملية ',                
            ],
            [
                'name'=>'date',
                'data'=>'date',
                'title'=>'التاريخ',                
            ],
            [
                'name'=>'from',
                'data'=>'from',
                'title'=>'من',                
            ],
            [
                'name'=>'to',
                'data'=>'to',
                'title'=>'الي',                
            ],
             [
                'name'=>'cost',
                'data'=>'cost',
                'title'=>'المدفوع',                
            ], 
            [
                'name'=>'notes',
                'data'=>'notes',
                'title'=>'ملاحظات',                
            ], 
            // [
            //     'name'=>'type_transalated',
            //     'data'=>'type_transalated',
            //     'title'=>'النوع',                
            // ], 
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
            // 'order' => [ [0,'desc'] ],
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
        return 'Transactions_' . date('YmdHis');
    }
}
