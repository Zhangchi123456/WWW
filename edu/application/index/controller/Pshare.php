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
        $Pshare =PshareModel::create(["photo_id"=>$request->param("photo_id"),"user_id"=>Session::get("user_id"),"creat_time"=>$date]);
        $photo = PhotoMOdel::get(["photo_id"=>$request->param("photo_id")]);
        $photo->setInc("sharenum");
        if($Pshare==true)
            return["message"=>"分享成功","data"=>$Pshare];
    }
    function  deletePshare(Request $request){

        $Pshare =PshareModel::get(["photo_id"=>$request->param("photo_id"),"user_id"=>Session::get("user_id")]);

        if($Pshare==true)
            $Pshare->delete();
            return["message"=>"分享成功","data"=>$Pshare];
    }
}