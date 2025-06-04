<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Core Tables
        Schema::create('schools', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('npsn')->unique()->nullable();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('nss')->unique()->nullable();
            $table->string('edu_type')->nullable(); // education_type
            $table->string('status')->nullable(); // school_status
            $table->year('est_year')->nullable(); // establishment_year
            $table->string('op_permit_no')->nullable(); // operational_permit_number
            $table->date('op_permit_date')->nullable();
            $table->string('accreditation')->nullable();
            $table->string('accred_score')->nullable();
            $table->year('accred_year')->nullable();
            $table->string('curriculum')->nullable();
            $table->string('village_id', 10)->nullable()->index();
            $table->string('district_id', 6)->nullable()->index();
            $table->string('regency_id', 4)->nullable()->index();
            $table->string('province_id', 2)->nullable()->index();
            $table->string('postal_code', 10)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('address')->nullable();
            $table->timestamps();

            $table->foreign('village_id')->references('id')->on('villages')->onDelete('set null');
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('set null');
            $table->foreign('regency_id')->references('id')->on('regencies')->onDelete('set null');
            $table->foreign('province_id')->references('id')->on('provinces')->onDelete('set null');
        });

        Schema::create('infra_cats', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Land, Building, etc
            $table->string('type')->nullable(); // type
            $table->string('code')->nullable(); // code
            $table->text('desc')->nullable(); // description
            $table->timestamps();
        });

        Schema::create('room_refs', function (Blueprint $table) { // room_references
            $table->id();
            $table->string('name')->unique();
            $table->string('type');
            $table->string('code', 25)->nullable();
            $table->text('desc')->nullable();
            $table->timestamps();
        });

        // Physical Infrastructure
        Schema::create('lands', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('infra_cat_id')->constrained('infra_cats')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('code', 25)->nullable();
            $table->string('name');
            $table->string('cert_no')->nullable(); // certificate_number
            $table->decimal('length', 10, 2)->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('area', 10, 2)->nullable();
            $table->decimal('avail_area', 10, 2)->nullable(); // available_area
            $table->string('ownership')->nullable();
            $table->decimal('njop', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->foreignUuid('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        Schema::create('buildings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('infra_cat_id')->constrained('infra_cats')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('land_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->string('code', 25)->nullable();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->decimal('length', 10, 2)->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('area', 10, 2)->nullable();
            $table->string('ownership')->nullable();
            $table->string('borrow_status')->nullable();
            $table->decimal('asset_value', 15, 2)->nullable();
            $table->integer('floors')->nullable();
            $table->year('build_year')->nullable();
            $table->text('notes')->nullable();
            $table->date('permit_date')->nullable();
            $table->decimal('foundation_vol', 10, 2)->nullable();
            $table->decimal('roof_vol', 10, 2)->nullable();
            $table->decimal('truss_len', 10, 2)->nullable(); // truss_length
            $table->decimal('rafter_len', 10, 2)->nullable();
            $table->decimal('batten_len', 10, 2)->nullable();
            $table->decimal('roof_area', 10, 2)->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->foreignUuid('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        Schema::create('rooms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('room_ref_id')->constrained('room_refs')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignUuid('building_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('code', 25)->nullable();
            $table->string('name');
            $table->string('reg_no')->nullable(); // registration_number
            $table->integer('floor')->nullable();
            $table->decimal('length', 10, 2)->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('area', 10, 2)->nullable();
            $table->integer('capacity')->nullable();
            $table->decimal('plaster_area', 10, 2)->nullable();
            $table->decimal('ceiling_area', 10, 2)->nullable();
            $table->decimal('wall_area', 10, 2)->nullable();
            $table->decimal('window_area', 10, 2)->nullable();
            $table->decimal('door_area', 10, 2)->nullable();
            $table->decimal('frame_len', 10, 2)->nullable();
            $table->decimal('floor_area', 10, 2)->nullable();
            $table->decimal('elec_area', 10, 2)->nullable(); // electrical
            $table->integer('elec_points')->nullable();
            $table->decimal('water_len', 10, 2)->nullable();
            $table->integer('water_points')->nullable();
            $table->integer('drain_len')->nullable(); // drainage_length
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->foreignUuid('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // Supporting Tables
        Schema::create('infra_conditions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuidMorphs('entity'); // Replaces 'conditionable'
            $table->string('condition'); // light/heavy damage
            $table->string('slug')->nullable();
            $table->decimal('percentage', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->date('checked_at');
            $table->timestamps();
        });

        Schema::create('infra_legal', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuidMorphs('entity'); // Replaces 'legal_statusable'
            $table->string('status');
            $table->string('doc_no')->nullable(); // document_number
            $table->date('doc_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('infra_docs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuidMorphs('entity'); // Replaces 'documentable'
            $table->string('name');
            $table->string('path'); // file_path
            $table->timestamps();
        });

        Schema::create('other_facil', function (Blueprint $table) { // facilities
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('infra_cat_id')->constrained('infra_cats')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->integer('qty')->default(1); // quantity
            $table->text('specs')->nullable(); // specifications
            $table->decimal('value', 15, 2)->nullable(); // acquisition_value
            $table->year('acq_year')->nullable(); // acquisition_year
            $table->foreignUuid('created_by')->nullable()->constrained('users');
            $table->foreignUuid('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        Schema::create('infra_acquisitions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuidMorphs('entity'); // Replaces 'acquisitionable'
            $table->string('source'); // funding_source
            $table->decimal('amount', 15, 2)->nullable(); // value
            $table->year('year')->nullable(); // acquisition_year
            $table->string('method')->nullable(); // acquisition_method
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('infra_relocations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuidMorphs('entity'); // Replaces 'relocatable'
            $table->string('from')->nullable(); // original_location
            $table->string('to'); // new_location
            $table->date('moved_at'); // relocation_date
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('facil_conditions', function (Blueprint $table) { // facility_conditions
            $table->uuid('id')->primary();
            $table->foreignUuid('facil_id')->constrained('other_facil')->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('condition', ['good', 'light', 'heavy']);
            $table->string('slug')->nullable();
            $table->decimal('percentage', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->date('checked_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facil_conditions');
        Schema::dropIfExists('infra_relocations');
        Schema::dropIfExists('infra_acquisitions');
        Schema::dropIfExists('other_facil');
        Schema::dropIfExists('infra_docs');
        Schema::dropIfExists('infra_legal');
        Schema::dropIfExists('infra_conditions');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('buildings');
        Schema::dropIfExists('lands');
        Schema::dropIfExists('room_refs');
        Schema::dropIfExists('infra_cats');
        Schema::dropIfExists('schools');
    }
};
