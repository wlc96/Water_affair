<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateRoleAdminDirectoryBindsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_admin_directory_binds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->comment('公司id');
            $table->integer('role_id')->comment('角色id');
            $table->integer('admin_id')->comment('管理员id');
            $table->integer('directory_id')->comment('目录id');
            $table->integer('role_admin_bind_id')->comment('角色管理员绑定id');
            $table->integer('role_directory_bind_id')->comment('角色目录绑定id');
            $table->tinyInteger('can_add')->comment('可否增加');
            $table->tinyInteger('can_edit')->comment('可否编辑');
            $table->tinyInteger('can_delete')->comment('可否删除');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE role_admin_directory_binds comment '权限表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_admin_directory_binds');
    }
}
