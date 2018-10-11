<?php
/**
 * Created by PhpStorm.
 * User: xin
 * Date: 2018/10/11
 * Time: 上午11:37
 */

namespace app\api\controller\v1;

use app\api\controller\Api;
use app\api\model\BookClassify;
use lib\Tools;
use think\Config;
use think\Db;
use think\Request;

class Book extends Api {

    protected static $rule_bookList = [
        'id_str'     =>  'require',
        'num'     =>  'require|number',
    ];
    protected static $rule_bookInfo = [
        'bk_id'     =>  'require|number',
    ];
    //电子数分类请求
    public function bookClassify(){
        $lang=$this->request->param("lang")? $this->request->param("lang"):'zh_CN';
        $bookClassify=new BookClassify();
        $results=$bookClassify->where('lang',$lang)->select()->toArray();

        $infos=Tools::make_tree($results);

        return $this->sendSuccess($infos);
    }
    //电子数分类请求
    public function bookList(){
        $this->checkParam(self::$rule_bookList);
        $lang=$this->request->param("lang")? $this->request->param("lang"):'zh_CN';
        $id_str=$this->request->param("id_str");
        $page=(int)$this->request->param("num")?(int)$this->request->param("num"):1;
        $is_arr=explode(',',$id_str);
        $results=Db::table('p_book book')
                    ->field('book.bk_id,book.bk_name,book.bk_url,book.bk_cover,book.bk_author,book.bk_description,book.bk_star,book.create_time,book.bk_name_format,book.bk_lang')
                    ->join('p_book_classify_middle middle','book.bk_id=middle.book_id')
                    ->where('middle.class_id','in',$is_arr)
                    ->where('book.lang',$lang)
                    ->limit(Config::get('per_page')*($page-1),Config::get('per_page'))
                    ->select();
        return $this->sendSuccess($results);
    }

    //电子书详情
    public function bookInfo(){
        $this->checkParam(self::$rule_bookInfo);
        $id=$this->request->param("bk_id");
        $book=new \app\api\model\Book();
        $results=$book->where('bk_id',$id)->select()->toArray();
        return $this->sendSuccess($results);
    }
}