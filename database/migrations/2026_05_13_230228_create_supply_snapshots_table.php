<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplySnapshotsTable extends Migration
{
    public function up()
    {
        Schema::create('supply_snapshots', function (Blueprint $table) {
            // Must be bigIncrements to match BIGINT
            $table->bigIncrements('id');

            // Must be unsignedBigInteger to match the BIGINT id in supplies table
            $table->unsignedBigInteger('supplie_id');
            
            $table->double('quantity');
            $table->date('snapshot_date');
            $table->timestamps();

            // Set the foreign key
            $table->foreign('supplie_id')
                  ->references('id')
                  ->on('supplies')
                  ->onDelete('cascade');

            $table->unique(['supplie_id', 'snapshot_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('supply_snapshots');
    }

}
