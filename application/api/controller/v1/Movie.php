<?php
/**
 * Created by PhpStorm.
 * User: xin
 * Date: 2018/10/12
 * Time: 下午5:16
 */
namespace app\api\controller\v1;

use app\api\controller\Api;
use app\api\model\movieClassify;
use lib\Tools;
use think\console\command\optimize\Config;
use think\Db;

class Movie extends Api {

    protected static $rule_movieList = [
        'id_str' => 'require',
        'num' => 'require|number',
    ];
    protected static $rule_movieInfo = [
        'mv_id' => 'require|number',
    ];

    protected static $rule_scanfilepath = [
        'scanfilepath' => 'require',
    ];

    public function movieClassify(){
        $lang=$this->request->param("lang") ? $this->request->param("lang") : 'zh_CN';
        $movieClassify=new movieClassify();

        $results=$movieClassify->where('lang', $lang)->select()->toArray();

        $infos=Tools::make_tree($results);
        return $this->sendSuccess($infos);
    }

    public function movieInfo(){
        $this->checkParam(self::$rule_movieInfo);
        $id=$this->request->param("mv_id");
        $movie=new \app\api\model\Movie();
        $results=$movie->where('mv_id', $id)->select()->toArray();
        return $this->sendSuccess($results);
    }

    public function scanFile()
    {
        $this->checkParam(self::$rule_scanfilepath);
        $path = \think\Config::get('MOVIE_PREVIEW_PATH') . $this->request->param('path');
        $files = scandir($path);
        $result=array();
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                if (is_dir($path . '/' . $file)) {
                    scandir($path . '/' . $file);
                } else {
                    if (strstr(basename($file), '.png')) {
                        $result[] = basename($file);
                    }
                }
            }
        }
        return $this->sendSuccess($result);
    }

    public function movieList() {
        $this->checkParam(self::$rule_movieList);
        $id_str=$this->request->param("id_str");
        $page=$this->request->param('num') ? $this->request->param('num') :1;
        $is_addr=explode(',' , $id_str);
        $result=Db::table('p_movie movie')
            ->field('movie.mv_id, movie.mv_name,movie.mv_cover,movie.mv_star,movie.mv_director,movie.mv_performer,movie.mv_description')
            ->join('p_movie_classify_middle middle', 'movie.mv_id=middle.mv_id')
            ->join('p_movie_url url', 'movie.mv_id=url.mv_id')
            ->where('middle.class_id', 'in', $is_addr)
            ->limit(\think\Config::get('per_page') * ($page - 1), \think\Config::get('per_page'))
            ->select();
        return $this->sendSuccess($result);
    }
}