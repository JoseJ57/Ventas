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
        Schema::create('talla_tipo_articulos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('talla_id')
            ->constrained('tallas')
            ->onDelete('cascade');
            $table->foreignId('tipo_articulo_id')
            ->constrained('tipo_articulos')
            ->onDelete('cascade');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('talla_tipo_articulos');
    }
};
