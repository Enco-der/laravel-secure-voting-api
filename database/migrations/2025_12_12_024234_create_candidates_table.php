<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('candidates', function (Blueprint $table) {
        $table->id();
        $table->string('username')->unique();    // LOGIN
        $table->string('password');              // LOGIN PASSWORD

        $table->string('applicant_name');
        $table->string('email')->unique();
        $table->string('country');
        $table->string('organization');
        $table->date('applied_date');
        $table->string('region_code');

        $table->enum('access_scope', ['city', 'state', 'national']);

        $table->string('cnic_picture')->nullable();   // single file
        $table->json('documents')->nullable();        // multiple files

        $table->text('feedback')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
