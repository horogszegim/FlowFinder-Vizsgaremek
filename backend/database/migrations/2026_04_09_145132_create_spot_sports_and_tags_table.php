<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spot_sports_and_tags', function (Blueprint $table) {
            $table->foreignId('spot_id')->constrained('spots')->cascadeOnDelete();
            $table->foreignId('sports_and_tag_id')->constrained('sports_and_tags')->cascadeOnDelete();

            $table->primary(['spot_id', 'sports_and_tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spot_sports_and_tags');
    }
};
