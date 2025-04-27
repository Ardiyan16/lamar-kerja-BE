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
        Schema::create('profile_users', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('place_birth');
            $table->date('date_birth');
            $table->string('whatsapp');
            $table->enum('gender', ['Laki-laki', 'Perempuan', 'Tidak ingin memberi tahu', 'Lainnya']);
            $table->text('address');
            $table->integer('postal_code');
            $table->string('picture')->nullable();
            $table->text('about_me')->nullable();
            $table->text('education')->nullable();
            $table->string('last_education');
            $table->text('skill')->nullable();
            $table->integer('field_work_id')->nullable();
            $table->string('position', 500)->nullable();
            $table->string('job_type')->nullable();
            $table->string('salary_expectation')->nullable();
            $table->text('work_city_preference')->nullable();
            $table->integer('is_remote')->nullable();
            $table->string('resume')->nullable();
            $table->string('link_portfolio');
            $table->text('social_media')->nullable();
            $table->text('certificated')->nullable();
            $table->text('award')->nullable();
            $table->integer('total applications')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_users');
    }
};
