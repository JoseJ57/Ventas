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
        Schema::create('talla_articulos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('talla_id')
            ->constrained('tallas')
            ->onDelete('cascade');
            $table->foreignId('articulo_id')
            ->constrained('articulos')
            ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('talla__articulos');
    }
};
