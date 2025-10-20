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
        Schema::table('livraisons', function (Blueprint $table) {
            $table->string('delivery_proof_photo')->nullable()->after('statut');
            $table->string('delivery_signature')->nullable()->after('delivery_proof_photo');
            $table->text('delivery_notes')->nullable()->after('delivery_signature');
            $table->timestamp('proof_uploaded_at')->nullable()->after('delivery_notes');
            $table->boolean('client_confirmed')->default(false)->after('proof_uploaded_at');
            $table->timestamp('client_confirmed_at')->nullable()->after('client_confirmed');
            $table->text('client_confirmation_notes')->nullable()->after('client_confirmed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('livraisons', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_proof_photo',
                'delivery_signature',
                'delivery_notes',
                'proof_uploaded_at',
                'client_confirmed',
                'client_confirmed_at',
                'client_confirmation_notes'
            ]);
        });
    }
};
