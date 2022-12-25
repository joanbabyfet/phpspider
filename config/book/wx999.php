<?php
//采集规则配置
return [
    'base_url'  => 'http://www.999wx.cc/',
    'charset'   => 'UTF-8', //输入编码格式(暂未使用)
    'category' => [
        1 => 'xuanhuan',
        2 => 'xianxia',
        3 => 'dushi',
        4 => 'lishi',
        5 => 'kongbu',
        6 => 'youxi',
        7 => 'kehuan',
        8 => 'qita',
    ],
    'list'          => [    //列表页
        'range'     => '.store_left ul li',  //区域选择器
        'rules'     => [    //规则
            'title'         => [
                '.w100 h2', 'text'
            ],
            'from_url'    => [
                '.w100 a', 'href'
            ],
            'author'        => [
                '.w100 .li_bottom i', 'text'
            ],
            'update_time'   => [
                '.w100 .li_bottom .blue', 'text'
            ],
        ],
        'page_size' => 30,  //该站每页展示几条, 以该站为基准
        'page_url'  => 'fenlei/%s/%d/' //分页地址
    ],
    'detail'        => [    //详情页
        'rules'     => [    //规则
            'introduce'         => [
                '.intro', 'text'
            ],
            'thumb'        => [
                '.novel_info_main img', 'src'
            ]
        ],
    ],
    'zhangjie_list' => [    //章节列表页
        'range'     => '#ul_all_chapters li',  //区域选择器
        'rules'     => [    //规则
            'title'         => [
                '', 'text'
            ],
            'from_url'    => [
                'a', 'href'
            ],
        ],
    ],
    'zhangjie_detail'   => [        //章节详情页
        'range'     => '',  //区域选择器
        'rules'     => [    //规则
            'content'       => [ //过滤div和p标签
                '.content', 'html'
            ],
        ],
    ]
];
