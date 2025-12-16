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
        Schema::create('orden', function (Blueprint $table) {
            $table->id('ID_Recibo_NUMBER');
            $table->dateTime('Fecha');
            $table->string('Forma_de_pago', 10);
            $table->decimal('Total', 10, 2);
            $table->string('Estado_Dio', 50);
            $table->boolean('Envio_Bool')->default(false);
            $table->unsignedBigInteger('ID_Cliente');
            $table->unsignedBigInteger('ID_Empleado')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('ID_Cliente')->references('ID_Cliente_NUMBER')->on('cliente')->onDelete('cascade');
            $table->foreign('ID_Empleado')->references('ID_Empleado_NUMBER')->on('empleado')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden');
    }
};
