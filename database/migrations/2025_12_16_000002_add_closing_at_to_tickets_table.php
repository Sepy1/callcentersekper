<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClosingAtToTicketsTable extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('tickets')) {
            return;
        }
        Schema::table('tickets', function (Blueprint $table) {
            if (! Schema::hasColumn('tickets', 'closing_at')) {
                $table->timestamp('closing_at')->nullable()->after('status');
            }
        });
    }

    public function down()
    {
        if (! Schema::hasTable('tickets')) {
            return;
        }
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'closing_at')) {
                $table->dropColumn('closing_at');
            }
        });
    }
}
