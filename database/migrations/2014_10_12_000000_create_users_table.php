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
        Schema::create('users', function (Blueprint $table) {
           $table->id();
            $table->string('tipo');
            $table->string('password');
            $table->string('bi')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('genero')->nullable();
            $table->string('morada')->nullable();
            $table->string('nomePai')->nullable();
            $table->string('nomeMae')->nullable();
            $table->string('telefone')->nullable();
            $table->string('dataNascimento')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
