<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->timestamp('entry_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->boolean('was_successful');

            $table->unsignedBigInteger('uw_created')->nullable();
            $table->unsignedBigInteger('uw_updated')->nullable();
            $table->unsignedBigInteger('uw_deleted')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('uw_created')->references('id')->on('users');
            $table->foreign('uw_updated')->references('id')->on('users');
            $table->foreign('uw_deleted')->references('id')->on('users');

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_entries');
    }
};
