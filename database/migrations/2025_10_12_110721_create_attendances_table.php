<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('attendances', function (Blueprint $table) {
        $table->id();
        $table->foreignId('daily_report_id')->constrained()->onDelete('cascade');
        $table->integer('main_hall_people')->default(0);
        $table->integer('main_hall_staff')->default(0);
        $table->integer('vip_hall_people')->default(0);
        $table->integer('vip_hall_staff')->default(0);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
