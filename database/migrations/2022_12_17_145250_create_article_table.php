<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateArticleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article', function (Blueprint $table) {
            $table->char('id', 32)->default('');
            $table->integer('cat_id')->default(0)->nullable()->comment('分类id');
            $table->char('uid',32)->default('')->nullable()->comment('发布人id');
            $table->string('title',255)->default('')->nullable()->comment('标题');
            $table->string('introduce',255)->default('')->nullable()->comment('简介');
            $table->string('tag',255)->default('')->nullable()->comment('标签');
            $table->longText('content')->nullable()->comment('内容');
            $table->string('thumb',100)->default('')->nullable()->comment('标题图片');
            $table->integer('comment')->default(0)->nullable()->comment('评论数量');
            $table->integer('zan')->default(0)->nullable()->comment('点赞数');
            $table->integer('hit')->default(0)->nullable()->comment('浏览次数');
            $table->tinyInteger("status")->default(1)->nullable()->comment('状态：0=禁用 1=启用');
            $table->integer('create_time')->default(0)->nullable()->comment("創建時間");
            $table->char('create_user', 32)->default('0')->nullable()->comment("創建人");
            $table->integer('update_time')->default(0)->nullable()->comment("修改時間");
            $table->char('update_user', 32)->default('0')->nullable()->comment("修改人");
            $table->integer('delete_time')->default(0)->nullable()->comment("刪除時間");
            $table->char('delete_user', 32)->default('0')->nullable()->comment("刪除人");
            $table->primary(['id']);
            $table->index('uid');
            $table->index('title');
            $table->index('tag');
            $table->index('create_time');
        });
        $table = DB::getTablePrefix().'article';
        DB::statement("ALTER TABLE `{$table}` comment'文章'"); // 表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article');
    }
}
