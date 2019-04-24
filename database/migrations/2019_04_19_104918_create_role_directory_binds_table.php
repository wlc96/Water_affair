<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateRoleDirectoryBindsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_directory_binds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->comment('公司id');
            $table->integer('role_id')->comment('角色id');
            $table->integer('directory_id')->comment('目录id');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE role_directory_binds comment '角色目录绑定表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_directory_binds');
    }
}
