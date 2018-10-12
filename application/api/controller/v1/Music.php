<?php
/**
 * Created by PhpStorm.
 * User: xin
 * Date: 2018/10/11
 * Time: 下午5:33
 */
namespace app\api\controller\v1;

use app\api\controller\Api;
use app\api\model\MusicClassify;
use app\api\model\Sheet;
use app\api\model\SheetSong;
use app\api\model\Song;
use lib\Tools;
use think\Config;
use think\console\command\make\Model;
use think\Db;

class Music extends Api {

    public static  $rule_MusicList=[
        'id_str'     =>  'require',
        'num'     =>  'require|number',
    ];

    public static $rule_MusicInfo=[
        'msid'  =>'require|number',
    ];
    public static $rule_SongSheet=[
        'seat_no'=>'require',
    ];

    public static $rule_AddSheet=[
        'sheet_name'=>'require',
        'seat'=>'require',
        'status'=>'require|number',
    ];

    public static $rule_SheetID=[
        'sheet_id'=>'require|number',
    ];

    public static $rule_AddSong=[
        'sheet_id'=>"require|number",
        'song_id'=>'require|number',
    ];

    public static $rule_DeleteSong=[
        'sheet_song_id'=>'require|number'
    ];
    //音乐分类请求
    public function musicClassify(){

        $lang=$this->request->param("lang")? $this->request->param("lang"):'zh_CN';
        $classify=new MusicClassify();

        $results=$classify->where('lang',$lang)->select()->toArray();

        $info=Tools::make_tree($results);
        return $this->sendSuccess($info);
    }

    //专辑列表接口
    public function musicList(){
        $this->checkParam(self::$rule_MusicList);
        $lang=$this->request->param("lang")? $this->request->param("lang"):'zh_CN';
        $id_str=$this->request->param("id_str");
        $page=(int)$this->request->param("num")?(int)$this->request->param("num"):1;

        $is_arr=explode(',',$id_str);
        $results=Db::table('p_music music')
            ->field('music.ms_id,music.ms_name,music.ms_cover,music.ms_publish,music.ms_description,music.ms_star,music.ms_author,music.create_time,music.ms_deleted,music.ms_lang,music.ms_name_format')
            ->join('p_music_classify_middle middle','music.ms_id=middle.ms_id')
            ->where('middle.class_id','in',$is_arr)
            ->where('music.ms_lang',$lang)
            ->limit(Config::get('per_page')*($page-1),Config::get('per_page'))
            ->select();
        return $this->sendSuccess($results);
}

    //专辑详情
    public function musicInfo(){
        $this->checkParam(self::$rule_MusicInfo);
        $id=$this->request->param("msid");
        $model=new \app\api\model\Music();
        $music=$model->where('ms_id',$id)->find()->toArray();
        $song_model=new Song();
        $songs=$song_model->field('song_id,song_name,song_url,mu_id,sort_,song_name_format')->where('mu_id',$id)->select()->toArray();
        $music['song’']=$songs;
        return $this->sendSuccess($music);
    }

    //获取歌单列表
    public function songSheet(){
        $this->checkParam(self::$rule_SongSheet);
        $seat=$this->request->param('seat_no');
        $result=Db::table('p_sheet')->where('seat_no',$seat)->select();
        return $this->sendSuccess($result);
    }

    //添加歌单
    public function save(){
        $this->checkParam(self::$rule_AddSheet);
        $model=new Sheet();

        $data=[
            'seat_no'=>$this->request->post('seat'),
            'sheet_name'=>$this->request->post('sheet_name'),
            'create_time'=>date("Y-m-d H:i:s"),
            'status'=>(int)$this->request->post('status'),
        ];
        $model->data($data)->save();
        if ($model->sheet_id){
            return $this->sendSuccess();
        }
        return $this->sendError(400,'保存失败');
    }

    //删除歌单
    public function delete(){
        $this->checkParam(self::$rule_SheetID);
        $sheet_id=$this->request->param("sheet_id");
        $sheet=new Sheet();
        $result=$sheet->where('sheet_id',$sheet_id)->delete();
        if ($result){
            return $this->sendSuccess();
        }
        return $this->sendError(400,'删除失败');
    }

    //获取歌单歌曲列表
    public function songList(){
        $this->checkParam(self::$rule_SheetID);
        //$lang=$this->request->param("lang")? $this->request->param("lang"):'zh_CN';
        $sheet_id=$this->request->param("sheet_id");
        $results=Db::table('p_sheet_song sheet')
            ->field('song.song_id,song.song_name,song.song_url,song.mu_id,song.song_duration,song.song_deleted,song.sort_,song.song_name_format,sheet.sheet_song_id')
            ->join('p_song song','sheet.song_id=song.song_id')
            ->where('sheet.sheet_id',$sheet_id)
            ->select();
        return $this->sendSuccess($results);
    }

    //添加歌曲
    public function songAdd(){
        $this->checkParam(self::$rule_AddSong);
        $data=[
            'sheet_id'=>$this->request->post('sheet_id'),
            'song_id'=>$this->request->post('song_id'),
            'create_time'=>date("Y-m-d H:i:s"),
        ];
        $model=new SheetSong();
        $model->data($data)->save();
        if ($model->sheet_song_id){
            return $this->sendSuccess();
        }
        return $this->sendError(400,'保存失败');
    }

    //删除歌曲
    public function songDelete(){
        $this->checkParam(self::$rule_DeleteSong);
        $sheet_song_id=$this->request->param('sheet_song_id');
        $sheet=new SheetSong();
        $result=$sheet->where('sheet_song_id',$sheet_song_id)->delete();
        if ($result){
            return $this->sendSuccess();
        }
        return $this->sendError(400,'删除失败');
    }

}