<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaravelUserModel;
use Illuminate\Support\Facades\Cache;

class ApiController extends Controller
{
    public  function  user(Request $request)
    {
        $LaravelUserModel = new LaravelUserModel;
        $LaravelUserModel->name = $request->name;
        $LaravelUserModel->email = $request->email;
        $LaravelUserModel->tel = $request->tel;
        $LaravelUserModel->pwd = $request->pwd;
        $LaravelUserModel->save();
    }

    public function login(Request $request)
    {
        $name = $request->name;
        $email = $request->email;
        $tel = $request->tel;
        $pwd = $request->pwd;
        $res1 = LaravelUserModel::where([['name','=',$name],['pwd','=',$pwd]])->first();
        $res2= LaravelUserModel::where([['email','=',$email],['pwd','=',$pwd]])->first();
        $res3 = LaravelUserModel::where([['tel','=',$tel],['pwd','=',$pwd]])->first();
        if ($res1){
            $sign = "abcdefg";
            $data = [
                'name'=>$name,
                'pwd' =>$pwd
            ];
            ksort($data);
            $str = "";
            foreach ($data as $k=>$v){
                $str.=$k."=".$v."&";
            }
            $tmp_str = $str.$sign;
            $sign = md5($tmp_str);

            Cache::put('token', $sign, 60*60);
            $a = Cache::get('token');
            echo $a;
            echo "登陆成功";
        }else if($res2){
            $sign = "abcdefg";
            $data = [
                'name'=>$email,
                'pwd' =>$pwd
            ];
            ksort($data);
            $str = "";
            foreach ($data as $k=>$v){
                $str.=$k."=".$v."&";
            }
            $tmp_str = $str.$sign;
            $sign = md5($tmp_str);
            Cache::put('token', $sign, 60*60);
            echo "登陆成功";
        }else if($res3){
            $sign = "abcdefg";
            $data = [
                'name'=>$tel,
                'pwd' =>$pwd,
                'time'=>time()
            ];
            var_dump($data);echo "<hr>";
            ksort($data);
            $str = "";
            foreach ($data as $k=>$v){
                $str.=$k."=".$v."&";
            }
            $tmp_str = $str.$sign;
            $sign = md5($tmp_str);
            Cache::put('token', $sign, 60*60);
            $a = Cache::get('token');
            echo $a;
            echo "登陆成功";
        }
    }
    public function info()
    {
        echo phpinfo();
    }

    public function userInfo(Request $request)
    {
        $data =$request->header('sign');
        $name = $request->name;
        $email = $request->email;
        $tel = $request->tel;
        $pwd = $request->pwd;
        $token = Cache::get('token');

        if ($data === $token){
            $res1 = LaravelUserModel::where([['name','=',$name],['pwd','=',$pwd]])->first();
            $res2= LaravelUserModel::where([['email','=',$email],['pwd','=',$pwd]])->first();
            $res3 = LaravelUserModel::where([['tel','=',$tel],['pwd','=',$pwd]])->first();
            if ($res1){
                var_dump($res1);
            }elseif ($res2){
                var_dump($res2);
            }elseif ($res3){
                var_dump($res2);
            }
        }
    }
}
