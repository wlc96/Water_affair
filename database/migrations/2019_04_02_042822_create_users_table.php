<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',20)->comment('用户名');
            $table->string('password',40)->comment('密码');
            $table->string('phone',20)->comment('手机号');
            $table->string('email',50)->comment('邮箱');
            $table->softDeletes();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE users comment '用户表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
