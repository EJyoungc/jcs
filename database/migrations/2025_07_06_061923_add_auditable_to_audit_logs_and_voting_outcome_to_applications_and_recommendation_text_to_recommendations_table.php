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
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('auditable_type')->nullable()->after('description');
            $table->unsignedBigInteger('auditable_id')->nullable()->after('auditable_type');
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->string('voting_outcome')->nullable()->after('reviewed_at');
        });

        Schema::table('recommendations', function (Blueprint $table) {
            $table->text('recommendation_text')->after('application_id');
            $table->string('status')->after('recommendation_text');
            $table->dropColumn(['final_decision', 'report_path']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn(['auditable_type', 'auditable_id']);
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('voting_outcome');
        });

        Schema::table('recommendations', function (Blueprint $table) {
            $table->dropColumn(['recommendation_text', 'status']);
            $table->string('final_decision')->nullable();
            $table->string('report_path')->nullable();
        });
    }
};