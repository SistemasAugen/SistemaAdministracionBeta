<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function($table) {
            $table->decimal('discount',11,2);
            $table->decimal('service',11,2);
            $table->decimal('total',11,2);
            $table->enum('status',['en_proceso','terminado','garantia','entregado','pagado'])->default("en_proceso");
            $table->integer('laboratory_id');
            $table->string('rx');
            $table->integer('percent_discount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
