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
    Schema::create('sections', function (Blueprint $table) {
        $table->id('section_id');
        $table->string('grade_level');
        $table->string('section_name');
        $table->unsignedBigInteger('user_id'); 
        $table->unsignedBigInteger('track_strand_id');
        $table->timestamp('date_created')->default(DB::raw('CURRENT_TIMESTAMP'));
        $table->timestamp('date_archived')->nullable();
        $table->foreign('user_id')
            ->references('user_id') 
            ->on('users')
            ->onDelete('cascade');

        $table->foreign('track_strand_id') // ✅ updated FK
            ->references('track_strand_id')
            ->on('tracks')
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
        Schema::dropIfExists('sections');
    }
};
