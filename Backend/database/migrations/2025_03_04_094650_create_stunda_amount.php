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
        Schema::create('stunda_amount', function (Blueprint $table) {
            $table->id();
            $table->integer('daudzums');
            $table->unsignedBigInteger('stundaID');
            $table->unsignedBigInteger('pasniedzejsID');
            $table->unsignedBigInteger('kurssID');
            $table->timestamps();


            $table->foreign('stundaID')->references('id')->on('stunda')->onDelete('cascade');
            $table->foreign('pasniedzejsID')->references('id')->on('pasniedzejs')->onDelete('cascade');
            $table->foreign('kurssID')->references('id')->on('kurss')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stunda_amount');
    }
};
