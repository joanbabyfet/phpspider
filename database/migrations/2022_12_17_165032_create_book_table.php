<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateBookTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book', function (Blueprint $table) {
            $table->Increments('id');
            $table->integer('cat_id')->default(0)->nullable()->comment('分类id');
            $table->string('title',255)->default('')->nullable()->comment('标题');
            $table->string('introduce',500)->default('')->nullable()->comment('简介');
            $table->string('thumb',100)->default('')->nullable()->comment('标题图片');
            $table->string('zhangjie',50)->default('')->nullable()->comment('章节');
            $table->string('author',50)->default('')->nullable()->comment('作者');
            $table->integer('word_count')->default(0)->nullable()->comment('字数');
            $table->tinyInteger("level")->default(0)->nullable()->comment('等级');
            $table->integer('follow')->default(0)->nullable()->comment('关注人数');
            $table->integer('hit')->default(0)->nullable()->comment('浏览次数');
            $table->string('source',50)->default('')->nullable()->comment('来源');
            $table->string('from_url',100)->default('')->nullable()->comment('来源地址');
            $table->string('from_hash',50)->default('')->nullable()->comment('来源地址hash,用来判断是否插入过');
            $table->tinyInteger("status")->default(1)->nullable()->comment('状态：0=禁用 1=启用');
            $table->integer('create_time')->default(0)->nullable()->comment("創建時間");
            $table->char('create_user', 32)->default('0')->nullable()->comment("創建人");
            $table->integer('update_time')->default(0)->nullable()->comment("修改時間");
            $table->char('update_user', 32)->default('0')->nullable()->comment("修改人");
            $table->integer('delete_time')->default(0)->nullable()->comment("刪除時間");
            $table->char('delete_user', 32)->default('0')->nullable()->comment("刪除人");
            $table->index('title');
            $table->index('author');
            $table->index('source');
            $table->index('create_time');
            $table->unique(['from_hash']);
        });
        $table = DB::getTablePrefix().'book';
        DB::statement("ALTER TABLE `{$table}` comment'小说'"); // 表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book');
    }
}
