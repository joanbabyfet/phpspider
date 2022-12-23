<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateBookContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('book_content', function (Blueprint $table) {
            $table->integer('zhangjie_id', false, true)->default(0)->nullable()->comment('章节id');
            $table->longText('content')->nullable()->comment('章节内容');
            $table->primary(['zhangjie_id']);
        });
        $table = DB::getTablePrefix().'book_content';
        DB::statement("ALTER TABLE `{$table}` comment'小说章节內容'"); // 表注释
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_content');
    }
}
