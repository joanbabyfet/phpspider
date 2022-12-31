<?php

namespace App\Http\Controllers\admin;

use App\repositories\repo_admin_user;
use App\repositories\repo_api_req_log;
use App\repositories\repo_app_key;
use App\repositories\repo_book_detail;
use App\repositories\repo_config;
use App\repositories\repo_example;
use App\repositories\repo_member_increase_data;
use App\repositories\repo_order_transfer;
use App\repositories\repo_user;
use App\services\serv_display;
use App\services\serv_redis;
use App\services\serv_req;
use App\services\serv_sys_mail;
use App\services\serv_sys_sms;
use App\services\serv_util;
use App\services\serv_wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use QL\QueryList;


/**
 * 测试用控制器
 * Class ctl_test
 * @package App\Http\Controllers\admin
 */
class ctl_test extends Controller
{
    private $repo_config;
    private $serv_req;
    private $serv_sys_mail;
    private $serv_sys_sms;
    private $repo_admin_user;
    private $repo_member_increase_data;
    private $serv_util;
    private $repo_app_key;
    private $serv_wallet;
    private $serv_display;
    private $repo_order_transfer;
    private $repo_user;
    private $serv_redis;
    private $repo_example;
    private $repo_api_req_log;
    private $repo_book_detail;

    public function __construct(
        repo_config $repo_config,
        serv_req $serv_req,
        serv_sys_mail $serv_sys_mail,
        serv_sys_sms $serv_sys_sms,
        repo_admin_user $repo_admin_user,
        repo_member_increase_data $repo_member_increase_data,
        serv_util $serv_util,
        repo_app_key $repo_app_key,
        serv_wallet $serv_wallet,
        serv_display $serv_display,
        repo_order_transfer $repo_order_transfer,
        repo_user $repo_user,
        serv_redis $serv_redis,
        repo_example $repo_example,
        repo_api_req_log $repo_api_req_log,
        repo_book_detail $repo_book_detail
    )
    {
        parent::__construct();
        $this->repo_config = $repo_config;
        $this->serv_req = $serv_req;
        $this->serv_sys_mail = $serv_sys_mail;
        $this->serv_sys_sms = $serv_sys_sms;
        $this->repo_admin_user = $repo_admin_user;
        $this->repo_member_increase_data = $repo_member_increase_data;
        $this->serv_util = $serv_util;
        $this->repo_app_key = $repo_app_key;
        $this->serv_wallet = $serv_wallet;
        $this->serv_display = $serv_display;
        $this->repo_order_transfer = $repo_order_transfer;
        $this->repo_user = $repo_user;
        $this->serv_redis = $serv_redis;
        $this->repo_example = $repo_example;
        $this->repo_api_req_log = $repo_api_req_log;
        $this->repo_book_detail = $repo_book_detail;
    }

    public function index(Request $request)
    {
        //多线程扩展
//        QueryList::run('Multi', [
//            'list' => [ //待DOM解析链接集合
//                'http://www.999wx.cc/kan/545/32852786/',
//                'http://www.999wx.cc/kan/545/32841149/',
//                'http://www.999wx.cc/kan/545/28585199/'
//            ],
//            'curl' => [
//                'opt' => [
//                    CURLOPT_SSL_VERIFYPEER => false,
//                    CURLOPT_SSL_VERIFYHOST => false,
//                    CURLOPT_FOLLOWLOCATION => true,
//                    CURLOPT_AUTOREFERER => true,
//                ],
//                //设置线程数
//                'maxThread' => 100,
//                //设置最大尝试数
//                'maxTry' => 3
//            ],
//            'start' => true, //不自动开始线程，默认自动开始
//            'success' => function($a) { //每个任务成功调用此回调
//                $rules = [ //DOM解析规则
//                    'content' => ['.content', 'html']
//                ];
//                $range = '';
//                $res = QueryList::Query($a['content'], $rules, $range)->getData();
//                $res = array_shift($res); //将第1个数组元素弹出
//                $content = empty($res) ? '' : strip_tags($res['content']);
//                pr($content);
//            },
//            'error' => function(){ //出错处理
//            }
//        ]);

        //昵图网
//        $data = $this->serv_util->collect([
//            'url'   => 'http://www.nipic.com',
//            'rules' => [
//                'title'  => [
//                    'title', 'text'
//                ],
//                'keywords'  => [
//                    'meta[name=keywords]', 'content'
//                ],
//                'description'  => [
//                    'meta[name=description]', 'content'
//                ],
//                'sogou_site_verification'  => [
//                    'meta[name=sogou_site_verification]', 'content'
//                ],
////                'img'       => [
////                    '.paddingLay1 img', 'src'
////                ]
//            ],
//            'range' => '',
//        ]);

        //淘宝, 输出编码:UTF-8,输入编码:自动识别
//        $data = QueryList::Query('https://top.etao.com', [
//            'link'  => [
//                'a', 'text'
//            ]
//        ], '', 'UTF-8', null)->getData();
//        pr($data);

        //上传本地图片到s3
//        $thumb = '031/4bdea26dc9d8118bc38a766dee8b32b4.jpg';
//        $upload_dir = storage_path('app/public/').'image/';
//        $content = file_get_contents($upload_dir.$thumb); //跟本地拿图片
//        Storage::disk('s3')->put($thumb, $content);

        //获取图片从s3
//        $thumb = '031/4bdea26dc9d8118bc38a766dee8b32b4.jpg';
//        $content = Storage::disk('s3')->get($thumb);
//        pr($content);

        //获取小说内容
//        $content = $this->repo_book_detail->get_content(3, 6);
//        pr($content);

        //干掉本地图片
        $thumb = '001/4378e0f5d8de8c227b85f7c3853e5235.jpg';
        $upload_dir = "public/image/"; //这里不能用绝对路径
        Storage::disk('local')->delete($upload_dir.$thumb);
    }
}
