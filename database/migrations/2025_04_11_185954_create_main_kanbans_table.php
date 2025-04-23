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
        Schema::create('main_kanbans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Карточка основного канбана
            $table->text('name');
            $table->text('description')->nullable();
            // Этапы канбана1 и канбана2
            $table->string('sortban_status')->nullable();
            $table->string('kanban_status')->default('todo')->nullable();
            $table->integer('order')->default(0); // Это будет индекс порядка в колонке
            // Список задач внутри проекта
//            $table->string('tasks_ids')->nullable();

            // Запланировано и дедлайн
            $table->date('planned_date')->nullable();
            $table->date('deadline_date')->nullable();
            // Сумма проекта и сколько внесено
            $table->integer('amount')->nullable();
            $table->integer('amount_coll')->nullable();
            // Крошки
            $table->tinyInteger('progress')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('main_kanbans');
    }
};
