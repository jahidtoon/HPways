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
        Schema::create('quiz_nodes', function (Blueprint $table) {
            $table->id();
            $table->string('node_id')->unique();
            $table->string('title');
            $table->text('question')->nullable();
            $table->string('type')->default('single'); // single or multi
            $table->json('options'); // array of options
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_nodes');
    }
};
