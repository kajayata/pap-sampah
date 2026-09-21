<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waste_banks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('village_id');
            $table->string('name', 150);
            $table->string('address', 255);
            $table->string('phone', 30)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            $table->foreign('village_id')->references('id')->on('villages')->restrictOnDelete();
            $table->index('village_id');
        });

        DB::statement('ALTER TABLE waste_banks ADD COLUMN location geometry(Point, 4326) NOT NULL');
        DB::statement('CREATE INDEX idx_waste_banks_location ON waste_banks USING GIST (location)');
    }

    public function down(): void
    {
        Schema::dropIfExists('waste_banks');
    }
};
