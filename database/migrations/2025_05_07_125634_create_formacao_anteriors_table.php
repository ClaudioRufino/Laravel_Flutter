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
        Schema::create('formacao_anteriors', function (Blueprint $table) {
            $table->id();
            $table->string('nomeEscola');
            $table->string('mediaCurso');
            $table->string('anoConclusao');
            $table->string('cursoConcluido');
            $table->timestamps();

            $table->foreignId('user_id')->constrained('users'); /* Chave Estrangeira */
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formacao_anteriors');
    }
};
