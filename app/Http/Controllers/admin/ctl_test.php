<?php

namespace App\Http\Controllers\admin;

use App\repositories\repo_admin_user;
use App\repositories\repo_api_req_log;
use App\repositories\repo_app_key;
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
        repo_api_req_log $repo_api_req_log
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
    }

    public function index(Request $request)
    {

    }
}
