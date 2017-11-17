<?php
namespace app\index\controller;
use app\index\controller\Base;
use phpDocumentor\Reflection\Types\Resource;
use think\Request;
use app\index\model\User ;
use think\Session;
use app\index\model\Attention as AttentionModel;
class Attention extends Base
{
    function  getAttention(Request $request){
        $Attention=  AttentionModel::all(["fans_id"=>Session::get('user_id')]);
        if($Attention) {
            foreach ($Attention as $value) {
                $user = User::get(["user_id" => $value->user_id]);
                $data = $user->getData();
                $datalist[] = $data;
            }
            return ["data"=>$datalist];
        }
        else{
            return['data'=>"无关注列表"];
        }

    }
    function isAttention(Request $request){
        $attention =AttentionModel::get(["fans_id"=>Session::get('user_id'),"user_id"=>Session::get("other_info.user_id")]);
        if($attention){ return "1";}
        return "0";
    }
    function dealAttention(Request $request){
        $temp = $request->param("temp");
        $date =date('Y-m-d H:i:s');
        if($temp==1){
            AttentionModel::create(["fans_id"=>Session::get('user_id'),"user_id"=>Session::get('other_info.user_id'),"creat_time"=>$date]);
            return "关注成功";
        }
        else{
            $attention = AttentionModel::get(["fans_id"=>Session::get('user_id'),"user_id"=>Session::get('other_info.user_id')]);
            $attention->delete();
            return "取消成功";
        }
    }

    function cancelAttention(Request $request){
        $id = $request->param('id');
        $attention = AttentionModel::get(["user_id"=>$id,"fans_id"=>Session::get('user_id')]);
        $attention->delete();

        return "取消成功";
    }
    function  getFans(Request $request){
        $Attention=  AttentionModel::all(["user_id"=>Session::get('user_id')]);
        return ["data"=>$Attention];
    }
    function  addAttention(Request $request){
        $date =date('Y-m-d H:i:s');
        $Attention =AttentionModel::create(["user_id"=>$request->param("id"),"creat_time"=>$date,"fans_id"=>Session::get("user_info.user_id")]);
        if($Attention==true)

            return "关注成功";
    }

}