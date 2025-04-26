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
        Schema::create('data', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('uploader');
            $table->string('group');
            $table->string('imgURI');
            $table->integer('spandukCount')->default(0);
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('long', 10, 7)->nullable();
            $table->string('thoroughfare')->nullable();
            $table->string('subLocality')->nullable();
            $table->string('locality')->nullable();
            $table->string('subAdmin')->nullable();
            $table->string('adminArea')->nullable();
            $table->string('postalCode')->nullable();
            $table->boolean('deleted')->default(false);
            $table->timestamps();
            
            $table->foreign('uploader')->references('username')->on('users');
            $table->foreign('group')->references('group_name')->on('groups');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data');
    }
};