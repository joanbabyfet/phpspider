<?php
return [
    'base_url'  => 'http://www.88dushu.com/',
    'charset'   => 'gb2312',
    'categorys' => [
        1 => 7,
        2 => 13,
        3 => 17,
        4 => 19,
        5 => 15,
        6 => 25,
        7 => 16,
        8 => 76,
        9 => 18,
    ],
    'list'          => [    //列表页
        'range'     => '',  //区域选择器
        'rules'     => [    //规则
            'title'         => [],
            'source_url'    => [],
            'author'        => [],
            'word_count'    => [],
            'update_time'   => [],
            'zhangjie'      => []
        ],
        'page_size' => 50,  //每页展示几条
        'page_url'  => ''
    ],
    'zhangjie_list' => [    //章节列表页
        'range'     => '',  //区域选择器
        'rules'     => [    //规则
            'title'         => [],
            'source_url'    => [],
        ],
        'page_url'  => '',
    ],
    'content'   => [        //详情页
        'range'     => '',  //区域选择器
        'rules'     => [    //规则
            'content'       => [], //过滤div和p标签
        ],
    ]
];
