<?php

namespace App\Http\Controllers\admin;

use App\repositories\repo_admin_user_oplog;
use App\repositories\repo_agent;
use App\repositories\repo_member_active_data;
use App\repositories\repo_member_increase_data;
use App\repositories\repo_member_online_data;
use App\repositories\repo_member_retention_data;
use App\repositories\repo_order_transfer;
use App\repositories\repo_user;
use App\services\serv_member_active_data;
use App\services\serv_member_increase_data;
use App\services\serv_member_retention_data;
use App\services\serv_util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ctl_report extends Controller
{
    private $repo_member_active_data;
    private $repo_member_increase_data;
    private $repo_member_retention_data;
    private $repo_member_online_data;
    private $repo_admin_user_oplog;
    private $repo_order_transfer;
    private $repo_user;
    private $repo_agent;
    private $serv_member_retention_data;
    private $serv_member_active_data;
    private $serv_member_increase_data;
    private $serv_util;
    private $today;

    public function __construct(
        repo_member_active_data $repo_member_active_data,
        repo_member_increase_data $repo_member_increase_data,
        repo_member_retention_data $repo_member_retention_data,
        repo_member_online_data $repo_member_online_data,
        repo_admin_user_oplog $repo_admin_user_oplog,
        repo_order_transfer $repo_order_transfer,
        repo_user $repo_user,
        repo_agent $repo_agent,
        serv_member_retention_data $serv_member_retention_data,
        serv_member_active_data $serv_member_active_data,
        serv_member_increase_data $serv_member_increase_data,
        serv_util $serv_util
    )
    {
        parent::__construct();
        $this->repo_member_active_data = $repo_member_active_data;
        $this->repo_member_increase_data = $repo_member_increase_data;
        $this->repo_member_retention_data = $repo_member_retention_data;
        $this->repo_member_online_data = $repo_member_online_data;
        $this->repo_admin_user_oplog = $repo_admin_user_oplog;
        $this->repo_order_transfer = $repo_order_transfer;
        $this->repo_user = $repo_user;
        $this->repo_agent = $repo_agent;
        $this->serv_member_retention_data = $serv_member_retention_data;
        $this->serv_member_active_data = $serv_member_active_data;
        $this->serv_member_increase_data = $serv_member_increase_data;
        $this->serv_util = $serv_util;
        $this->today = date('Y/m/d');
    }

    /**
     * ??????????????????????????????, ????????????????????????
     * @version 1.0.0
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function member_active_list(Request $request)
    {
        $agent_id   = $request->input('agent_id', '');
        $page_size  = get_action() == 'export_member_active' ? 100 :
            $request->input('page_size', $this->repo_member_active_data->page_size);
        $page       = $request->input('page', 1);
        $date_start = $request->input('date_start', '');
        $date_end   = $request->input('date_end', '');
        $date_start = empty($date_start) ? '2019/01/01' : $date_start;
        $date_end = empty($date_end) ? date('Y/m/d') : $date_end;

        //?????????????????????
        if ($page == 1)
        {
            $this->serv_member_active_data->generate_data(date('Y/m/d', strtotime('-30 day')));
        }

        $conds = [
            'agent_id'      => $agent_id,
            'date_start'    => $date_start,
            'date_end'      => $date_end,
            'page_size'     => $page_size, //????????????
            'page'          => $page, //?????????
            'order_by'      => ['date', 'desc'],
            'count'         => 1, //?????????????????????
        ];
        $rows = $this->repo_member_active_data->get_list($conds);
        $total_page = ceil($rows['total'] / $page_size);

        //??????????????????
        $agents = $this->repo_agent->lists([
            'fields'    => ['id', 'realname'],
            'index'     => 'id',
            'where'     => [
                ['id', '=', sql_in($rows['lists'], 'agent_id')],
        ]])->toArray();

        foreach($rows['lists'] as $k => $v) //???????????????
        {
            $row_plus = [
                'realname' => $agents[$v['agent_id']]['realname'] ?? '',
            ];
            $rows['lists'][$k] = array_merge($v, $row_plus);
        }

        if(get_action() == 'export_member_active')
        {
            $titles = [
                'realname'              => '??????',
                'member_active_count'   => '???????????????',
                'd1'                    => '??????',
                'd7'                    => '7???',
                'd30'                   => '30???',
            ];

            $status = $this->serv_util->export_data([
                'page_no'   => $page,
                'rows'      => $rows['lists'],
                'file'      => $request->input('file', ''),
                'fields'    => $request->input('fields', []), //??????????????????
                'titles'    => $titles, //????????????
                'total_page' => $total_page,
            ], $ret_data);
            if($status < 0)
            {
                return res_error($this->serv_util->get_err_msg($status), $status);
            }
            return res_success($ret_data);
        }
        return res_success($rows);
    }

    /**
     * ????????????????????????excel
     * @version 1.0.0
     * @param Request $request
     */
    public function export_member_active(Request $request)
    {
        return $this->member_active_list($request);
    }

    /**
     * ??????????????????????????????
     * @version 1.0.0
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function member_retention_list(Request $request)
    {
        $agent_id       = $request->input('agent_id', '');
        $page_size  = get_action() == 'export_member_retention' ? 100 :
            $request->input('page_size', $this->repo_member_retention_data->page_size);
        $page       = $request->input('page', 1);
        $date_start = $request->input('date_start', '');
        $date_end   = $request->input('date_end', '');
        $date_start = empty($date_start) ? '2019/01/01' : $date_start;
        $date_end = empty($date_end) ? date('Y/m/d') : $date_end;

        //?????????????????????
        if ($page == 1)
        {
            $this->serv_member_retention_data->generate_data(date('Y/m/d', strtotime('-30 day')));
        }

        $conds = [
            'agent_id'      => $agent_id,
            'date_start'    => $date_start,
            'date_end'      => $date_end,
            'page_size'     => $page_size, //????????????
            'page'          => $page, //?????????
            'order_by'      => ['date', 'desc'],
            'load'          => ['agent_maps'],
            'count'         => 1, //?????????????????????
        ];
        $rows = $this->repo_member_retention_data->get_list($conds);
        $total_page = ceil($rows['total'] / $page_size);

        if(get_action() == 'export_member_retention')
        {
            $titles = [
                'agent_maps.realname'   => '??????',
                'member_register_count' => '???????????????',
                'd1'                    => '??????',
                'd7'                    => '7???',
                'd30'                   => '30???',
            ];

            $status = $this->serv_util->export_data([
                'page_no'   => $page,
                'rows'      => $rows['lists'],
                'file'      => $request->input('file', ''),
                'fields'    => $request->input('fields', []), //??????????????????
                'titles'    => $titles, //????????????
                'total_page' => $total_page,
            ], $ret_data);
            if($status < 0)
            {
                return res_error($this->serv_util->get_err_msg($status), $status);
            }
            return res_success($ret_data);
        }

        return res_success($rows);
    }

    /**
     * ????????????????????????excel
     * @version 1.0.0
     * @param Request $request
     */
    public function export_member_retention(Request $request)
    {
        return $this->member_retention_list($request);
    }

    /**
     * ??????????????????????????????
     * @version 1.0.0
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function member_increase_list(Request $request)
    {
        $agent_id       = $request->input('agent_id', '');
        $page_size  = $request->input('page_size', $this->repo_member_increase_data->page_size);
        $page       = $request->input('page', 1);
        $date_start = $request->input('date_start', '');
        $date_end   = $request->input('date_end', '');
        $date_start = empty($date_start) ? '2019/01/01' : $date_start;
        $date_end = empty($date_end) ? date('Y/m/d') : $date_end;

        //??????????????????
        if ($page == 1)
        {
            $this->serv_member_increase_data->generate_data($this->today);
        }

        $conds = [
            'fields'    => [
                'date',
                'agent_id',
                DB::raw('SUM(member_count) AS member_count'),
                DB::raw('SUM(member_increase_count) AS member_increase_count'),
            ],
            'agent_id'      => $agent_id,
            'date_start'    => $date_start,
            'date_end'      => $date_end,
            'page_size'     => $page_size, //????????????
            'page'          => $page, //?????????
            'group_by'      => ['date', 'agent_id'],
            'order_by'      => ['date', 'desc'],
            'load'          => ['agent_maps'],
            'count'         => 1, //?????????????????????
        ];
        $rows = $this->repo_member_increase_data->get_list($conds);
        return res_success($rows);
    }

    /**
     * ??????????????????????????????
     * @version 1.0.0
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function member_online_list(Request $request)
    {
        $agent_id       = $request->input('agent_id', '');
        $page_size  = get_action() == 'export_member_online' ? 100 :
            $request->input('page_size', $this->repo_member_online_data->page_size);
        $page       = $request->input('page', 1);
        $date_start = $request->input('date_start', '');
        $date_end   = $request->input('date_end', '');
        $date_start = empty($date_start) ? '2019/01/01' : $date_start;
        $date_end = empty($date_end) ? date('Y/m/d') : $date_end;

        //????????????????????????????????????????????????
        $sub_query = DB::table('member_online_data')
            ->select(
                'agent_id',
                DB::raw("SUM(IF(game1 = 1 OR game2 = 1, member_online_count, 0)) AS member_online_count"),
                DB::raw("DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(create_time, '%Y/%m/%d %H:00'), '+0:00', '+8:00'), '%Y/%m/%d') AS date"),
                DB::raw("DATE_FORMAT(CONVERT_TZ(FROM_UNIXTIME(create_time, '%Y/%m/%d %H:00'), '+0:00', '+8:00'), '%H:00') AS hour"),
        )->groupBy('agent_id', 'create_time');

        //??????????????????
        $query = DB::table(DB::raw("({$sub_query->toSql()}) AS sub"))
            ->select(
                DB::raw('date'),
                DB::raw('agent_id'),
                DB::raw('MAX(IF(HOUR(hour) = 0, member_online_count, 0)) AS h0'),
                DB::raw('MAX(IF(HOUR(hour) = 1, member_online_count, 0)) AS h1'),
                DB::raw('MAX(IF(HOUR(hour) = 2, member_online_count, 0)) AS h2'),
                DB::raw('MAX(IF(HOUR(hour) = 3, member_online_count, 0)) AS h3'),
                DB::raw('MAX(IF(HOUR(hour) = 4, member_online_count, 0)) AS h4'),
                DB::raw('MAX(IF(HOUR(hour) = 5, member_online_count, 0)) AS h5'),
                DB::raw('MAX(IF(HOUR(hour) = 6, member_online_count, 0)) AS h6'),
                DB::raw('MAX(IF(HOUR(hour) = 7, member_online_count, 0)) AS h7'),
                DB::raw('MAX(IF(HOUR(hour) = 8, member_online_count, 0)) AS h8'),
                DB::raw('MAX(IF(HOUR(hour) = 9, member_online_count, 0)) AS h9'),
                DB::raw('MAX(IF(HOUR(hour) = 10, member_online_count, 0)) AS h10'),
                DB::raw('MAX(IF(HOUR(hour) = 11, member_online_count, 0)) AS h11'),
                DB::raw('MAX(IF(HOUR(hour) = 12, member_online_count, 0)) AS h12'),
                DB::raw('MAX(IF(HOUR(hour) = 13, member_online_count, 0)) AS h13'),
                DB::raw('MAX(IF(HOUR(hour) = 14, member_online_count, 0)) AS h14'),
                DB::raw('MAX(IF(HOUR(hour) = 15, member_online_count, 0)) AS h15'),
                DB::raw('MAX(IF(HOUR(hour) = 16, member_online_count, 0)) AS h16'),
                DB::raw('MAX(IF(HOUR(hour) = 17, member_online_count, 0)) AS h17'),
                DB::raw('MAX(IF(HOUR(hour) = 18, member_online_count, 0)) AS h18'),
                DB::raw('MAX(IF(HOUR(hour) = 19, member_online_count, 0)) AS h19'),
                DB::raw('MAX(IF(HOUR(hour) = 20, member_online_count, 0)) AS h20'),
                DB::raw('MAX(IF(HOUR(hour) = 21, member_online_count, 0)) AS h21'),
                DB::raw('MAX(IF(HOUR(hour) = 22, member_online_count, 0)) AS h22'),
                DB::raw('MAX(IF(HOUR(hour) = 23, member_online_count, 0)) AS h23'),
                )
            ->groupBy('date', 'agent_id')
            ->orderBy('date', 'desc');
        $query->get();

        //??????
        $agent_id and $query->where('agent_id', '=', $agent_id);
        $date_start and $query->where('date', '>=', $date_start);
        $date_end and $query->where('date', '<=', $date_end);
        //??????
        $page   = max(1, ($page ? $page : 1));
        $offset = ($page - 1) * $page_size;
        $query->limit($page_size)->offset($offset);
        //??????????????????
        $query->mergeBindings($sub_query);
        //?????????
        $count = $query->get()->count();
        $member_online_data = $query->get()->toArray();
        $member_online_data = json_decode(json_encode($member_online_data),true); //stdClass?????????
        $rows = [
            'total' => $count,
            'lists' => $member_online_data,
        ];
        $total_page = ceil($rows['total'] / $page_size);

        //??????????????????
        $agents = $this->repo_agent->lists([
            'fields'    => ['id', 'realname'],
            'index'     => 'id',
            'where'     => [
                ['id', '=', sql_in($rows['lists'], 'agent_id')],
            ]])->toArray();

        foreach($rows['lists'] as $k => $v) //???????????????
        {
            $row_plus = [
                'realname' => $agents[$v['agent_id']]['realname'] ?? '',
            ];
            $rows['lists'][$k] = array_merge($v, $row_plus);
        }

        if(get_action() == 'export_member_online')
        {
            $titles = [
                'realname'  => '??????',
                'date'      => '??????',
                'h0'        => '00:00',
                'h1'        => '01:00',
                'h2'        => '02:00',
                'h3'        => '03:00',
                'h4'        => '04:00',
                'h5'        => '05:00',
                'h6'        => '06:00',
                'h7'        => '07:00',
                'h8'        => '08:00',
                'h9'        => '09:00',
                'h10'        => '10:00',
                'h11'        => '11:00',
                'h12'        => '12:00',
                'h13'        => '13:00',
                'h14'        => '14:00',
                'h15'        => '15:00',
                'h16'        => '16:00',
                'h17'        => '17:00',
                'h18'        => '18:00',
                'h19'        => '19:00',
                'h20'        => '20:00',
                'h21'        => '21:00',
                'h22'        => '22:00',
                'h23'        => '23:00',
            ];

            $status = $this->serv_util->export_data([
                'page_no'   => $page,
                'rows'      => $rows['lists'],
                'file'      => $request->input('file', ''),
                'fields'    => $request->input('fields', []), //??????????????????
                'titles'    => $titles, //????????????
                'total_page' => $total_page,
            ], $ret_data);
            if($status < 0)
            {
                return res_error($this->serv_util->get_err_msg($status), $status);
            }
            return res_success($ret_data);
        }

        return res_success($rows);
    }

    /**
     * ????????????????????????excel
     * @version 1.0.0
     * @param Request $request
     */
    public function export_member_online(Request $request)
    {
        return $this->member_online_list($request);
    }
}
