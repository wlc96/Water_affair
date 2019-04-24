<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->comment('公司id');
            $table->string('number',32)->comment('事件编号');
            $table->tinyInteger('type')->comment('1维修事件');
            $table->tinyInteger('status')->comment('状态 0未处理 1正在处理 2处理完成');
            $table->integer('report_user_id')->comment('上报人id');
            $table->integer('station_id')->comment('站点id');
            $table->string('explain',50)->comment('事件说明')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE events comment '事件表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
