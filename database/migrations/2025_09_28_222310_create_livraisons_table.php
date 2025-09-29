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
        Schema::create('livraisons', function (Blueprint $table) {
            $table->id('idLivraison');
            $table->unsignedBigInteger('idOrder');
            $table->unsignedBigInteger('idClient');
            $table->string('adresseLivraison');
            $table->date('dateLivraison');
            $table->string('statut');
            $table->timestamps();

            $table->foreign('idOrder')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('idClient')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livraisons');
    }
};