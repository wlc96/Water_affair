<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateEqDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eq_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->comment('公司id');
            $table->integer('station_id')->comment('站点id');
            $table->integer('real_num')->comment('实抄户')->default(0);
            $table->integer('should_num')->comment('应抄户')->default(0);
            $table->float('completion_rate',5,2)->comment('完成率');
            $table->float('accuracy_rate',5,2)->comment('准确率');
            $table->float('mr_rate',5,2)->comment('抄见率');
            $table->float('ws_rate',5,2)->comment('趸售比例');
            $table->timestamp('month')->comment('月份');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE eq_datas comment '设备数据表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eq_datas');
    }
}
