<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('waste_banks', function (Blueprint $table) {
            $table->string('phone', 13)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('waste_banks', function (Blueprint $table) {
            $table->string('phone', 30)->nullable()->change();
        });
    }
};
