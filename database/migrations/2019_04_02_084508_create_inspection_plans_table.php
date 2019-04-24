<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateInspectionPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inspection_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->comment('公司id');
            $table->string('num',20)->comment('计划编号');
            $table->string('name',20)->comment('计划名');
            $table->tinyInteger('type')->comment('计划类型 1常规计划 2临时计划');
            $table->string('cycle',20)->comment('周期');
            $table->integer('station_id')->comment('巡检站点id');
            $table->string('xobject',50)->comment('巡检对象');
            $table->string('time',50)->comment('巡检时间');
            $table->integer('examiner_id')->comment('巡检人id');
            $table->integer('admin_id')->comment('添加人id');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE inspection_plans comment '巡检计划表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inspection_plans');
    }
}
