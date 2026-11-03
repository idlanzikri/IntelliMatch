<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
        });
        foreach (['companies', 'vacancies', 'institutions'] as $name) {
            Schema::table($name, fn (Blueprint $table) => $table->boolean('location_is_approximate')->default(false));
        }
    }

    public function down(): void
    {
        foreach (['companies', 'vacancies', 'institutions'] as $name) {
            Schema::table($name, fn (Blueprint $table) => $table->dropColumn('location_is_approximate'));
        }
        Schema::table('companies', fn (Blueprint $table) => $table->dropColumn(['latitude', 'longitude']));
    }
};
