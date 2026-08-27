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
        Schema::create('books', function (Blueprint $table) {
            $table->id();

            $table->string('book_code', 3)->unique();

            $table->string('title', 255);

            $table->foreignId('author_id')
                ->constrained('authors')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('genre_id')
                ->constrained('genres')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->smallInteger('publication_year');

            $table->text('description')->nullable();

            $table->timestamps();

            $table->index('author_id');
            $table->index('genre_id');
            $table->index('publication_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
