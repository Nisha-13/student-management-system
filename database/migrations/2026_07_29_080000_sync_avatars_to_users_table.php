<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Sync existing avatars from students table to users table
     */
    public function up(): void
    {
        // Sync student avatars to their corresponding user records
        DB::statement("
            UPDATE users u
            INNER JOIN students s ON u.id = s.user_id
            SET u.avatar = s.avatar
            WHERE s.avatar IS NOT NULL
        ");

        // Note: Teachers table doesn't have avatar column yet
        // If teachers get avatar field in future, add similar sync here
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optional: Clear synced avatars from users table
        DB::statement("
            UPDATE users u
            INNER JOIN students s ON u.id = s.user_id
            SET u.avatar = NULL
            WHERE s.avatar IS NOT NULL
        ");
    }
};
