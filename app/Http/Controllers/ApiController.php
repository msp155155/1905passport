<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaravelUserModel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class ApiController extends Controller
{
    /**
     * 用户注册
     */
    public function reg(Request $request)
    {
        //echo '<pre>';print_r($_POST);echo '</pre>';
        $pwd1 = $request->input('pwd1');
        $pwd2 = $request->input('pwd2');
        //验证两次输入的密码
        if($pwd1 != $pwd2)
        {
            echo "两次输入的密码不一致";
            die;
        }
        $name = $request->input('name');
        $email = $request->input('email');
        $tel = $request->input('tel');
        // 验证 用户名 email mobile 是否已被注册
        $u = LaravelUserModel::where(['name'=>$name])->first();
        if($u){
            $response = [
                'errno' => 500002,
                'msg'   => "用户名已被使用"
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        //验证email
        $u = LaravelUserModel::where(['email'=>$email])->first();
        if($u){
            $response = [
                'errno' => 500003,
                'msg'   => "Email已被使用"
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        //验证tel
        $u = LaravelUserModel::where(['tel'=>$tel])->first();
        if($u){
            $response = [
                'errno' => 500003,
                'msg'   => "电话号已被使用"
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        //生成密码
        $pwd = password_hash($pwd1,PASSWORD_BCRYPT);
        //入库
        $user_info = [
            'email'     => $email,
            'name'      => $name,
            'tel'    => $tel,
            'pwd'  => $pwd
        ];
        $uid = LaravelUserModel::insertGetId($user_info);
        if($uid)
        {
            $response = [
                'errno' => 0,
                'msg'   => 'ok'
            ];
        }else{
            $response = [
                'errno' => 500001,
                'msg'   => "服务器内部错误,请稍后再试"
            ];
        }
        die(json_encode($response));
    }
    /**
     * 用户登录
     */
    public function login(Request $request)
    {
        //echo '<pre>';print_r($_POST);echo '</pre>';
        $value = $request->input('name');
        $pwd = $request->input('pwd');
        // 按name找记录
        $u1 = LaravelUserModel::where(['name'=>$value])->first();
        $u2 = LaravelUserModel::where(['email'=>$value])->first();
        $u3 = LaravelUserModel::where(['tel'=>$value])->first();
        if($u1==NULL && $u2==NULL && $u3==NULL){
            $response = [
                'errno' => 400004,
                'msg'   => "用户不存在"
            ];
            return $response;
        }
        if($u1)     // 使用用户名登录
        {
            if(password_verify($pwd,$u1->pwd)){
                $uid = $u1->user_id;
            }else{
                $response = [
                    'errno' => 400003,
                    'msg'   => 'password wrong'
                ];
                return $response;
            }
        }
        if($u2){        //使用 email 登录
            if(password_verify($pwd,$u2->pwd)){
                $uid = $u2->user_id;
            }else{
                $response = [
                    'errno' => 400003,
                    'msg'   => 'password wrong'
                ];
                return $response;
            }
        }
        if($u3){        // 使用电话号登录
            if(password_verify($pwd,$u3->pwd)){
                $uid = $u3->user_id;
            }else{
                $response = [
                    'errno' => 400003,
                    'msg'   => 'password wrong'
                ];
                return $response;
            }
        }
        $token =  $this->getToken($uid);        //生成token
        $redis_token_key = 'str:user:token:'.$uid;
        Redis::set($redis_token_key,$token,86400);  // 生成token  设置过期时间
        $response = [
            'errno' => 0,
            'msg'   => 'ok',
            'data'  => [
                'uid'   => $uid,
                'token' => $token
            ]
        ];
        return $response;
    }
    /**
     * 生成用户token
     * @param $uid
     * @return false|string
     */
    protected function getToken($uid)
    {
        $token = md5(time() . mt_rand(11111,99999) . $uid);
        return substr($token,5,20);
    }
    /**
     * 获取用户信息接口
     */
    public function showTime()
    {
        if(empty($_SERVER['HTTP_TOKEN']) || empty($_SERVER['HTTP_UID']))
        {
            $response = [
                'errno' => 40003,
                'msg'   => 'Token Not Valid!'
            ];
            return $response;
        }
        //获取客户端的 token
        $token = $_SERVER['HTTP_TOKEN'];
        $uid = $_SERVER['HTTP_UID'];
        $redis_token_key = 'str:user:token:'.$uid;
        //验证token是否有效
        $cache_token = Redis::get($redis_token_key);
        if($token==$cache_token)        // token 有效
        {
            $data = date("Y-m-d H:i:s");
            $response = [
                'errno' => 0,
                'msg'   => 'ok',
                'data'  => $data
            ];
        }else{
            $response = [
                'errno' => 40003,
                'msg'   => 'Token Not Valid!'
            ];
        }
        return $response;
    }

    /**
     * 接口鉴权
     */
    public function auth()
    {
        $uid = $_POST['uid'];
        $token = $_POST['token'];
        if(empty($uid) || empty($token))
        {
            $response = [
                'errno' => 40003,
                'msg'   => 'Token Not Valid!'
            ];
            return $response;
        }
        $redis_token_key = 'str:user:token:'.$uid;
        //验证token是否有效
        $cache_token = Redis::get($redis_token_key);
        if($token==$cache_token)        // token 有效
        {
            $data = date("Y-m-d H:i:s");
            $response = [
                'errno' => 0,
                'msg'   => 'ok',
                'data'  => $data
            ];
        }else{
            $response = [
                'errno' => 40003,
                'msg'   => 'Token Not Valid!'
            ];
        }
        return $response;
    }

    public function info()
    {
        echo phpinfo();
    }
}
