<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->comment('公司id');
            $table->tinyInteger('status')->default(0)->comment('0 待处理 1已解决');
            $table->string('number',32)->comment('发票单号');
            $table->integer('user_id')->comment('用户id');
            $table->string('link_phone')->comment('联系电话');
            $table->string('head_name')->comment('抬头名称');
            $table->tinyInteger('type')->comment('1充值发票 2维护发票 3其他发票');
            $table->decimal('cost',10,4)->comment('金额');
            $table->string('address',100)->comment('邮寄地址');
            $table->integer('admin_id')->comment('操作员id');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE invoices comment '发票表'");
        DB::statement("ALTER TABLE costs comment '成本表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
