<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateIconUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('icon_users', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('type')->comment('头像类型');
            $table->string('url',200)->comment('头像地址');
            $table->integer('admin_id')->comment('管理员id')->nullable();
            $table->integer('user_id')->comment('用户id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE icon_users comment '用户头像表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('icon_users');
    }
}
