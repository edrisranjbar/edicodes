<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_contents', function (Blueprint $table) {
            $table->longText('content')->nullable()->change();
            $table->string('content_format', 10)->default('text');
        });
    }

    public function down(): void
    {
        // Keep the larger column to avoid truncating existing HTML on rollback.
        Schema::table('course_contents', function (Blueprint $table) {
            $table->dropColumn('content_format');
        });
    }
};
