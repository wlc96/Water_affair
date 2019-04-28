<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cid')->comment('城市编号');
            $table->string('name',30)->comment('城市名');
            $table->tinyInteger('layer')->comment('城市等级');
            $table->integer('pid')->comment('父级城市id');
            $table->char('firstLetter',1)->comment('城市首字母');
            $table->string('full_firstLetter',20)->comment('城市所有字首字母');
            $table->string('fullLetter',100)->comment('城市字母全拼');
            $table->char('lng',20)->comment('经度');
            $table->char('lat',20)->comment('维度');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE cities comment '城市表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cities');
    }
}
