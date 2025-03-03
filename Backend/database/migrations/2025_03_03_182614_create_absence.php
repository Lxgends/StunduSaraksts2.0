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
        Schema::create('absence', function (Blueprint $table) {
            $table->id();
            $table->date('dienas')->nullable();
            $table->time('laiks')->nullable();
            $table->unsignedBigInteger('kurssID')->nullable();
            $table->unsignedBigInteger('pasniedzejsID')->nullable();
            $table->unsignedBigInteger('laiksID');
            $table->timestamps();



            $table->foreign('kurssID')->references('id')->on('kurss')->onDelete('cascade');
            $table->foreign('laiksID')->references('id')->on('laiks')->onDelete('cascade');
            $table->foreign('pasniedzejsID')->references('id')->on('pasniedzejs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absence');
    }
};
