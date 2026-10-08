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
        if (! Schema::hasTable('attendances')) {
            Schema::create('attendances', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            });
        }

        $columns = Schema::getColumnListing('attendances');

        foreach (['name', 'student_id', 'course', 'station', 'status'] as $column) {
            if (! in_array($column, $columns, true)) {
                Schema::table('attendances', function (Blueprint $table) use ($column) {
                    match ($column) {
                        'name' => $table->string('name')->nullable(),
                        'student_id' => $table->string('student_id')->nullable(),
                        'course' => $table->string('course')->nullable(),
                        'station' => $table->string('station')->nullable(),
                        'status' => $table->string('status')->default('Active'),
                    };
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
