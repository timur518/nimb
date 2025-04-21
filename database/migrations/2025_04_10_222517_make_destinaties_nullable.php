<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
// Пример миграции для изменения столбцов
    public function up(): void
    {
        Schema::table('destinies', function (Blueprint $table) {
            $table->text('mission')->nullable()->change();
            $table->text('vocation')->nullable()->change();
            $table->text('passion')->nullable()->change();
            $table->text('profession')->nullable()->change();
            $table->text('destiny')->nullable()->change();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
