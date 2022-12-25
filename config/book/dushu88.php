<?php
//采集规则配置
return [
    'base_url'  => 'https://www.88dushu.club/',
    'charset'   => 'UTF-8', //输入编码格式(暂未使用)
    'category' => [
        1 => 1,
        2 => 2,
        3 => 9,
        4 => 5,
        5 => 8,
    ],
    'list'          => [    //列表页
        'range'     => '#newscontent .l ul li',  //区域选择器
        'rules'     => [    //规则
            'title'         => [
                '.s2 a', 'text'
            ],
            'from_url'    => [
                '.s2 a', 'href'
            ],
            'author'        => [
                '.s4 a', 'text'
            ],
            'update_time'   => [
                '.s5', 'text'
            ],
            'zhangjie'      => [
                '.s3 a', 'text'
            ]
        ],
        'page_size' => 20,  //每页展示几条
        'page_url'  => 'books/%d_%d.html'
    ],
    'detail'        => [    //详情页
        'rules'     => [    //规则
            'introduce'         => [
                '#intro p', 'text'
            ],
            'thumb'        => [
                '#fmimg img', 'src'
            ]
        ],
    ],
    'zhangjie_list' => [    //章节列表页
        'range'     => '#list dl dd',  //区域选择器
        'rules'     => [    //规则
            'title'         => [
                'a', 'text'
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
                '#chapter-content .chapter-line', 'text'
            ],
        ],
    ]
];
