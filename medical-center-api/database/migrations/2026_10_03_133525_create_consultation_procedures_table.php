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
        Schema::create('consultation_procedures', function (Blueprint $table) {
            $table->id();

            $table->foreignId('consultation_id')
                ->constrained('consultations')
                ->cascadeOnDelete();

            $table->foreignId('procedure_id')
                ->constrained('procedures')
                ->restrictOnDelete();

            $table->unsignedInteger('quantity')->default(1);

            // Price at the moment the procedure was performed
            $table->decimal('unit_price', 12, 2);

            $table->string('tooth')->nullable();
            $table->text('observation')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_procedures');
    }
};
