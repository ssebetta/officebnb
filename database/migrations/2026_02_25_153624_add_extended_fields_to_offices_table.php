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
            $table->foreignId('owner_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            $table->json('amenities')->nullable()->after('description');
            $table->json('image_urls')->nullable()->after('amenities');
            $table->string('workspace_type')->nullable()->after('image_urls');
            $table->unsignedInteger('size_sqft')->nullable()->after('workspace_type');
            $table->unsignedInteger('meeting_rooms')->default(0)->after('size_sqft');
            $table->unsignedInteger('desks')->default(0)->after('meeting_rooms');
            $table->string('timezone')->nullable()->after('desks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offices', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->dropColumn([
                'owner_id',
                'amenities',
                'image_urls',
                'workspace_type',
                'size_sqft',
                'meeting_rooms',
                'desks',
                'timezone',
            ]);
        });
    }
};
