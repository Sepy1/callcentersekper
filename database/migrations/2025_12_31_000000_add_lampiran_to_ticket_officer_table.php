<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLampiranToTicketOfficerTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('ticket_officer')) {
            Schema::table('ticket_officer', function (Blueprint $table) {
                if (! Schema::hasColumn('ticket_officer', 'lampiran')) {
                    $table->string('lampiran')->nullable()->after('status');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('ticket_officer')) {
            Schema::table('ticket_officer', function (Blueprint $table) {
                if (Schema::hasColumn('ticket_officer', 'lampiran')) {
                    $table->dropColumn('lampiran');
                }
            });
        }
    }
}
