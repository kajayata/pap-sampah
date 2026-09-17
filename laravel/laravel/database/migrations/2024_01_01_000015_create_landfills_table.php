<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landfills', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('village_id');
            $table->string('name', 150);
            $table->string('address', 255);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            $table->foreign('village_id')->references('id')->on('villages')->restrictOnDelete();
            $table->index('village_id');
        });

        DB::statement('ALTER TABLE landfills ADD COLUMN location geometry(Point, 4326) NOT NULL');
        DB::statement('CREATE INDEX idx_landfills_location ON landfills USING GIST (location)');
    }

    public function down(): void
    {
        Schema::dropIfExists('landfills');
    }
};
