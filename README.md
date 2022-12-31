## About
基于laravel的爬虫框架，使用 QueryList3 采集指定站点“999小说”，支持页面深度采集与JavaScript动态渲染页面爬取

## Feature

* 爬取小说, 网站以 http://www.999wx.cc 为例
* 引用 QueryList3 实现php选择html DOM
* 通过redis异步队列, 爬取小说内容与下载图片
* 存储小说内容为txt文件并入库
* 支持采集JavaScript动态渲染页面
* 更新策略支持指定分类/指定范围/指定文章/修复空白数据
* 写入log日志能查看爬取任务状态
* 小说管理模块
* 支持转换简繁体小说内容
* 支持图片上传到S3云存储服务

## Requires
PHP 7.4 or Higher  
Redis
MongoDB 3.2 or Higher

## Install
```
composer install
cp .env.example .env
php artisan app:install
php artisan storage:link
php artisan jwt:secret
```

## Usage
```
# Login Admin
username: admin
password: Bb123456
```

## Change Log
v1.0.0

## Maintainers
Alan

## LICENSE
[MIT License](https://github.com/joanbabyfet/phpspider/blob/master/LICENSE)
