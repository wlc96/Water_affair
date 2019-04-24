<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateCashWithdrawalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_withdrawals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->comment('公司id');
            $table->string('number',32)->comment('流水号');
            $table->decimal('sum',10,4)->comment('充值金额');
            $table->tinyInteger('type')->comment('提现方式 1银行卡 2支付宝');
            $table->tinyInteger('status')->comment('提现状态 0未完成 1完成');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE cash_withdrawals comment '提现订单表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cash_withdrawals');
    }
}
