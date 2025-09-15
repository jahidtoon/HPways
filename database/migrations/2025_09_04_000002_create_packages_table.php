<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        // Legacy migration converted to NO-OP to prevent duplicate table creation.
        if (Schema::hasTable('packages')) {
            return; // already exists from earlier migration path
        }
        // (If fresh install without earlier migration, we create minimal normalized schema)
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_category_id')->nullable()->constrained('visa_categories')->nullOnDelete();
            $table->string('visa_type',40)->nullable();
            $table->string('code',30)->default('basic');
            $table->string('name');
            $table->unsignedInteger('price_cents')->default(0);
            $table->json('features')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->unique(['visa_type','code']);
        });
    }
    public function down() {
        // Do not drop to avoid data loss in shared normalized path.
    }
};
