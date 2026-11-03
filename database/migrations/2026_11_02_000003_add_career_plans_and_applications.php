<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->string('qualification_level')->nullable();
            $table->foreignId('target_career_id')->nullable()->constrained('careers')->nullOnDelete();
            $table->json('completed_steps')->nullable();
        });
        Schema::table('vacancies', fn (Blueprint $table) => $table->string('qualification_level')->nullable());
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vacancy_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('submitted');
            $table->timestamps();
            $table->unique(['user_id', 'vacancy_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
        Schema::table('vacancies', fn (Blueprint $table) => $table->dropColumn('qualification_level'));
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('target_career_id');
            $table->dropColumn(['qualification_level', 'completed_steps']);
        });
    }
};
