<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false);
        });
        Schema::create('careers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('sector');
            $table->text('description');
            $table->json('skills');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        foreach (config('careers') as $career) {
            DB::table('careers')->insert([
                'title' => $career['title'], 'sector' => $career['sector'],
                'description' => $career['description'], 'skills' => json_encode($career['skills']),
                'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('careers');
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('is_admin'));
    }
};
