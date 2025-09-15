<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tracking_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->string('location')->nullable();
            $table->string('status_class', 30)->nullable(); // completed|current|pending
            $table->timestamp('event_time');
            $table->timestamps();
            $table->index(['shipment_id','event_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_events');
    }
};
