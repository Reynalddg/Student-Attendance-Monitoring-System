<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('academic_years', function (Blueprint $table) {
        $table->id('academic_year_id');
        $table->string('name'); 
        $table->date('start_date'); 
        $table->date('end_date');  
        $table->enum('status', ['current','previous','next'])->default('next');
        $table->timestamp('date_created')->default(DB::raw('CURRENT_TIMESTAMP'));
        $table->timestamp('date_archived')->nullable();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('academic_years');
    }
};
