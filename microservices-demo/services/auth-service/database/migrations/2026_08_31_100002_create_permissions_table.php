<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('group_name', 50);
            $table->string('guard_name', 50)->default('web');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('group_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
