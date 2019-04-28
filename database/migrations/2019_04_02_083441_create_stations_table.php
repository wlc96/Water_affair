<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateStationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->comment('公司id');
            $table->tinyInteger('status')->comment('营业点状态 0暂停 1正常');
            $table->integer('user_id')->comment('建站人id');
            $table->string('name',30)->comment('站点名');
            $table->string('linkman',20)->comment('联系人');
            $table->string('phone',20)->comment('电话');
            $table->integer('city_id')->comment('城市id');
            $table->char('lng',20)->comment('经度');
            $table->char('lat',20)->comment('维度');
            $table->string('pic',200)->comment('站点图片');
            $table->string('business_hours',50)->comment('营业时间');
            $table->string('address',50)->comment('站点地址');
            $table->decimal('history_num',15,4)->comment('历史用水量');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE stations comment '站点表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stations');
    }
}
