<?php
namespace app\index\controller;
use app\index\controller\Base;
use phpDocumentor\Reflection\Types\Resource;
use think\Request;
use app\index\model\Photo as PhotoMOdel;
use app\index\model\Album as AlbumMOdel;
use app\index\model\User as UserMOdel;
use app\index\model\Tag as TagModel;
use app\index\model\Activity as ActivityModel;
use app\index\model\Pclick;
use app\index\model\Pshare;
use think\Session;
class Activity extends Base{
      public  function  activittCenter(){
          return $this->view->fetch();
      }
      public  function  getActivityinfo(Request $request){
          $name = $request->param("name");
          $activity=ActivityModel::get(["activity_name"=>$name]);
          return $activity;
      }
      public  function  checkActivityName(Request $request){
          $name = $request->param("name");
          $activity = ActivityModel::get(["activity_name"=>$name]);
          if($activity){
              return ["status"=>"0","message"=>"活动名已存在"];
          }
          else{
              return ["status"=>"1","message"=>"活动名可用"];
          }
      }

}