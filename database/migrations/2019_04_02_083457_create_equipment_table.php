<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateEquipmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->string('id',32)->primary();
            $table->tinyInteger('type')->comment('设备类型 1大口径 2小口径');
            $table->integer('equipment_type_id')->comment('设备型号id');
            $table->tinyInteger('status')->comment('设备状态 0 关闭 1开启');
            $table->string('eq_addr',32)->comment('设备地址号');
            $table->string('eq_num',32)->comment('机芯编号');
            $table->integer('station_id')->comment('站点id 0无站点属于库存');
            $table->tinyInteger('use_type')->comment('用水性质 1民用 2商用');
            $table->decimal('total_water',10,4)->comment('累计用水量');
            $table->string('address',50)->comment('设备地址');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE equipment comment '设备表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('equipment');
    }
}
