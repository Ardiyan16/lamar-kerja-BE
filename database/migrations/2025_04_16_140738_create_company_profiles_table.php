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
        Schema::create('company_profiles', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('name');
            $table->string('address')->nullable();
            $table->integer('province')->nullable();
            $table->integer('regency')->nullable();
            $table->integer('district')->nullable();
            $table->string('telp_number', 15)->nullable();
            $table->integer('type_industry')->nullable();
            $table->string('total_employee')->nullable();
            $table->text('about_us')->nullable();
            $table->text('corporate_culture')->nullable();
            $table->json('link', 500)->nullable();
            $table->json('social_media')->nullable();
            $table->string('logo_profile')->nullable();
            $table->json('gallery')->nullable();
            $table->text('motto')->nullable();
            $table->integer('total_job_posts')->default(0);
            $table->integer('is_premium')->default(0);
            $table->integer('status_profile')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_profiles');
    }
};
