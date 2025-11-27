<?php

// database/migrations/2024_01_01_create_posts_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This method executes when you run: php artisan migrate
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            // Primary key - auto-incrementing ID
            $table->id(); // Creates: id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY

            // String column - max 255 characters by default
            $table->string('title'); // Creates: title VARCHAR(255) NOT NULL

            // Text column - for longer content
            $table->text('content'); // Creates: content TEXT NOT NULL
            // String with custom length
            $table->foreignId('author_id')->constrained('authors')->cascadeOnDelete();


            // Boolean with default value
            $table->boolean('published')->default(false);
            // Creates: published TINYINT(1) NOT NULL DEFAULT 0

            // Automatically adds created_at and updated_at timestamp columns
            $table->timestamps();
            // Creates: created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL
        });
    }

    /**
     * Reverse the migrations.
     * This method executes when you run: php artisan migrate:rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
