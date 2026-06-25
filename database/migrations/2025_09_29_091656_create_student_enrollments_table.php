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
        Schema::create('student_enrollments', function (Blueprint $table) {
           $table->id('enrollment_id');
            $table->foreignId('student_id')
                ->constrained('students', 'student_id') 
                ->onDelete('cascade');
            $table->foreignId('section_id')
                ->constrained('sections', 'section_id')
                ->onDelete('cascade');
           $table->foreignId('semester_id')
                ->constrained('semesters', 'semester_id')
                ->onDelete('cascade');
              $table->string('status')->default('Active');
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
        Schema::dropIfExists('student_enrollments');
    }
};
