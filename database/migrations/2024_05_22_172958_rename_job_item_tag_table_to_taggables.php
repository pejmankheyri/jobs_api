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
        Schema::table('job_item_tag', function (Blueprint $table) {
            $table->dropForeign(['job_item_id']);
            $table->dropColumn('job_item_id');
        });

        Schema::rename('job_item_tag', 'taggables');

        Schema::table('taggables', function (Blueprint $table) {
            $table->morphs('taggable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('taggables', function (Blueprint $table) {
            $table->dropMorphs('taggable');
        });

        Schema::rename('taggables', 'job_item_tag');

        Schema::disableForeignKeyConstraints();

        Schema::table('job_item_tag', function (Blueprint $table) {
            $table->foreignId('job_item_id')->constrained()->onDelete('cascade');
        });

        Schema::enableForeignKeyConstraints();
    }
};
