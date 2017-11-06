<?php
namespace app\index\controller;
use app\index\controller\Base;
use phpDocumentor\Reflection\Types\Resource;
use think\Request;
use app\index\model\Photo ;
use think\Session;
use app\index\model\Comment as CommentModel;
class Comment extends Base
{
    function  getComment(Request $request){
      $comment=  CommentModel::all(["photo_id"=>$request->param("photo_id")]);
        return ["data"=>$comment];
    }
    function  addComment(Request $request){
     $date =date('Y-m-d H:i:s');
     $comment =CommentModel::create(["photo_id"=>$request->param("photo_id"),"comment"=>$request->param("comment"),"creat_time"=>$date,"name"=>Session::get("user_info.name")]);
     if($comment==true)

         return["message"=>"评论成功","data"=>$comment];
    }

}