<?php
/**
 * Created by PhpStorm.
 * User: xin
 * Date: 2018/10/12
 * Time: 下午2:11
 */
namespace app\api\controller\v1;

use app\api\controller\Api;
use app\api\model\Filghtinfo;
use app\api\model\Seat;
use think\Db;

class Sync extends Api {

    public static $rule_SeatInfo=[
        'FlightNum' =>'require',
    ];

    public static $rule_transInfo=[
        'FlightNum' =>'require',
        'date'      =>'require',
    ];
    //座位号信息
    public function wsSeatInfo(){
        //var_dump($this->request->param('FlightNum'));die;
        $this->checkParam(self::$rule_SeatInfo);
        $flightNum=$this->request->param('FlightNum');
        $model=new Seat();
        $result=$model->where('flight_number',$flightNum)->select()->toArray();
        $infos=array();
        if ($result){
            foreach ($result as $k=>$v){
                $infos[$v['row_num']][]=$v['columns_num'];
            }
            return $this->sendSuccess($infos);
        }
        return $this->sendError('400','请求结果不存在');
    }

    //乘客信息
    public function wsIdentitySeatInfo(){
        $this->checkParam(self::$rule_SeatInfo);
        $flightNum=$this->request->param('FlightNum');
        $results=Db::table('p_passenger p')
            ->field('p.telephone,p.id_card,s.row_num,s.columns_num')
            ->join('p_seat s','s.seat_id=p.seat_id','left')
            ->where('s.flight_number',$flightNum)
            ->select();
        if ($results){
            foreach ($results as &$v){
                $v['seat']=$v['row_num'].$v['columns_num'];
                unset($v['row_num']);
                unset($v['columns_num']);
            }

            return $this->sendSuccess($results);
        }
        return $this->sendError('400','请求结果不存在');
    }

    //目的地信息获取
    public function wsDestinationInfo(){
        $this->checkParam(self::$rule_SeatInfo);
        $flightNum=$this->request->param('FlightNum');
        $model=new Filghtinfo();
        $result=$model->field('in_dest,in_depa,in_temp')->where('flight_number',$flightNum)->find()->toArray();

        if ($result){
            return $this->sendSuccess($result);
        }
        return $this->sendError('400','请求结果不存在');
    }

    //中转信息获取
    public function wsTransferInfo(){
        $this->checkParam(self::$rule_SeatInfo);
        $flightNum=$this->request->param('FlightNum');
        $date=$this->request->param('date');
        $model=new Filghtinfo();
        $result=$model->field('start_time,end_time')->where('flight_number',$flightNum)->where('flight_date',$date)->find()->toArray();
        $data=array();
        if ($result){
            $seccend=strtotime($result['end_time'])-strtotime($result['start_time']);
            $str=gmdate("H",$seccend).'小时'.gmdate('i',$seccend).'分';

            return $this->sendSuccess(['stopping_time'=>$str]);
        }
        return $this->sendError('400','请求结果不存在');
    }

    //行李托盘信息获取
    public function wsBaggageInfo(){
        $this->checkParam(self::$rule_SeatInfo);
        $flightNum=$this->request->param('FlightNum');
        $date=$this->request->param('date');
    }

    //登机口信息获取
    public function wsBoradingGateInfo(){
        $this->checkParam(self::$rule_SeatInfo);
        $flightNum=$this->request->param('FlightNum');
        $date=$this->request->param('date');
        $model=new Filghtinfo();
        $result=$model->field('boarding_gate_num as boarding_gate')->where('flight_number',$flightNum)->where('flight_date',$date)->find()->toArray();
        if ($result){
            return $this->sendSuccess($result);
        }
        return $this->sendError('400','请求结果不存在');
    }
}