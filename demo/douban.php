<?php
/**
 * Created by PhpStorm.
 * User: frank
 * Date: 2018/11/6
 * Time: 15:30
 */
require_once __DIR__ . '/../autoloader.php';
use phpspider\core\phpspider;

/* Do NOT delete this comment */
/* 不要删除这段注释 */
//看了一下源代码，作者将这注释写到构造函数中了，所以尊重作者意愿，没有去掉，去掉会报错。

$configs = array(
    'name' => '豆瓣电影',
    'domains' => array(
        'movie.douban.com',
    ),
    'scan_urls' => array(
        'https://movie.douban.com/subject/26366496/',   //豆瓣电影地址
        'https://movie.douban.com/subject/26752088/',
        'https://movie.douban.com/subject/25921812/',
    ),
    'content_url_regexes' => array(
        "https://movie.douban.com/subject/\d"    //url模式
    ),

    //模拟客户端IP
    'client_ip' => array(
        '127.168.0.1',
        '127.168.0.3',
        '127.168.0.4',
        '127.168.0.5',
        '127.168.0.6',
    ),

    //模拟客户端设备
    //访问来源
    'user_agent' => array(
        "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36",
        "Mozilla/5.0 (iPhone; CPU iPhone OS 9_3_3 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13G34 Safari/601.1",
        "Mozilla/5.0 (Linux; U; Android 6.0.1;zh_cn; Le X820 Build/FEXCNFN5801507014S) AppleWebKit/537.36 (KHTML, like Gecko)Version/4.0 Chrome/49.0.0.0 Mobile Safari/537.36 EUI Browser/5.8.015S",
    ),

    'node' => [',','.',':',' ','*'],

    'fields' => array(
        array(
            // 电影标题
            'name' => "title",
            'selector' => "//*[@id='content']/h1/span[1]",   //文章内容区域class
            'required' => true
        ),
        array(
            //电影导演
            'name' => "casts",
            'selector' => "//*[@id='info']/span[1]/span[2]/a",   //文章内容区域class
            'required' => true
        ),
        array(
            // 电影简介
            'name' => "summary",
            'selector' => "//*[@id='link-report']/span[1]/text()",   //文章内容区域class
            'required' => true
        ),
        array(
            //
            'name'=>'year',
            'selector'=>"//*[@id='content']/h1/span[2]",   //文章题目区域class
            'required'=>true
        ),
    ),
    //脚本输出类型，输出到数据库
    'export'=>array(
        'type'=>'db',
        'table'=>'t_movies'  //存放文章的数据表
    ),
//    'export' => array(
//        'type' => 'csv',
//        'file' => './data/douban.csv', // data目录下
//    ),
    //数据库连接配置
    'db_config'=>array(
        'host'=>'127.0.0.1',
        'port'=>3306,
        'user'=>'root',  //连接数据库的用户名,根据自己环境定义
        'pass'=>'root',  //连接数据库的密码，根据自己环境定义
        'name'=>'demo'   //存放的数据库
    ),
    'log_show' => true,

);

$spider = new phpspider($configs);

//数据不符合要求，则不录入数据库
$spider->on_extract_page = function($page, $data)
{
    // 返回false不处理，当前页面的字段不入数据库直接过滤
    // 比如采集电影网站，标题匹配到“预告片”这三个字就过滤
    if ($data['title'] == '')
    {
        return false;
    }
};

//数据过滤处理
$spider->on_extract_field = function($fieldname, $data, $page)
{
    if($data){
        if ($fieldname == 'summary')
        {
            $data = trim($data);
        }
    }
    return $data;
};

$spider->start();

//$spider->on_start = function($spider)
//{
//    // add_sacn_url 没有URL去重机制，可用作增量更新
//    //$phpspider->add_scan_url("https://movie.douban.com/subject/");
//};
