<?php


Route::group(['prefix' => LaravelLocalization::setLocale(),'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]],
    function() {
        Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function(){

            Route::get('/index', 'WelcomeController@index')->name('index');


            // machine_types routes
            Route::resource('machine_types', 'MachineTypesController')->except(['show']);
            Route::get('machine_types/datatable', 'MachineTypesController@getData')->name('machine_types.machine_types_Datatable');



            // machines routes
            Route::resource('machines', 'MachinesController')->except(['show']);
            Route::get('machines/datatable', 'MachinesController@getData')->name('machines.machines_Datatable');
            Route::get('machines/get_machines_by_type', 'MachinesController@getMachinesByType')->name('machines.get_machines_by_type');

            // machine_items routes
            Route::resource('machine_items', 'MachineItemController')->except(['show']);
            Route::get('machine_items/datatable', 'MachineItemController@getData')->name('machine_items.machine_items_Datatable');
            Route::get('machine_items/get_item_quantity', 'MachineItemController@getItemQuantity')->name('machine_items.get_item_quantity');

            // machine_groups routes
            Route::resource('machine_groups', 'MachineGroupController')->except(['show']);
            Route::get('machine_groups/datatable', 'MachineGroupController@getData')->name('machine_groups.machine_groups_Datatable');

            // machine_supplies routes
            Route::resource('machine_supplies', 'MachineSupplieController')->except(['show']);
            Route::get('machine_supplies/datatable', 'MachineSupplieController@getData')->name('machine_supplies.machine_supplies_Datatable');
            Route::get('machine_supplies/get_supply_quantity', 'MachineSupplieController@getSuppliesQuantity')->name('machine_supplies.get_supply_quantity');

            // supplie_types routes
            Route::resource('supplie_types', 'SupplieTypesController')->except(['show']);
            Route::get('supplie_types/datatable', 'SupplieTypesController@getData')->name('supplie_types.supplie_types_Datatable');

            // supplies routes
            Route::resource('supplies', 'SuppliesController')->except(['show']);
            Route::get('supplies/datatable', 'SuppliesController@getData')->name('supplies.supplies_Datatable');
            Route::get('supplies/getSuppliesByMachine', 'SuppliesController@getSuppliesByMachine')->name('supplies.getSuppliesByMachine');
            Route::get('supplies/getSuppliesByMachineReport', 'SuppliesController@getSuppliesByMachineReport')->name('supplies.getSuppliesByMachineReport');
            Route::get('supplies/exchange/create', 'SuppliesController@exchange_create_view')->name('supplies.exchange_machine_supplies_create_view');
            Route::get('supplies/exchange/edit/{exchange_id}', 'SuppliesController@edit_exchange')->name('supplies.exchange_machine_supplies_edit_view');
            Route::get('supplies/exchange', 'SuppliesController@exchange_view')->name('supplies.exchange_machine_supplies_view');
            Route::POST('supplies/exchange/update/{exchange_id}', 'SuppliesController@exchange_update')->name('supplies.exchange_update_machine_supplies');
            Route::POST('supplies/exchange', 'SuppliesController@exchange')->name('supplies.exchange_machine_supplies');
            Route::get('supplies/exchange/delete/{exchange_id}', 'SuppliesController@exchange_delete')->name('supplies.exchange_delete_machine_supplies');
            // Route::resource('supplies', 'SuppliesController')->except(['show']);
            // Route::get('supplies/datatable', 'SuppliesController@getData')->name('supplies.supplies_Datatable');
            // Route::get('supplies/getSuppliesByMachine', 'SuppliesController@getSuppliesByMachine')->name('supplies.getSuppliesByMachine');
            // Route::get('supplies/exchange', 'SuppliesController@exchange_view')->name('supplies.exchange_machine_supplies_view');
            // Route::POST('supplies/exchange', 'SuppliesController@exchange')->name('supplies.exchange_machine_supplies');

            // operation_orders routes
            Route::resource('operation_orders', 'OperationOrderController')->except(['show']);
            Route::get('operation_orders/datatable', 'OperationOrderController@getData')->name('operation_orders.operation_orders_Datatable');
            Route::get('operation_orders/datatable_out', 'OperationOrderController@getDataOut')->name('operation_orders.operation_orders_Datatable_out');
            Route::get('operation_orders/index_out', 'OperationOrderController@indexOut')->name('operation_orders.index_out');
            Route::get('operation_orders/create_out', 'OperationOrderController@createOut')->name('operation_orders.createOut');
            Route::post('operation_orders/store_out', 'OperationOrderController@storeOut')->name('operation_orders.storeOut');
            Route::get('operation_orders/{id}/edit_out', 'OperationOrderController@editOut')->name('operation_orders.edit_out');
            Route::post('operation_orders/{id}/update_out', 'OperationOrderController@updateOut')->name('operation_orders.update_out');
            Route::get('operation_orders/update_is_complete/{id}', 'OperationOrderController@updateIsComplete')->name('operation_orders.update_is_complete');

            Route::get('operation_orders/summary', 'OperationOrderController@summary')->name('operation_orders.summary');

            Route::get('operation_orders/complete_out/{id}', 'OperationOrderController@showCompleteOut')->name('operation_orders.showCompleteOut');
            Route::put('operation_orders/complete_out/{id}', 'OperationOrderController@updateCompleteOut')->name('operation_orders.updateCompleteOut');
            Route::post('operation_orders/machine_access/{id}', 'OperationOrderController@machineAccess')->name('operation_orders.machineAccess');
            
            Route::post('operation_orders/updateConfirm', 'OperationOrderController@updateConfirm')->name('operation_orders.updateConfirm');
            Route::get('operation_orders/get_items_by_machine', 'OperationOrderController@getItemsByMachine')->name('operation_orders.get_items_by_machine');
            Route::get('operation_orders/get_inGroups_by_machine', 'OperationOrderController@getInGroupsByMachine')->name('operation_orders.get_inGroups_by_machine');
            Route::get('operation_orders/get_outGroups_by_machine', 'OperationOrderController@getOutGroupsByMachine')->name('operation_orders.get_outGroups_by_machine');
            Route::get('operation_orders/get_items_by_group', 'OperationOrderController@getItemsByGroup')->name('operation_orders.get_items_by_group');
            Route::get('operation_orders/get_supplies_by_machine', 'OperationOrderController@getSuppliesByMachine')->name('operation_orders.get_supplies_by_machine');
            Route::get('operation_orders/get_machineitem_quantity', 'OperationOrderController@getMachineItemQnt')->name('operation_orders.get_machineitem_quantity');
            Route::get('operation_orders/get_item_quantity', 'OperationOrderController@getItemQuantity')->name('operation_orders.get_item_quantity');
            Route::get('operation_orders/show/{operationOrder}', 'OperationOrderController@show')->name('operation_orders.show');
            
            Route::get('operation_orders/show-store/{operationOrder}', 'OperationOrderController@showStore')->name('operation_orders.showStore');
  
            Route::get('order_delete_detail/{id}', 'OperationOrderController@order_delete_detail');


            //action_histories
            Route::get('action_histories', 'ActionHistoryController@index')->name('action_histories.index');
            Route::get('action_histories/datatable', 'ActionHistoryController@getData')->name('action_histories.datatable');

            // operation_order_results routes
            Route::resource('operation_order_results', 'OperationOrderResultController')->except(['show']);
            Route::get('operation_order_results/datatable', 'OperationOrderResultController@getData')->name('operation_order_results.operation_order_results_Datatable');
            Route::get('operation_order_results/datatable_out', 'OperationOrderResultController@getDataOut')->name('operation_order_results.operation_order_results_Datatable_out');
            Route::get('operation_order_results/index_out', 'OperationOrderResultController@indexOut')->name('operation_order_results.index_out');
            Route::get('operation_order_results/create_out', 'OperationOrderResultController@createOut')->name('operation_order_results.create_out');
            Route::post('operation_order_results/store_out', 'OperationOrderResultController@storeOut')->name('operation_order_results.store_out');
            Route::get('operation_order_results/get_opertOrderInfo', 'OperationOrderResultController@getOpertOrderInfo')->name('operation_order_results.get_opertOrderInfo');
            Route::get('operation_order_results/get_opertOrderWeight', 'OperationOrderResultController@getOpertOrderWeight')->name('operation_order_results.get_opertOrderWeight');
            Route::get('operation_order_results/del_edit/{operation_id}', 'OperationOrderResultController@del_edit')->name('operation_order_results.del_edit');
            Route::get('operation_order_results/get_damageWeight', 'OperationOrderResultController@getDamageWeight')->name('operation_order_results.get_damageWeight');
            Route::get('operation_order_results/show/{operationOrderResult}', 'OperationOrderResultController@show')->name('operation_order_results.show');
            Route::get('operation_order_results/show_out/{operationOrderResult}', 'OperationOrderResultController@showOut')->name('operation_order_results.show_out');
            
            Route::get('operation_order_results/show-store/{operationOrderResult}', 'OperationOrderResultController@showStore')->name('operation_order_results.showStore');
            Route::get('operation_order_results/show-out-store/{operationOrderResult}', 'OperationOrderResultController@showOutStore')->name('operation_order_results.showOutStore');
            
            Route::post('operation_order_results/updateConfirm', 'OperationOrderResultController@updateConfirm')->name('operation_order_results.updateConfirm');
            Route::post('operation_order_results/updateConfirmOut', 'OperationOrderResultController@updateConfirmOut')->name('operation_order_results.updateConfirmOut');
            Route::get('order_result_delete_detail/{id}', 'OperationOrderResultController@order_result_delete_detail');
            Route::get('auto_complete_first', 'OperationOrderResultController@auto_complete_first')->name('auto_complete_first');
            Route::get('auto_complete_second', 'OperationOrderResultController@auto_complete_second')->name('auto_complete_second');

            //damages routes
            Route::resource('damages', 'DamageController')->except(['show']);
            Route::get('damages/datatable', 'DamageController@getData')->name('damages.damages_Datatable');
            Route::get('damages/auto_complete_first', 'DamageController@auto_complete_first')->name('damages.auto_complete_first');

            //specials routes
            Route::resource('specials', 'SpecialController')->except(['show']);
            Route::get('specials/datatable', 'SpecialController@getData')->name('specials.specials_Datatable');
            Route::get('specials/auto_complete_first', 'SpecialController@auto_complete_first')->name('specials.auto_complete_first');


            //student_histories routes
            Route::resource('student_histories', 'StdHistoryController')->except(['show']);
            Route::get('student_histories/datatable', 'StdHistoryController@getData')->name('student_histories.stdHistoryDatatable');



            //reports routs
            Route::get('reports', 'ReportController@index')->name('reports.index');
            Route::get('reports/machine_report', 'ReportController@machine_report')->name('reports.machine_report');
            Route::get('reports/machine_supplie_inventory', 'ReportController@machineSupplieInventory')->name('reports.machine_supplie_inventory');
            Route::get('reports/damage_special_report', 'ReportController@damage_special_report')->name('reports.damage_special');
            Route::get('reports/scraps_report', 'ReportController@scraps_report')->name('reports.scraps_report');
            Route::get('reports/pieces_report', 'ReportController@pieces_report')->name('reports.pieces_report');
            Route::get('reports/machine_supplies_report', 'ReportController@machineSuppliesReport')->name('reports.machine_supplies');
            Route::get('reports/new_machine_supplies_report', 'ReportController@newMachineSuppliesReport')->name('reports.new_machine_supplies');
            Route::get('reports/confirm_notes_report', 'ReportController@confirmNotesReport')->name('reports.confirm_notes');
            Route::get('reports/operation_order_results', 'ReportController@operationOrderResults')->name('reports.operation_order_results');
            Route::get('reports/employees_performance', 'ReportController@employeesPerformance')->name('reports.employees_performance');
            Route::get('reports/supplies', 'ReportController@suppliesReport')->name('reports.supplies');
            Route::get('reports/machine_supplie_used_inventory', 'ReportController@machine_supplie_used_inventory')->name('reports.machine_supplie_used_inventory');

            Route::get('reports/mahcine-performance', 'ReportController@machinePerformance')->name('reports.machinePerformance');
            Route::get('reports/employee-performance', 'ReportController@employeePerformance')->name('reports.employeePerformance');
            Route::get('reports/sync-current-quantities', 'ReportController@syncCurrentQuantities')->name('reports.syncCurrentQuantities');
            //admin routes
            Route::resource('admins', 'AdminController')->except(['show']);


            //user routes
            Route::resource('users', 'UserController')->except(['show']);


            // register doctor to course
            Route::get('course/assign/{course}', 'SubjectController@assign')->name('assignDoctorToCourseView');
            Route::post('course/assign/{course}', 'SubjectController@performAssign')->name('assignDoctorToCourse');

            // register student to course
            Route::get('course/students', 'StudentSubjectController@getStudents')->name('courseStudentData');
            Route::post('course/student-assign', 'StudentSubjectController@performAssign')->name('assignStudentToCourse');

            //military_status routes
            Route::resource('military_status', 'MilitaryStatusController')->except(['show']);
            Route::resource('nationalities', 'NationalityController')->except(['show']);
            Route::resource('governments', 'GovernmentController')->except(['show']);



        });//end the dashboard routes

        Route::get('notifications/operation-orders-in', 'NotificationController@inOperationOrders')->name('inOperationOrdersNotify');
        Route::get('notifications/operation-orders-out', 'NotificationController@outOperationOrders')->name('outOperationOrdersNotify');
    });

       //profile updates
       Route::post('profile/changname/{id}', 'UserController@changeName');
       Route::post('profile/changpass/{id}', 'UserController@changePass');
       Route::post('profile/changemail/{id}', 'UserController@changeEmail');
       Route::post('profile/changphone/{id}', 'UserController@changePhone');


 //new
    Route::post('update-status', 'UpdateStatusController@updateStatus');


