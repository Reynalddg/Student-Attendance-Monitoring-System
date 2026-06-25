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
      Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id('attendance_id');
              $table->foreignId('enrollment_id')
          ->constrained('student_enrollments', 'enrollment_id')
          ->onDelete('cascade');
            $table->timestamp('date_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('status');
            $table->timestamp('date_created')->default(DB::raw('CURRENT_TIMESTAMP'));
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_logs');
    }
};
