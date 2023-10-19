<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mongodb')->create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('company_id')->nullable();
            $table->string('user_id')->nullable();
            $table->string('client_id')->nullable();
            $table->string('project_id')->nullable();
            $table->string('task_id')->nullable();
            $table->string('status')->nullable();
            $table->string('recipient')->nullable();
            $table->string('payer')->nullable();
            $table->longText('items')->nullable();
            $table->string('total')->nullable();
            $table->string('currency')->nullable();
            $table->string('tax_rate')->nullable();
            $table->string('tax_applied')->nullable();
            $table->string('total_including_tax')->nullable();
            $table->string('create_date')->nullable();
            $table->string('due_date')->nullable();
            $table->string('paid_date')->nullable();
            $table->string('amount_paid')->nullable();
            $table->string('title')->nullable();
            $table->longText('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice');
    }
};
