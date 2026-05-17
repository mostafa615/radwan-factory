<?php

namespace App\DataTables;

use App\Models\Account;
use Yajra\DataTables\Services\DataTable;

class AccountsDataTable extends DataTable
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
            // ->addColumn('type_transalated',function(Account $account){
            //         return $this->names()[$account->type.'Name'];
            // })
            ->addColumn('action', 'accounts.actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\client $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Account $model)
    {
        $owners = [
            'supplier'=>'App\Models\Supplier',
            'client'=>'App\Models\Client',
        ];

        $query =  $model
        ->select('accounts.id','accounts.type','accounts.created_at','accounts.date','reposites.name','cost','notes','chaque_no','image')
        ->where('accountable_type', $owners[$this->request()->owner])
        ->where('accountable_id', $this->request()->id)
        ->leftJoin('reposites','reposites.id','=','accounts.reposite_id')
        ->where('pending',false)
        ->latest();

        if(in_array($this->request()->type,['in','out'] )){
            $query = $query->where('accounts.type',$this->request()->type);
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
                    ->ajax([
                        'data' => 'function(data){
                                  data.type      =  type;
                        }'
                    ])
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
                'name'=>'reposites.name',
                'data'=>'name',
                'title'=>'الخزنة',
            ],
             [
                'name'=>'cost',
                'data'=>'cost',
                'title'=>'المدفوع',
            ], [
                'name'=>'notes',
                'data'=>'notes',
                'title'=>'ملاحظات',
            ],
            [
                'name'=>'chaque_no',
                'data'=>'chaque_no',
                'title'=>'شيك',
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
        return 'Accounts_' . date('YmdHis');
    }


    public function names()
    {
        $inName = 'وارد (الي الخزنة)';
        $outName = ' صادر (من الخزنة)';
        if(request('owner') == 'client'){
            $outName = 'مرتجع (من الخزنة)';
        } else {
            $inName = 'مرتجع (الي الخزنة)';
        }
        return compact('inName','outName');
    }
}
