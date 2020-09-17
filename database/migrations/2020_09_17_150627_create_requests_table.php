<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->float('from_lat', 10,6);
            $table->float('from_lng', 10,6);
            $table->float('to_lat', 10,6);
            $table->float('to_lng', 10,6);
            $table->datetime('from_time', 0);
            $table->datetime('delivery_time', 0)->nullable();
            $table->enum('status', ['pending','acepted','on way','delivered','canceled'])->default('pending');
            $table->enum('qualification', [1,2,3,4,5])->nullable();
            $table->longText('msg')->nullable();
            $table->timestamps();

            $table->foreignId('service_id')
                  ->constrained('services')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('manager_id')
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requests');
    }
}
