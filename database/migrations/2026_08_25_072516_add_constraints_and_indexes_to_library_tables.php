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
        Schema::table('library_tables', function (Blueprint $table) {
                Schema::table('authors', function (Blueprint $table) {
                $table->unique('name');
            });

            Schema::table('genres', function (Blueprint $table) {
                $table->unique('name');
            });

            Schema::table('books', function (Blueprint $table) {
                $table->dropIndex(['publication_year']);
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('library_tables', function (Blueprint $table) {
                Schema::table('authors', function (Blueprint $table) {
                $table->dropUnique(['name']);
            });

            Schema::table('genres', function (Blueprint $table) {
                $table->dropUnique(['name']);
            });

            Schema::table('books', function (Blueprint $table) {
                $table->index('publication_year');
            });
        });
    }
};
