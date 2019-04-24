<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreatePriceManagementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_managements', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->comment('公司id');
            $table->string('number',32)->comment('流水单号');
            $table->integer('station_id')->comment('站点id');
            $table->tinyInteger('type')->comment('收费类型');
            $table->decimal('original_price',10,4)->comment('原价');
            $table->decimal('existing_price',10,4)->comment('现价');
            $table->integer('admin_id')->comment('操作员id');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE price_managements comment '价格管理表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('price_managements');
    }
}
