<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateEqRecoveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eq_recoveries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->comment('公司id');
            $table->string('equipment_id',32)->comment('设备id');
            $table->integer('user_id')->comment('用户id');
            $table->integer('station_id')->comment('站点id');
            $table->string('order_num',20)->comment('流水单号');
            $table->tinyInteger('way')->comment('恢复方式 1远程恢复 2手动恢复');
            $table->string('explain',100)->comment('恢复说明')->nullable();
            $table->string('operation',20)->comment('操作人员');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE eq_recoveries comment '设备恢复表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eq_recoveries');
    }
}
