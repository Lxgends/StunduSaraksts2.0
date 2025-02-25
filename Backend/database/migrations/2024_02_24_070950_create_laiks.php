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
        Schema::create('laiks', function (Blueprint $table) {
            $table->id();
            $table->text('DienasTips');
            $table->time('sakumalaiks');
            $table->time('beigulaiks');
            $table->text('Diena');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laiks');
    }
};
