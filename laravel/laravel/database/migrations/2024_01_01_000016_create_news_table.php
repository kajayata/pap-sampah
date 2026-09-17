<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->string('thumbnail_storage_key', 500)->nullable();
            $table->text('content');
            $table->unsignedInteger('author_id');
            $table->string('status', 20)->default('DRAFT');
            $table->timestampTz('published_at')->nullable();
            $table->timestampTz('created_at')->useCurrent();

            $table->foreign('author_id')->references('id')->on('users')->restrictOnDelete();
        });

        DB::statement("ALTER TABLE news ADD CONSTRAINT chk_news_status CHECK (status IN ('DRAFT', 'PUBLISHED', 'ARCHIVED'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
