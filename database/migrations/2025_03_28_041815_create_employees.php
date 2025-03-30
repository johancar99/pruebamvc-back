<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id('id');
            $table->string('document')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('department');
            $table->boolean('access')->default(false);

            $table->unsignedBigInteger('uw_created')->nullable();
            $table->unsignedBigInteger('uw_updated')->nullable();
            $table->unsignedBigInteger('uw_deleted')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('uw_created')->references('id')->on('users');
            $table->foreign('uw_updated')->references('id')->on('users');
            $table->foreign('uw_deleted')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
