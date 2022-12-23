<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTagRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tag_record', function (Blueprint $table) {
            $table->Increments('id');
            $table->integer('article_id')->default(0)->nullable()->comment('文章id');
            $table->integer('tag_id')->default(0)->nullable()->comment('标签id');
        });
        $table = DB::getTablePrefix().'tag_record';
        DB::statement("ALTER TABLE `{$table}` comment'标签文章映射'"); // 表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tag_record');
    }
}
