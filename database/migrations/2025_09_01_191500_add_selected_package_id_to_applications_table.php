<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->foreignId('selected_package_id')->nullable()->after('visa_type')->constrained('packages')->nullOnDelete();
        });
    }
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('selected_package_id');
        });
    }
};
