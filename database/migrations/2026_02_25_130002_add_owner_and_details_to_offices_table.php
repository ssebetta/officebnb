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
        Schema::table('offices', function (Blueprint $table) {
            if (!Schema::hasColumn('offices', 'owner_id')) {
                $table->foreignId('owner_id')->nullable()->after('id')->constrained('users')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('offices', 'amenities')) {
                $table->json('amenities')->nullable()->after('description');
            }
            if (!Schema::hasColumn('offices', 'image_urls')) {
                $table->json('image_urls')->nullable()->after('amenities');
            }
            if (!Schema::hasColumn('offices', 'workspace_type')) {
                $table->string('workspace_type')->nullable()->after('image_urls');
            }
            if (!Schema::hasColumn('offices', 'size_sqft')) {
                $table->unsignedInteger('size_sqft')->nullable()->after('workspace_type');
            }
            if (!Schema::hasColumn('offices', 'meeting_rooms')) {
                $table->unsignedInteger('meeting_rooms')->default(0)->after('size_sqft');
            }
            if (!Schema::hasColumn('offices', 'desks')) {
                $table->unsignedInteger('desks')->default(0)->after('meeting_rooms');
            }
            if (!Schema::hasColumn('offices', 'timezone')) {
                $table->string('timezone')->nullable()->after('desks');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            if (Schema::hasColumn('offices', 'owner_id')) {
                $table->dropForeign(['owner_id']);
            }
            
            $columnsToCheck = [
                'owner_id',
                'amenities',
                'image_urls',
                'workspace_type',
                'size_sqft',
                'meeting_rooms',
                'desks',
                'timezone',
            ];

            $columnsToRemove = [];
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('offices', $column)) {
                    $columnsToRemove[] = $column;
                }
            }

            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
        });
    }
};
