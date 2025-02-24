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
        Schema::create('pasniedzejs', function (Blueprint $table) {
            $table->id();
            $table->string('Vards', 50);
            $table->string('Uzvards', 50);
            $table->unsignedBigInteger('KabinetsID')->nullable();
            $table->timestamps();

            $table->foreign('KabinetsID')
                  ->references('id')
                  ->on('kabinets')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasniedzejs');
    }
};
