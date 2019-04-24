<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateRechargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recharges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->comment('公司id');
            $table->string('number',32)->comment('订单号');
            $table->integer('user_id')->comment('用户id');
            $table->integer('station_id')->comment('站点id');
            $table->string('equipment_id',32)->comment('设备id');
            $table->decimal('water_quantity',10,4)->comment('充值水量');
            $table->decimal('sum',10,4)->comment('充值金额');
            $table->tinyInteger('type')->comment('充值方式 1营业点 2App 3PC');
            $table->tinyInteger('status')->comment('充值状态 0未完成 1完成');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE recharges comment '充值订单表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recharges');
    }
}
