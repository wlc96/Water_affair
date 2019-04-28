<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('status')->comment('状态 0未激活 1已激活');
            $table->string('name',32)->comment('公司名');
            $table->string('address',50)->comment('公司地址');
            $table->string('linkman',20)->comment('联系人');
            $table->string('link_phone',20)->comment('联系人电话');
            $table->string('phone',20)->comment('公司电话')->nullable();
            $table->integer('icon_company_id')->comment('公司头像/logo_id');
            $table->string('account_name',50)->comment('开户名称');
            $table->string('account_bank',50)->comment('开户银行');
            $table->string('card_num',32)->comment('银行卡号');
            $table->string('Alipay_num',50)->comment('支付宝账号');
            $table->string('finance_phone',20)->comment('财务电话');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE companies comment '公司表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
