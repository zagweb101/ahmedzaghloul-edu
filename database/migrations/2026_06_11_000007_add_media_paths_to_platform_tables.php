<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('learning_paths', function (Blueprint $table) {
            $table->string('cover_image_path')->nullable()->after('description');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->string('thumbnail_path')->nullable()->after('summary');
            $table->string('pdf_path')->nullable()->after('pdf_url');
        });

        Schema::table('live_events', function (Blueprint $table) {
            $table->string('cover_image_path')->nullable()->after('description');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_path')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('learning_paths', function (Blueprint $table) {
            $table->dropColumn('cover_image_path');
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn(['thumbnail_path', 'pdf_path']);
        });

        Schema::table('live_events', function (Blueprint $table) {
            $table->dropColumn('cover_image_path');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar_path');
        });
    }
};
