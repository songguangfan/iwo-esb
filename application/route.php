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

Route::rule(':version/music/musicClassify','api/:version.Music/musicClassify','get');//音乐分类请求
Route::rule(':version/music/musicList','api/:version.Music/musicList','get');//音乐分类请求
Route::rule(':version/music/musicInfo','api/:version.Music/musicInfo','get');//音乐专辑详情
Route::rule(':version/music/songSheet','api/:version.Music/songSheet','get');//歌单类别
Route::rule(':version/music/songSheet','api/:version.Music/save','post');//歌单类别
Route::rule(':version/music/songSheet','api/:version.Music/delete','delete');//歌单类别
Route::rule(':version/music/songList','api/:version.Music/songList','get');//歌曲列表
Route::rule(':version/music/songList','api/:version.Music/songAdd','post');//歌曲添加
Route::rule(':version/music/songList','api/:version.Music/songDelete','delete');//歌曲删除

Route::rule(':version/movie/movieClassify','api/:version.Movie/movieClassify','get');//电影分类请求
Route::rule(':version/movie/movieInfo','api/:version.Movie/movieInfo','get');//影视详情接口
Route::rule(':version/movie/scanFile','api/:version.Movie/scanFile','get');//影视播放切图路径接口
Route::rule(':version/movie/movieList','api/:version.Movie/movieList','get');//影视列表接口

Route::rule(':version/sync/wsSeatInfo','api/:version.Sync/wsSeatInfo','get');//座位信息
Route::rule(':version/sync/wsIdentitySeatInfo','api/:version.Sync/wsIdentitySeatInfo','get');//乘客信息
Route::rule(':version/sync/wsDestinationInfo','api/:version.Sync/wsDestinationInfo','get');//乘客信息
Route::rule(':version/sync/wsTransferInfo','api/:version.Sync/wsTransferInfo','get');//中转信息
Route::rule(':version/sync/wsBaggageInfo','api/:version.Sync/wsBaggageInfo','get');//中转信息
Route::rule(':version/sync/wsBoradingGateInfo','api/:version.Sync/wsBoradingGateInfo','get');//中转信息
Route::miss('Error/index');
return [
    '__pattern__' => [
        'name' => '\w+',
    ],   
];
