<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_brands', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->comment('品牌名称');
            $table->string('name_py', 100)->comment('品牌拼音')->default('')->nullable();
            $table->string('image')->comment('品牌图片');
            $table->tinyInteger('is_recommend')->default(1)->comment('是否推荐 1:否、2：是');
            $table->unsignedSmallInteger('sort')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('car_brands');
    }
}
