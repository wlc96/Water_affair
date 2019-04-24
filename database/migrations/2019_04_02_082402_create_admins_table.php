<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->comment('管理员公司id');
            $table->string('name',30)->comment('管理员名');
            $table->string('password',40)->comment('管理员密码');
            $table->string('phone',20)->comment('管理员手机号');
            $table->string('email',30)->comment('管理员邮箱');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE admins comment '管理员表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
