<?php
namespace app\index\controller;
use app\index\controller\Base;
use phpDocumentor\Reflection\Types\Resource;
use think\Request;
use app\index\model\Photo as PhotoMOdel;
use app\index\model\Album as AlbumMOdel;
use app\index\model\User as UserMOdel;
use app\index\model\Tag as TagModel;
use app\index\model\Attention as AttentionModel;
use app\index\model\Pclick;
use app\index\model\Pshare;
use think\Session;
class Photo extends Base{
    public function Photocenter()
    {
        return $this -> view -> fetch();
    }
    public  function uploadimg(Request $request){
        $data = $request->param();
        $user=Session::get('user_info.name');
        $albumname = $request->param("album_name");
        $photoinfo = $request->param("photoinfo");
        $tags = $request->param("tags");
        $status=0;
        $rule=[
            'album_name|相册名'=>'require',
            'photo|照片'=>'require',
            'photoinfo|相片介绍'=>'require',
        ];
        $meg=[
            'album_name' => ['require'=>'相册名不能为空'],

            'photo' => ['require'=>'照片不能为空'],
            'photoinfo' => ['require'=>'照片介绍不能为空'],
        ];
        $result = $this -> validate($data,$rule,$meg);
        if($result===true) {
            $url = "./static/image/" . $user . "/";
            if (!is_dir("./static/image/" . $user))//当路径不穿在
            {
                mkdir("./static/image/" . $user);//创建路径
            }
            $date = date('Y-m-d H.i.s');
            $imgname = $user . $date . ".jpg";
            $ismoved = move_uploaded_file($_FILES['upload_file']['tmp_name'], $url . $imgname);
            if ($ismoved) {
                $album = AlbumMOdel::get(["album_name" => $albumname, "user_id" => Session::get("user_id")]);
                $album_id = $album->album_id;
                 $photomodel =  PhotoMOdel::create(["photo_name" => $imgname, "creat_time" => $date, "album_id" => $album_id, "photo_info" => $photoinfo, "user_id" => Session::get("user_id")]);
                $status=1;
                $tagarray=explode(",",$tags);
                for($i =0;$i<count($tagarray);$i++){
                    TagModel::create(["photo_id"=>$photomodel->photo_id,"tag"=>$tagarray[$i]]);

                }
                $album->setInc("photonum");
                return ['message'=>"上传成功",'status'=>$status];


            }
            else{
                return ['message'=>"上传失败",'status'=>$status];
            }


        }
        return ['message'=>$tags,'status'=>$status];
    }
    public  function  getPlist(Request $request){
        $albumname = $request->param("albumname");
        $user_id = Session::get("user_id");
        $user_id1=$request->param("user_id");
        $album =AlbumMOdel::get(["album_name"=>$albumname,"user_id"=>$user_id1,"isuse"=>"1"]);
       $photolist= PhotoMOdel::all(["album_id"=>$album->album_id,"user_id"=>$user_id1,"isuse"=>"1"]);
            foreach ($photolist as $value){
                $isshare = Pshare::get(["user_id"=>$user_id,"photo_id"=>$value->photo_id]);
                $isclick = Pclick::get(["user_id"=>$user_id,"photo_id"=>$value->photo_id]);
                $tag = TagModel::all(["photo_id"=>$value->photo_id]);
                $userinfo =UserMOdel::get(["user_id"=>$value->user_id]);
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
                    'albumname'=>$albumname,
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
    public function getAlllist(Request $request){
        $user_id = Session::get("user_id");
        $user_id1=$request->param("user_id");
        $photolist= PhotoMOdel::all(["user_id"=>$user_id1,"isuse"=>"1"]);
        foreach ($photolist as $value){
           $album = AlbumMOdel::get(["album_id"=>$value->album_id]);
            $tag = TagModel::all(["photo_id"=>$value->photo_id]);
            $isshare = Pshare::get(["user_id"=>$user_id,"photo_id"=>$value->photo_id]);
            $isclick = Pclick::get(["user_id"=>$user_id,"photo_id"=>$value->photo_id]);
            $userinfo =UserMOdel::get(["user_id"=>$value->user_id]);
            $attention = AttentionModel::get(["user_id"=>$value->user_id,"fans_id"=>$user_id]);
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
               'albumname'=>$album->album_name,
                'taglist'=>$tag,
                'user_info'=>$userinfo,
                'share_info' => "1",
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
            if($attention){
                $data['attention']='1';
            }
            else{
                $data['attention']='0';
            }
            $photolist1[] = $data;
        }
        $photolistshare = Pshare::all(["user_id"=>$user_id1]);
        if($photolistshare){
            foreach ($photolistshare as $value1) {
                $value = PhotoMOdel::get(["photo_id" => $value1->photo_id, "isuse" => "1"]);
                $album = AlbumMOdel::get(["album_id" => $value->album_id]);
                $tag = TagModel::all(["photo_id" => $value->photo_id]);
                $isshare = Pshare::get(["user_id" => $user_id, "photo_id" => $value->photo_id]);
                $isclick = Pclick::get(["user_id" => $user_id, "photo_id" => $value->photo_id]);
                $userinfo = UserMOdel::get(["user_id" => $value->user_id]);
                $shareinfo = UserMOdel::get(["user_id" => $value1->user_id]);
                $data = [
                    'photo_id' => $value->photo_id,
                    'photo_name' => $value->photo_name,
                    'album_id' => $value->album_id,
                    'clicknum' => $value->clicknum,
                    'sharenum' => $value->sharenum,
                    'user_id' => $value->user_id,
                    'photo_info' => $value->photo_info,
                    'commentnum' => $value->commentnum,
                    'albumname' => $album->album_name,
                    'taglist' => $tag,
                    'user_info' => $userinfo,
                    'share_info' => $shareinfo,
                ];
                $data['creat_time']=$value1->creat_time;
                if ($isshare) {
                    $data['isshare'] = '1';
                } else {
                    $data['isshare'] = '0';
                }
                if ($isclick) {
                    $data['isclick'] = '1';
                } else {
                    $data['isclick'] = '0';
                }
                $photolist1[] = $data;


            }
        }
        return ["data"=>$photolist1];
    }
    public function getAllattentionlist(Request $request){
        $user_id = Session::get("user_id");
        $attentionlist = AttentionModel::all(["fans_id"=>$user_id]);
        foreach ($attentionlist as $value1) {
            $photolist = PhotoMOdel::all(["user_id" => $value1->user_id, "isuse" => "1"]);
            foreach ($photolist as $value) {
                $album = AlbumMOdel::get(["album_id" => $value->album_id]);
                $tag = TagModel::all(["photo_id" => $value->photo_id]);
                $isshare = Pshare::get(["user_id" => $user_id, "photo_id" => $value->photo_id]);
                $isclick = Pclick::get(["user_id" => $user_id, "photo_id" => $value->photo_id]);
                $userinfo = UserMOdel::get(["user_id" => $value->user_id]);

                $data = [
                    'photo_id' => $value->photo_id,
                    'photo_name' => $value->photo_name,
                    'album_id' => $value->album_id,
                    'clicknum' => $value->clicknum,
                    'sharenum' => $value->sharenum,
                    'creat_time' => $value->creat_time,
                    'user_id' => $value->user_id,
                    'photo_info' => $value->photo_info,
                    'commentnum' => $value->commentnum,
                    'albumname' => $album->album_name,
                    'taglist' => $tag,
                    'user_info' => $userinfo,
                    'share_info'=>"1",
                ];
                if ($isshare) {
                    $data['isshare'] = '1';
                } else {
                    $data['isshare'] = '0';
                }
                if ($isclick) {
                    $data['isclick'] = '1';
                } else {
                    $data['isclick'] = '0';
                }
                $photolist1[] = $data;

            }
        }

        $photolistown = PhotoMOdel::all(["user_id" => $user_id, "isuse" => "1"]);
        foreach ($photolistown as $value) {
            $album = AlbumMOdel::get(["album_id" => $value->album_id]);
            $tag = TagModel::all(["photo_id" => $value->photo_id]);
            $isshare = Pshare::get(["user_id" => $user_id, "photo_id" => $value->photo_id]);
            $isclick = Pclick::get(["user_id" => $user_id, "photo_id" => $value->photo_id]);
            $userinfo = UserMOdel::get(["user_id" => $value->user_id]);

            $data = [
                'photo_id' => $value->photo_id,
                'photo_name' => $value->photo_name,
                'album_id' => $value->album_id,
                'clicknum' => $value->clicknum,
                'sharenum' => $value->sharenum,
                'creat_time' => $value->creat_time,
                'user_id' => $value->user_id,
                'photo_info' => $value->photo_info,
                'commentnum' => $value->commentnum,
                'albumname' => $album->album_name,
                'taglist' => $tag,
                'user_info' => $userinfo,
                'share_info'=>"1"
            ];
            if ($isshare) {
                $data['isshare'] = '1';
            } else {
                $data['isshare'] = '0';
            }
            if ($isclick) {
                $data['isclick'] = '1';
            } else {
                $data['isclick'] = '0';
            }
            $photolist1[] = $data;

        }
       $photolistshare = Pshare::all(["user_id"=>$user_id]);
        if($photolistshare){
        foreach ($photolistshare as $value1) {
            $value = PhotoMOdel::get(["photo_id" => $value1->photo_id, "isuse" => "1"]);
                $album = AlbumMOdel::get(["album_id" => $value->album_id]);
                $tag = TagModel::all(["photo_id" => $value->photo_id]);
                $isshare = Pshare::get(["user_id" => $user_id, "photo_id" => $value->photo_id]);
                $isclick = Pclick::get(["user_id" => $user_id, "photo_id" => $value->photo_id]);
                $userinfo = UserMOdel::get(["user_id" => $value->user_id]);
                $shareinfo = UserMOdel::get(["user_id" => $value1->user_id]);
                $data = [
                    'photo_id' => $value->photo_id,
                    'photo_name' => $value->photo_name,
                    'album_id' => $value->album_id,
                    'clicknum' => $value->clicknum,
                    'sharenum' => $value->sharenum,
                    'user_id' => $value->user_id,
                    'photo_info' => $value->photo_info,
                    'commentnum' => $value->commentnum,
                    'albumname' => $album->album_name,
                    'taglist' => $tag,
                    'user_info' => $userinfo,
                    'share_info' => $shareinfo,
                ];
                $data['creat_time']=$value1->creat_time;
                if ($isshare) {
                    $data['isshare'] = '1';
                } else {
                    $data['isshare'] = '0';
                }
                if ($isclick) {
                    $data['isclick'] = '1';
                } else {
                    $data['isclick'] = '0';
                }
                $photolist1[] = $data;


        }
        }
      return ["data"=>$photolist1];
    }
    public  function addClicknum(Request $request){

    PClick::create(["photo_id"=>$request->param('photo_id'),"user_id"=>Session::get('user_id')]);
     $photo = PhotoMOdel::get(["photo_id"=>$request->param("photo_id")]);
     $photo->setInc("clicknum");
    }
    public  function addSharenum(Request $request){

        PShare::create(["photo_id"=>$request->param('photo_id'),"user_id"=>Session::get('user_id')]);
        $photo = PhotoMOdel::get(["photo_id"=>$request->param("photo_id")]);
        $photo->setInc("sharenum");
    }
    public  function upacphoto(Request $request){
        $activity_id = $request->param("activity_id");
        $photo_info = $request->param("photo_info");
        $user_id = Session::get("user_id");
        $user = Session::get("user_info.name");
        $url = "./static/image/" . $user . "/";
        if (!is_dir("./static/image/" . $user))//当路径不穿在
        {
            mkdir("./static/image/" . $user);//创建路径
        }
        $date = date('Y-m-d H.i.s');
        $imgname = $user . $date . ".jpg";
        $ismoved = move_uploaded_file($_FILES['upload_file']['tmp_name'], $url . $imgname);
        if ($ismoved) {
            $photomodel =  PhotoMOdel::create(["photo_name" => $imgname,"activity_id"=>$activity_id,"creat_time" => $date, "album_id" => "0", "photo_info" => $photo_info, "user_id" => Session::get("user_id"),"isuse"=>"0"]);
            if($photomodel)
                return "发布成功";
            else
                return "发布失败";
        }
        return "发布失败";

    }
    public function  deletephoto(Request $request){
         $id = $request->param("id");
        $photo= PhotoMOdel::update(["isuse"=>"0"],["photo_id"=>$id]);
        $photo1 = PhotoMOdel::get(["photo_id"=>$id]);
         if($photo){
             $album = AlbumMOdel::get(["album_id"=>$photo1->album_id]);
             $album->setDec("photonum");
             return "删除成功";
         }
         else{
             return "删除失败";
         }
    }
    public function  getActivityPhoto(Request $request)
    {
        $user_id = Session::get("user_id");
        $id = $request->param("id");
        $photolist = PhotoMOdel::all(["activity_id"=>$id]);
        foreach ($photolist as $value){
            $album = AlbumMOdel::get(["album_id"=>$value->album_id]);
            $tag = TagModel::all(["photo_id"=>$value->photo_id]);
            $isshare = Pshare::get(["user_id"=>$user_id,"photo_id"=>$value->photo_id]);
            $isclick = Pclick::get(["user_id"=>$user_id,"photo_id"=>$value->photo_id]);
            $userinfo =UserMOdel::get(["user_id"=>$value->user_id]);
            $attention = AttentionModel::get(["user_id"=>$value->user_id,"fans_id"=>$user_id]);
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
                'albumname'=>$album->album_name,
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
            if($attention){
                $data['attention']='1';
            }
            else{
                $data['attention']='0';
            }
            $photolist1[] = $data;
        }
        return ["data"=>$photolist1];
    }
}