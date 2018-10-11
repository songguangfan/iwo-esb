<?php
/**
 * 授权基类，所有获取access_token以及验证access_token 异常都在此类中完成
 */
namespace app\api\controller;

use think\Controller;
use think\Request;
use think\Config;
use think\Exception;
use app\api\controller\Send;
use think\response\Redirect;
use think\Session;

class Api extends Controller
{	
	use Send;

	/**
     * 对应操作
     * @var array
     */
    public $methodToAction = [
        'get' => 'read',
        'post' => 'save',
        'put' => 'update',
        'delete' => 'delete',
        'patch' => 'patch',
        'head' => 'head',
        'options' => 'options',
    ];
    /**
     * 允许访问的请求类型
     * @var string
     */
    public $restMethodList = 'get|post|put|delete|patch|head|options';
    /**
     * 默认不验证
     * @var bool
     */
    protected $apiAuth = true;

    protected $tokenAuth=true;
	protected $request;
	/**
     * 当前请求类型
     * @var string
     */
    protected $method;
    /**
     * 当前资源类型
     * @var string
     */
    protected $type;

    public static $app;
    /**
     * 返回的资源类的
     * @var string
     */
    protected $restTypeList = 'json';
    /**
     * REST允许输出的资源类型列表
     * @var array
     */
    protected $restOutputType = [ 
        'json' => 'application/json',
    ];
    protected $_user;

    protected $_user_id;
    /**
     * 客户端信息
     */
    protected $clientInfo;
	/**
	 * 控制器初始化操作
	 */
	public function _initialize()
    {
    	$request = Request::instance();
    	$this->request = $request;
        $this->init();    //检查资源类型
        if ($this->apiAuth){
            //echo 111;
            $this->checkAuth();  //接口检查
        }
    } 

    /**
     * 初始化方法
     * 检测请求类型，数据格式等操作
     */
    public function init()
    {
    	// 资源类型检测
        $request = Request::instance();
        $ext = $request->ext();
        if ('' == $ext) {
            // 自动检测资源类型
            $this->type = $request->type();
        } elseif (!preg_match('/\(' . $this->restTypeList . '\)$/i', $ext)) {
            // 资源类型非法 则用默认资源类型访问
            $this->type = $this->restDefaultType;
        } else {
            $this->type = $ext;
        }
        $this->setType();
        // 请求方式检测
        $method = strtolower($request->method());
        $this->method = $method;
        if (false === stripos($this->restMethodList, $method)) {

          return self::returnmsg(405,'Method Not Allowed',[],["Access-Control-Allow-Origin" => $this->restMethodList]);
        }
    }

    /**
     * 检测客户端是否有权限调用接口
     */
    public function checkAuth()
    {
        $request_header=Request::instance()->header();
        if (!array_key_exists("timestamp",$request_header)){
            return $this->returnmsg(401,"请求认证错误, 请重新登录");
        }

        if (time() - substr($request_header['timestamp'], 0, 10) > Config::get("expare")) {
            return $this->returnmsg(401, '签名认证超时, 请重新登录');
        }

        $token='';

        if (!array_key_exists("token",$request_header)&&$this->tokenAuth==true){
            return $this->returnmsg(401,"签名认证错误，请重新登陆");
        }
        if ($this->tokenAuth){
            $token=$request_header['token'];
            if (!$request_header['token']){
                return $this->returnmsg(401,"签名认证错误，请重新登陆1");
            }

            $this->checkToken($token);
        }
    }

    /**
     * token验证
     *
     * @param $token
     */
    protected function checkToken($token)
    {
        if (!$token) {
            return $this->returnmsg(401,"签名认证错误，请重新登陆");
        }
        try{
            $current_token=Session::get('jwt:'.$token);
            if (!$current_token){
                return $this->returnmsg(401,"签名认证错误，请重新登陆");
            }

            $playload=Jwt::decode($current_token,Config::get("jwt_key"));
            if (!$playload){
                return $this->returnmsg(401,"签名认证错误，请重新登陆");
            }

            if (time() -$playload['time'] > Config::get("expare")){
                return $this->returnmsg(401,"签名过期，请重新登陆");
            }
            $this->_user_id=$playload['user_id'];
        }catch (Exception $e){
            return $this->returnmsg(401,"签名认证错误，请重新登陆");
        }
    }
    /**
     * 空操作
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function _empty()
    {
        return $this->sendSuccess([], 'empty method!', 200);
    }

    /**
     * 验证参数
     * @param $rule
     */
    public function checkParam($rule){
        $result = $this->validate($this->request->param(),$rule);
        if ($result!==true){
            return $this->returnmsg(400,$result);
        }
    }
}