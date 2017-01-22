<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Kalnoy\Nestedset\NestedSet;
class AddNestedAndSizeFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('uploads', function (Blueprint $table) {
            NestedSet::columns($table);
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('type')->nullable();
            $table->boolean('watermarked')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('uploads', function (Blueprint $table) {
            NestedSet::dropColumns($table);
            $table->dropColumn('width');
            $table->dropColumn('height');
            $table->dropColumn('type');
            $table->dropColumn('watermarked');
        });
    }
}
