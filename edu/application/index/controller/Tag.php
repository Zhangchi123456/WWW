<?php
namespace app\index\controller;
use app\index\controller\Base;
use phpDocumentor\Reflection\Types\Resource;
use think\Request;
use app\index\model\Photo as PhotoMOdel;
use app\index\model\Album as AlbumMOdel;
use app\index\model\User as UserMOdel;
use app\index\model\Tag as TagModel;
use app\index\model\Pclick;
use app\index\model\Pshare;
use think\Session;
class Tag extends Base{
        public  function getPhotosByTag(Request $request){
            $tag = $request->param("tag");
            $taglist = TagModel::all(["tag"=>$tag]);
            $user_id = Session::get("user_id");
            foreach ($taglist as $value){
                $photo = PhotoMOdel::get(["photo_id"=>$value->photo_id]);
                $photolist[] = $photo;
            }
            foreach ($photolist as $value){
                $isshare = Pshare::get(["user_id"=>$user_id,"photo_id"=>$value->photo_id]);
                $isclick = Pclick::get(["user_id"=>$user_id,"photo_id"=>$value->photo_id]);
                $tag = TagModel::all(["photo_id"=>$value->photo_id]);
                $userinfo =UserMOdel::get(["user_id"=>$value->user_id]);
                $albumname = AlbumMOdel::get(["album_id"=>$value->album_id]);
                $data=[
                    'photo_id'=>$value->photo_id ,
                    'photo_name'=>$value->photo_name,
                    'album_id'=>$value->album_id,
                    'clicknum'=>$value->clicknum,
                    'sharenum'=>$value->sharenum,
                    'creat_time'=>$value->creat_time,
                    'user_id'=>$value->user_id,
                    'photo_info'=>$value->photo_info,
                    'commentnum'=>$value->commentnum,
                    'albumname'=>$albumname->album_name,
                    'taglist'=>$tag,
                    'user_info'=>$userinfo
                ];
                if($isshare){
                    $data['isshare']='1';
                }
                else{
                    $data['isshare']='0';
                }
                if($isclick){
                    $data['isclick']='1';
                }
                else{
                    $data['isclick']='0';
                }
                $photolist1[] = $data;
            }
            return ["data"=>$photolist1];
        }

}