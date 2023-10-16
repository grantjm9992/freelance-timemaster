<?php

use App\ValueObject\CheckStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::drop('checks');
        Schema::create('checks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id');
            $table->uuid('user_id');
            $table->string('status')->default( CheckStatus::open());
            $table->uuid('approved_by')->nullable();
            $table->uuid('check_type_id')->nullable();
            $table->longText('summary')->nullable();
            $table->uuid('task_id')->nullable();
            $table->uuid('project_id')->nullable();
            $table->uuid('client_id')->nullable();
            $table->timestamp('date_started');
            $table->timestamp('date_ended');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
