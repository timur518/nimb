<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('balance_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Секция: Финансы
            $table->tinyInteger('finance_goals')->nullable();
            $table->tinyInteger('finance_learning')->nullable();
            $table->tinyInteger('finance_environment')->nullable();
            $table->tinyInteger('finance_tracking')->nullable();
            $table->tinyInteger('finance_saving')->nullable();
            $table->tinyInteger('finance_income')->nullable();
            $table->tinyInteger('finance_economy')->nullable();
            $table->tinyInteger('finance_investment')->nullable();

            // Секция: Карьера
            $table->tinyInteger('career_growth')->nullable();
            $table->tinyInteger('career_engagement')->nullable();
            $table->tinyInteger('career_environment')->nullable();
            $table->tinyInteger('career_balance')->nullable();
            $table->tinyInteger('career_rewards')->nullable();
            $table->tinyInteger('career_goals')->nullable();
            $table->tinyInteger('career_satisfaction')->nullable();

            // Секция: Саморазвитие
            $table->tinyInteger('self_education')->nullable();
            $table->tinyInteger('self_growth')->nullable();
            $table->tinyInteger('self_skills')->nullable();
            $table->tinyInteger('self_creativity')->nullable();
            $table->tinyInteger('self_social')->nullable();
            $table->tinyInteger('self_planning')->nullable();
            $table->tinyInteger('self_discipline')->nullable();

            // Духовность и творчество
            $table->tinyInteger('soul_practices')->nullable();
            $table->tinyInteger('soul_creativity')->nullable();
            $table->tinyInteger('soul_knowledge')->nullable();
            $table->tinyInteger('soul_people')->nullable();
            $table->tinyInteger('soul_nature')->nullable();
            $table->tinyInteger('soul_reflection')->nullable();

            // Отдых и хобби
            $table->tinyInteger('rest_passive')->nullable();
            $table->tinyInteger('rest_active')->nullable();
            $table->tinyInteger('rest_hobbies')->nullable();
            $table->tinyInteger('rest_social')->nullable();
            $table->tinyInteger('rest_learning')->nullable();
            $table->tinyInteger('rest_relaxation')->nullable();

            // Друзья и окружение
            $table->tinyInteger('friends_support')->nullable();
            $table->tinyInteger('friends_meetings')->nullable();
            $table->tinyInteger('friends_culture')->nullable();
            $table->tinyInteger('friends_interests')->nullable();
            $table->tinyInteger('friends_trust')->nullable();
            $table->tinyInteger('friends_variety')->nullable();

            // Семья
            $table->tinyInteger('family_emotion')->nullable();
            $table->tinyInteger('family_time')->nullable();
            $table->tinyInteger('family_rituals')->nullable();
            $table->tinyInteger('family_communication')->nullable();
            $table->tinyInteger('family_support')->nullable();
            $table->tinyInteger('family_health')->nullable();

            // Здоровье
            $table->tinyInteger('health_physical')->nullable();
            $table->tinyInteger('health_emotional')->nullable();
            $table->tinyInteger('health_social')->nullable();
            $table->tinyInteger('health_sleep')->nullable();
            $table->tinyInteger('health_prevention')->nullable();
            $table->tinyInteger('health_balance')->nullable();

            // Анализ GPT
            $table->text('gpt_analyse')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balance_indicators');
    }
};
