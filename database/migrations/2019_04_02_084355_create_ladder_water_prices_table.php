<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateLadderWaterPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ladder_water_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->comment('公司id');
            $table->integer('station_id')->comment('站点id');
            $table->decimal('first_order',10,4)->comment('一阶水费');
            $table->decimal('second_order',10,4)->comment('二阶水费');
            $table->decimal('third_order',10,4)->comment('三阶水费');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE ladder_water_prices comment '阶梯水价表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ladder_water_prices');
    }
}
