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
       Schema::create('applications', function (Blueprint $table) {
    $table->id();
    $table->string('applicant_name');
    $table->string('email');
    $table->string('country');
    $table->string('organization');
    $table->date('applied_date');
    $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
    $table->json('documents')->nullable();
    $table->string('region_code')->nullable();
    $table->string('access_scope')->nullable();
    $table->text('feedback')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
