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
        Schema::create('job_posts', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->string('title');
            $table->integer('range_salary_1')->nullable();
            $table->integer('range_salary_2')->nullable();
            $table->integer('field_work_id');
            $table->integer('sub_field_work_id');
            $table->string('type_work');
            $table->string('work_policy');
            $table->string('work_experiece')->nullable();
            $table->string('minimum_study')->nullable();
            $table->string('age')->nullable();
            $table->json('allowances_and_benefits')->nullable();
            $table->text('skills');
            $table->text('description');
            $table->integer('status');
            $table->date('post_date');
            $table->date('post_expired_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_posts');
    }
};
