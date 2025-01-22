<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->string('action')->nullable()->after('level'); // Ajouter une colonne "action"
            $table->unsignedBigInteger('user_id')->nullable()->after('action'); // Ajouter une colonne "user_id"
            $table->ipAddress('ip_address')->nullable()->after('user_id'); // Ajouter une colonne "ip_address"
            $table->string('status')->nullable()->after('ip_address'); // Ajouter une colonne "status"
            $table->json('details')->nullable()->after('status'); // Ajouter une colonne "details"
        });
    }

    public function down(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropColumn('action');
            $table->dropColumn('user_id');
            $table->dropColumn('ip_address');
            $table->dropColumn('status');
            $table->dropColumn('details');
        });
    }
};
