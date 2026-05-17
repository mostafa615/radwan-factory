<?php

namespace App\DataTables;

use App\Models\Role;
use Yajra\DataTables\Services\DataTable;

class RolesDataTable extends DataTable
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
            ->editColumn('role_permissions', function(Role $role){
                $permissions =  $role->perms;
                return view('roles.datatable.permissions',compact('permissions'))
                ->render();
            })
            ->addColumn('action', 'roles.datatable.actions')
            ->rawColumns(['action','role_permissions']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Detail $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Role $model)
    {
        return $model
        ->newQuery()
        ->select(
        'roles.id','permission_role.role_id',
        'roles.name as role_name', 'permissions.display_name',
        'permission_role.permission_id','roles.created_at')
        ->leftJoin('permission_role','roles.id','=','permission_role.role_id')
        ->leftJoin('permissions','permissions.id','=','permission_role.permission_id')
        ->groupBy('roles.id')
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
            //  as 
            [
                'name'=>'roles.name',
                'data'=>'role_name',
                'title'=>'الاسم',                
            ],
            [
                'name'=>'permissions.display_name',
                'data'=>'role_permissions',
                'title'=>'الصلاحيات',                
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
        return 'Admin\Roles_' . date('YmdHis');
    }
}
