<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('steps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->comment('公司id');
            $table->string('name',20)->comment('步骤名称');
            $table->integer('procedures_id')->comment('所属流程id');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE steps comment '流程步骤表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('steps');
    }
}
