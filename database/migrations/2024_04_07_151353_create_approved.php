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
        Schema::create('approved', function (Blueprint $table) {
            $table->id();
			$table->integer('file_id');
            $table->integer('сol_id');
			$table->string('col1');
			$table->string('col2');
			$table->string('col3');
			$table->string('col4');
			$table->string('col5');
			$table->string('col6');
			$table->string('col7');
			$table->string('col8');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approved');
    }
};
