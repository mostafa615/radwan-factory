<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Supplies;
use App\MachineSupplie;
use DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        // $supplies = Supplies::latest()->get();

        // foreach ($supplies as $supply) {
        //     DB::table('supplies')
        //             ->where('id', $supply->id)
        //             ->update([
        //             'init_quantity' => $supply->quantity
        //         ]);
        //     if(!@empty($supply->MachineSupplies)){
        //         $totalQuantity = Supplies::where('id', $supply->id)->select(
        //             DB::raw('(select SUM(quantity) from machine_supplies where machine_supplies.supplie_id = supplies.id) as totalQuantity')
        //         )->first()->totalQuantity;

        //         DB::table('supplies')
        //             ->where('id', $supply->id)
        //             ->update([
        //             'init_quantity' => $totalQuantity+$supply->quantity
        //         ]);
        //     }
        // }
    }
}
