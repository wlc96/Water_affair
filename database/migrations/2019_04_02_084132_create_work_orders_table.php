<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateWorkOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->comment('公司id');
            $table->string('number',32)->comment('工单编号');
            $table->integer('user_id')->comment('用户id');
            $table->string('link_phone',12)->comment('联系电话');
            $table->string('equipment_id',200)->comment('设备id');
            $table->string('explain',100)->comment('说明')->nullable();
            $table->tinyInteger('type')->comment('1APP异常 2水表异常 3充值异常');
            $table->tinyInteger('status')->comment('状态 0未处理 1正在处理 2处理完成')->default(0);
            $table->integer('servicing_id')->comment('操作员id');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE work_orders comment '工单表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_orders');
    }
}
