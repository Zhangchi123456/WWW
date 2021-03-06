<?php
namespace app\index\controller;
use app\index\controller\Base;
use phpDocumentor\Reflection\Types\Resource;
use think\Request;
use app\index\model\User as UserModel;
use think\Session;
class User extends Base

{

    public function Login()
    {
        return $this -> view -> fetch();
    }
    public function User_info()
    {
        return $this -> view -> fetch();
    }
    public function Other_info()
    {
        return $this -> view -> fetch();
    }
    public function User_photo()
    {
        return $this -> view -> fetch();
    }
    public function User_share()
    {
        return $this -> view -> fetch();
    }
    public function Sign()
    {
        return $this -> view -> fetch();
    }
    public function setOther(Request $request){
        $otherid = $request->param("username");
        $userid=Session::get("user_id");
        if($userid==$otherid){
            return 1;
        }
        else {
            $user = UserModel::get(["user_id" => $otherid]);
            Session::set("other_id", $user->user_id);
            Session::set("other_info", $user->getData());
            return 0;
        }
    }
    public function checkName(Request $request){
        $name = trim($request->param('name'));
        $status = 1;
        $message = "用户名不可用";
        if(UserModel::get(['name'=>$name])){
            $status=0;
        }
        return ["status"=>$status,"message"=>$message];
    }
    public function updateUser(Request $request){
        $data = $request->param();
        $status = 0;

        $message = "添加失败(信息填写不完整)";
        $rule=[
            'name|用户名'=>'require',
            'password|密码'=>'require',
            'email|邮箱'=>'require',
            'telephone|电话'=>'require',
        ];
        $meg=[
            'name' => ['require'=>'用户名不能为空'],
            'password' => ['require'=>'密码不能为空'],
            'email' => ['require'=>'邮箱不能为空'],
            'telephone' => ['require'=>'电话不能为空'],
        ];
        $result = $this -> validate($data,$rule,$meg);
        $user=Session::get('user_info.name');
        if($result===true) {
            if($request->param("uploadfile1")=="1") {
                $url = "./static/image/" . $user . "/";
                $date = date('Y-m-d H.i.s');
                $imgname = $user . $date . ".jpg";
                $ismoved = move_uploaded_file($_FILES['uploadfile']['tmp_name'], $url . $imgname);
                $user = UserModel::update(['name' => $request->param('name'), 'password' => $request->param('password'),
                    'email' => $request->param('email'), 'phone' => $request->param('telephone'), "photo" => $imgname], ['user_id' => Session::get('user_id')]);

            }
            else{
                $user = UserModel::update(['name' => $request->param('name'), 'password' => $request->param('password'),
                    'email' => $request->param('email'), 'phone' => $request->param('telephone')],['user_id' => Session::get('user_id')]);

            }
            if ($user === null) {
                $status = 0;
                $message = "更新失败";
            } else {
                $status = 1;
                $message = "更新成功";
                $user1 =UserModel::get(['user_id'=>Session::get('user_id')]);
                    Session::set('user_info',$user1 ->getData());
            }

            return ["status" => $status, "message" => $message];
        }
        else {

            return ["status" => $status, "message" => $result];
        }

    }
    public function addUser(Request $request){
        $data = $request->param();
        $status = 0;
        $message = "添加失败(信息填写不完整)";
        $rule=[
            'name|用户名'=>'require',
            'password|密码'=>'require',
            'email|邮箱'=>'require',
        ];
        $meg=[
            'name' => ['require'=>'用户名不能为空'],
            'password' => ['require'=>'密码不能为空'],
            'email' => ['require'=>'邮箱不能为空'],
        ];
        $user=$request->param('name');
        $result = $this -> validate($data,$rule,$meg);
        if($result===true) {
                $url = "./static/image/" . $user . "/";
                if (!is_dir("./static/image/" . $user))//当路径不穿在
                {
                    mkdir("./static/image/" . $user);//创建路径
                }
                $date = date('Y-m-d H-i-s');
                $imgname = $user . $date . ".jpg";
                $ismoved = move_uploaded_file($_FILES['uploadfile']['tmp_name'], $url . $imgname);
                if ($ismoved) {

                    $user = UserModel::create(['name' => $request->param('name'), 'password' => $request->param('password'),
                        'email' => $request->param('email'), 'phone' => $request->param('telephone'), 'introduction' => $request->param('introduction')
                    ,'photo'=>$imgname]);
                    if ($user === null) {
                        $status = 0;
                        $message = "注册失败";
                    } else {
                        $status = 1;
                        $message = "注册成功";
                        Session::set('user_id', $user->user_id);
                        Session::set('user_info', $user->getData());
                    }

                    return ["status" => $status, "message" => $message];
                }
                else {
                    return ["status" => $status, "message" => $_FILES['uploadfile']['tmp_name']];
                }

        }
        else {

            return ["status" => $status, "message" => $result];
        }

    }
    public function checklogin(Request $request)
    {
        $status = 0;
        $result='';
        $data=$request -> param();
        //验证规则
        $rule =[
            'name|用户名' => 'require',
            'password|密码' => 'require',
            'verify|验证码' =>'require|captcha'

        ];
        $meg=[
            'name' => ['require'=>'用户名不能为空'],
            'password' => ['require'=>'密码不能为空'],
            'verify' =>['require'=>'验证码不能为空',
                'captcha'=>'验证码错误'],
        ];
        $result=$this -> validate($data,$rule,$meg);
        if($result===true){
            $map=[
                'name'=>$data['name'],
                'password'=>$data['password'],
            ];
            $user = UserModel::get($map);
            if($user==null){
                $result="没有找到该用户";
            }
            else{
                $status = 1;
                $result="验证通过";
                Session::set('user_id',$user->user_id);
                Session::set('user_info',$user ->getData());
            }

        }

        return ["status" =>$status,"message" =>$result,'data' =>$data];
    }
    public function Logout(Request $request)
    {
            Session::delete('user_id');
            Session::delete("user_info");

            return ['message'=>"退出成功"];
    }
}