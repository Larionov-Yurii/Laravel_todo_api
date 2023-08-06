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
        Schema::create('subtasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_task_id');
            $table->foreign('parent_task_id')->references('id')->on('tasks')->cascadeOnDelete();
            $table->enum('status', ['todo', 'done']);
            $table->unsignedTinyInteger('priority');
            $table->string('title');
            $table->text('description');
            $table->timestamps();
            $table->timestamp('completedAt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subtasks');
    }
};
