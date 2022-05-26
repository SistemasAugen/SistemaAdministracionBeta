<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacturacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facturacions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id');
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('celphone')->nullable();
            $table->string('rfc')->nullable();
            $table->string('address')->nullable();
            $table->string('suburb')->nullable();
            $table->integer('state_id')->nullable();
            $table->integer('town_id')->nullable();
            $table->integer('postal_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('facturacions');
    }
}
