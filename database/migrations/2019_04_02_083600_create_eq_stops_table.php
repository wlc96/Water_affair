<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateEqStopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eq_stops', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->comment('公司id');
            $table->string('equipment_id',32)->comment('设备id');
            $table->integer('user_id')->comment('用户id');
            $table->integer('station_id')->comment('站点id');
            $table->string('order_num',20)->comment('流水单号');
            $table->tinyInteger('type')->comment('停水类型 1欠费停水 2维修停水 3换表停水');
            $table->tinyInteger('way')->comment('停水方式 1 远程停水 2手动停水');
            $table->string('affected_area',30)->comment('影响区域')->nullable();
            $table->string('explain',100)->comment('停水说明')->nullable();
            $table->string('operation',20)->comment('操作人员');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE eq_stops comment '设备暂停表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eq_stops');
    }
}
