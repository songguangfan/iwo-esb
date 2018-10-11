<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;

//Route::resource(':version/demo','api/:version.Demo');   //注册jwt演示Demo
Route::rule(':version/demo/encode','api/:version.Demo/encode');
Route::rule(':version/demo/decode','api/:version.Demo/decode');
Route::rule(':version/book/bookClassify','api/:version.Book/bookClassify','get');//电子数分类请求
Route::rule(':version/book/bookList','api/:version.Book/bookList','get');//电子书列表条件查询
Route::rule(':version/book/bookInfo','api/:version.Book/bookInfo','get');//电子书详情
Route::miss('Error/index');
return [
    '__pattern__' => [
        'name' => '\w+',
    ],   
];
