<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('propertyables', function (Blueprint $table) {
            $table->string('property_key', 32);
            $table->integer('propertyable_id');
            $table->string('propertyable_type');
            $table->json('value')->nullable();
            $table->timestamps();

            $table->index(['property_key']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('propertyables');
    }
}
