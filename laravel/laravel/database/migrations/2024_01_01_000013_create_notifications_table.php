<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('title', 200);
            $table->text('message');
            $table->string('type', 50);
            $table->jsonb('data')->nullable();
            $table->timestampTz('read_at')->nullable();
            $table->timestampTz('created_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            // Query paling umum: inbox user yang belum dibaca.
            $table->index(['user_id', 'read_at'], 'idx_notifications_user_unread');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
