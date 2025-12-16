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
        Schema::create('detalle_orden', function (Blueprint $table) {
            $table->id('ID_Detalle_Recibo_NUMBER');
            $table->integer('Cantidad');
            $table->decimal('Subtotal', 10, 2);
            $table->text('Observacion')->nullable();
            $table->string('Estado', 50);
            $table->string('Tipo', 50);
            $table->decimal('Descuento', 10, 2)->default(0);
            $table->unsignedBigInteger('ID_Articulo');
            $table->unsignedBigInteger('ID_Recibo_NUMBER');
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('ID_Articulo')->references('ID_Articulo_NUMBER')->on('articulo')->onDelete('cascade');
            $table->foreign('ID_Recibo_NUMBER')->references('ID_Recibo_NUMBER')->on('orden')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_orden');
    }
};
