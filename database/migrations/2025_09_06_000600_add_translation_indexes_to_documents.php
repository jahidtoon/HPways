<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if(!Schema::hasTable('documents')) return;
        Schema::table('documents', function(Blueprint $table){
            if(!Schema::hasColumn('documents','translation_status')) return; // already there from original create
            $table->index(['needs_translation','translation_status'],'docs_translation_idx');
        });
    }

    public function down(): void
    {
        if(!Schema::hasTable('documents')) return;
        Schema::table('documents', function(Blueprint $table){
            $table->dropIndex('docs_translation_idx');
        });
    }
};
