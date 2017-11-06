<?php
namespace app\index\controller;
use app\index\controller\Base;
use phpDocumentor\Reflection\Types\Resource;
use think\Request;
use app\index\model\Photo ;
use think\Session;
use app\index\model\Pclick as PclickModel;
class Pclick extends Base
{
    function  getPclick(Request $request){
        $Pclick=  PclickModel::all(["photo_id"=>$request->param("photo_id")]);
        return ["data"=>$Pclick];
    }
    function  addPclick(Request $request){
        $date =date('Y-m-d H:i:s');
        $Pclick =PclickModel::create(["photo_id"=>$request->param("photo_id"),"Pclick"=>$request->param("Pclick"),"creat_time"=>$date,"name"=>Session::get("user_info.name")]);
        if($Pclick==true)

            return["message"=>"评论成功","data"=>$Pclick];
    }

}