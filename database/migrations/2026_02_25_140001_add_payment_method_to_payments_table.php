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
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'payment_method')) {
                $table->string('payment_method')->default('stripe')->after('provider');
            }
            if (!Schema::hasColumn('payments', 'payment_details')) {
                $table->text('payment_details')->nullable()->after('payment_method');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $columnsToRemove = [];
            if (Schema::hasColumn('payments', 'payment_method')) {
                $columnsToRemove[] = 'payment_method';
            }
            if (Schema::hasColumn('payments', 'payment_details')) {
                $columnsToRemove[] = 'payment_details';
            }
            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
        });
    }
};
