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
        Schema::create('provinces', function (Blueprint $table) {
            $table->string('id', 2)->primary()->comment('Province code (PERMENDAGRI 58/2021)');
            $table->string('name');
            $table->string('slug')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });

        Schema::create('regencies', function (Blueprint $table) {
            $table->string('id', 4)->primary()->comment('Regency/City code (PERMENDAGRI 58/2021)');
            $table->string('province_id', 2)->index()->comment('Province code');
            $table->string('name');
            $table->string('slug')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();

            $table->foreign('province_id')->references('id')->on('provinces')->onDelete('cascade');
        });

        Schema::create('districts', function (Blueprint $table) {
            $table->string('id', 6)->primary()->comment('District code (PERMENDAGRI 58/2021)');
            $table->string('regency_id', 4)->index()->comment('Regency/City code');
            $table->string('name');
            $table->string('slug')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();

            $table->foreign('regency_id')->references('id')->on('regencies')->onDelete('cascade');
        });

        Schema::create('villages', function (Blueprint $table) {
            $table->string('id', 10)->primary()->comment('Village code (PERMENDAGRI 58/2021)');
            $table->string('district_id', 6)->index()->comment('District code');
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villages');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('regencies');
        Schema::dropIfExists('provinces');
    }
};
