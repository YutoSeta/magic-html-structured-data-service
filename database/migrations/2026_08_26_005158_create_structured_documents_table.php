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
        Schema::create('structured_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('project_id', 100);
            $table->string('document_key', 100);
            $table->string('name');
            $table->string('kind', 50)->default('generic');
            $table->json('schema');
            $table->json('value');
            $table->json('metadata')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->timestamps();

            $table->unique(['project_id', 'document_key']);
            $table->index(['project_id', 'kind']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('structured_documents');
    }
};
