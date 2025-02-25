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
        Schema::create('ieplanot_stundu', function (Blueprint $table) {
            $table->id();
            $table->integer('skaitlis');
            $table->unsignedBigInteger('kurssID');
            $table->unsignedBigInteger('laiksID');
            $table->unsignedBigInteger('datumsID');
            $table->unsignedBigInteger('stundaID');
            $table->unsignedBigInteger('pasniedzejsID');
            $table->timestamps();
            
            $table->foreign('kurssID')->references('id')->on('kurss')->onDelete('cascade');
            $table->foreign('laiksID')->references('id')->on('laiks')->onDelete('cascade');
            $table->foreign('datumsID')->references('id')->on('datums')->onDelete('cascade');
            $table->foreign('stundaID')->references('id')->on('stunda')->onDelete('cascade');
            $table->foreign('pasniedzejsID')->references('id')->on('pasniedzejs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ieplanot_stundu');
    }
};
