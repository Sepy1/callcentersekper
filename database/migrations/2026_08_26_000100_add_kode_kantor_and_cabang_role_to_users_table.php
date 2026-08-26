<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('kode_kantor', 50)->nullable()->after('no_hp');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','officer','qa','cabang') NOT NULL DEFAULT 'officer'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("UPDATE users SET role = 'officer' WHERE role = 'cabang'");
            DB::statement("ALTER TABLE users MODIFY role ENUM('admin','officer','qa') NOT NULL DEFAULT 'officer'");
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('kode_kantor');
        });
    }
};
