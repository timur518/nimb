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
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Карточка основного канбана
            $table->text('name');
            $table->text('description')->nullable();
            // Этапы канбана1 и канбана2
            $table->string('goal_status')->default('todo')->nullable();
            $table->string('picture_url')->default('https://ammania.ru/image/catalog/ammania/trava/no_photo.jpg')->nullable();
            $table->integer('order')->default(0); // Это будет индекс порядка в колонке
            // Дедлайн
            $table->date('deadline_date')->nullable();
            // Сумма проекта и сколько внесено
            $table->integer('amount')->nullable();
            $table->integer('amount_coll')->nullable();
            // Крошки
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};
