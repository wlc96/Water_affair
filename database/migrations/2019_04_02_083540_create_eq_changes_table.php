<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateEqChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eq_changes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->comment('公司id');
            $table->tinyInteger('type')->comment('拆装类型 1换表 2暂拆 3重装 4销户拆表');
            $table->string('old_num',32)->nullable()->comment('原表码');
            $table->string('new_num',32)->nullable()->comment('现表码');
            $table->string('start_num',32)->nullable()->comment('起码');
            $table->string('end_num',32)->nullable()->comment('止码');
            $table->timestamp('move_time')->nullable()->comment('拆表时间');
            $table->integer('user_id')->comment('用户id');
            $table->integer('station_id')->comment('站点id');
            $table->string('order_num',20)->comment('流水单号');
            $table->string('servicing_id',20)->comment('操作人员id');
            $table->string('explain',100)->comment('说明')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE eq_changes comment '设备拆换表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eq_changes');
    }
}
