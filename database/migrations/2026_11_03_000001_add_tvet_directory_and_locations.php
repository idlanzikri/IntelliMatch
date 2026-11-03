<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agencies', function (Blueprint $table) {
            $table->id();
            $table->string('source_id')->unique();
            $table->string('name');
            $table->timestamps();
        });
        Schema::table('institutions', function (Blueprint $table) {
            $table->foreignId('agency_id')->nullable()->constrained()->nullOnDelete();
            $table->text('address')->nullable();
            $table->string('state')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
        });
        Schema::create('institution_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->string('source_id')->unique();
            $table->text('name');
            $table->text('source_url');
            $table->timestamps();
        });
        Schema::table('vacancies', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('vacancies', fn (Blueprint $table) => $table->dropColumn(['latitude', 'longitude']));
        Schema::dropIfExists('institution_courses');
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('agency_id');
            $table->dropColumn(['address', 'state', 'latitude', 'longitude']);
        });
        Schema::dropIfExists('agencies');
    }
};
