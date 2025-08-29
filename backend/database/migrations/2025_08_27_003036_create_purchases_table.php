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
        Schema::create('course_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content')->nullable(); // For text content
            $table->enum('type', ['video', 'text'])->default('text');
            $table->integer('order')->default(0);
            $table->string('video_path')->nullable(); // Path to video file
            $table->integer('video_duration')->nullable(); // Duration in seconds
            $table->boolean('is_free')->default(false); // Whether this content is free to preview
            $table->timestamps();
            
            $table->index(['course_id', 'order']);
            $table->index(['course_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_contents');
    }
};
