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
        Schema::create('semesters', function (Blueprint $table) {
            $table->id('semester_id');
            $table->foreignId('academic_year_id')
                ->constrained('academic_years', 'academic_year_id')
                ->onDelete('cascade');
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
        Schema::dropIfExists('semesters');
    }
};
