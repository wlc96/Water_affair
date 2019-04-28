<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('costs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->comment('公司id');
            $table->string('number',32)->comment('成本单号');
            $table->tinyInteger('type')->comment('1设备维护 2精华成本 3其他问题');
            $table->decimal('cost',10,4)->comment('金额');
            $table->integer('admin_id')->comment('操作员id');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE costs comment '成本表'");
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('costs');
    }
}
