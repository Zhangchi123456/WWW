<?php
namespace app\index\controller;
use app\index\controller\Base;
use phpDocumentor\Reflection\Types\Resource;
use think\Request;
use app\index\model\Photo ;
use think\Session;
use app\index\model\Pshare as PshareModel;
class Pshare extends Base
{
    function  getPshare(Request $request){
        $Pshare=  PshareModel::all(["photo_id"=>$request->param("photo_id")]);
        return ["data"=>$Pshare];
    }
    function  addPshare(Request $request){
        $date =date('Y-m-d H:i:s');
        $Pshare =PshareModel::create(["photo_id"=>$request->param("photo_id"),"Pshare"=>$request->param("Pshare"),"creat_time"=>$date,"name"=>Session::get("user_info.name")]);
        if($Pshare==true)

            return["message"=>"评论成功","data"=>$Pshare];
    }

}