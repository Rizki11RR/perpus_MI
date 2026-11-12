<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('import_batches', 'duplicate_mode')) {
            Schema::table('import_batches', function (Blueprint $table): void { $table->string('duplicate_mode', 20)->default('skip')->after('status'); });
        }
    }

    public function down(): void
    {
        Schema::table('import_batches', function (Blueprint $table): void { $table->dropColumn('duplicate_mode'); });
    }
};
