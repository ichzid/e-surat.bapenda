<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('departments')) {
            Schema::create('departments', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'department_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('department_id')->nullable()->after('role')->constrained()->nullOnDelete();
            });
        }

        if (Schema::hasTable('documents')) {
            Schema::table('documents', function (Blueprint $table) {
                if (!Schema::hasColumn('documents', 'department_id')) {
                    $table->foreignId('department_id')->nullable()->after('sender_or_receiver')->constrained()->nullOnDelete();
                }

                if (!Schema::hasColumn('documents', 'status')) {
                    $table->string('status')->default('draft')->after('department_id');
                }
            });

            DB::table('documents')
                ->where(function ($query) {
                    $query->whereNull('status')->orWhere('status', 'draft');
                })
                ->update(['status' => 'selesai']);
        }

        if (!Schema::hasTable('dispositions')) {
            Schema::create('dispositions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('document_id')->constrained()->cascadeOnDelete();
                $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
                $table->string('target_role')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->text('note')->nullable();
                $table->string('follow_up_status')->nullable();
                $table->text('follow_up_note')->nullable();
                $table->timestamp('followed_up_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('dispositions');

        if (Schema::hasTable('documents')) {
            Schema::table('documents', function (Blueprint $table) {
                if (Schema::hasColumn('documents', 'department_id')) {
                    $table->dropConstrainedForeignId('department_id');
                }

                if (Schema::hasColumn('documents', 'status')) {
                    $table->dropColumn('status');
                }
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'department_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropConstrainedForeignId('department_id');
            });
        }

        Schema::dropIfExists('departments');
    }
};
