<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateRoleAdminBindsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_admin_binds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->comment('公司id');
            $table->integer('role_id')->comment('角色id');
            $table->integer('admin_id')->comment('管理员id');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE role_admin_binds comment '角色管理员绑定表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_admin_binds');
    }
}
