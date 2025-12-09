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
        Schema::create('articulos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre',40);
            $table->string('eslogan',100);
            $table->text('descripcion')->max(300);
            $table->text('recomendacion')->max(300);
            $table->string('image_url',255);
            $table->unsignedDecimal('precio',10,2);
            $table->enum('estado',['disponible','agotado','no disponible'])
            ->default('disponible');
            $table->foreignId('tipo_articulo_id')
            ->constrained('tipo_articulos')
            ->onDelete('cascade')
            ->onUpdate('cascade');
            $table->foreignId('marca_id')
            ->nullabel()
            ->constrained('marcas')
            ->onUpdate('cascade')
            ->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articulos');
    }
};
