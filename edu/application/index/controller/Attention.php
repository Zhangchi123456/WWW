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
    function cancelAttention(Request $request){
        $id = $request->param('id');
        $attention = AttentionModel::get(["user_id"=>$id,"fans_id"=>Session::get('user_id')]);
        $attention->delete();

        return['data'=>'取消成功'];
    }
    function  getFans(Request $request){
        $Attention=  AttentionModel::all(["user_id"=>Session::get('user_id')]);
        return ["data"=>$Attention];
    }
    function  addAttention(Request $request){
        $date =date('Y-m-d H:i:s');
        $Attention =AttentionModel::create(["photo_id"=>$request->param("photo_id"),"Attention"=>$request->param("Attention"),"creat_time"=>$date,"name"=>Session::get("user_info.name")]);
        if($Attention==true)

            return["message"=>"评论成功","data"=>$Attention];
    }

}