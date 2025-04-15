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
        Schema::create('absences', function (Blueprint $table) {
            $table->id();
            $table->enum('absence_type', ['pasniedzejs', 'kurss']);
            $table->unsignedBigInteger('pasniedzejsID')->nullable();
            $table->unsignedBigInteger('kurssID')->nullable();
            $table->date('sakuma_datums');
            $table->date('beigu_datums');
            $table->text('piezimes')->nullable();
            $table->timestamps();
            
            $table->foreign('pasniedzejsID')->references('id')->on('pasniedzejs')->nullOnDelete();
            $table->foreign('kurssID')->references('id')->on('kurss')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};