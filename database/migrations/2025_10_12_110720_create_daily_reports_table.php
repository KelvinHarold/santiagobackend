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
    Schema::create('daily_reports', function (Blueprint $table) {
        $table->id();
        $table->string('match_name');
        $table->date('report_date')->default(now());
        $table->decimal('total_income', 12, 2);
        $table->decimal('remaining_balance', 12, 2)->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
