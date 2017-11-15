<?php
namespace app\index\controller;
use app\index\controller\Base;
use phpDocumentor\Reflection\Types\Resource;
use think\Request;
use app\index\model\Album as AlbumModel;
use think\Session;
class Album extends Base
{

    public function checkAlbumName(Request $request){
        $name = trim($request->param('name'));
        $status = 1;
        $message = "相册名不存在";
        if(AlbumModel::get(['album_name'=>$name,"user_id"=>Session::get('user_id')])){
            $status=0;
        }
        return ["status"=>$status,"message"=>$message];
    }
    public function addAlbum(Request $request){
        $data = $request->param();
        $status = 0;
        $rule=[
            'albumname|相册名'=>'require',
            'photo|相册封面'=>'require',
            'albuminfo|相册介绍'=>'require',
        ];
        $meg=[
            'albumname' => ['require'=>'相册名不能为空'],
            'photo' => ['require'=>'相册封面不能为空'],
            'albuminfo' => ['require'=>'相册介绍不能为空'],
        ];
        $date =date('Y-m-d H.i.s');
        $result = $this -> validate($data,$rule,$meg);
        $user=Session::get('user_info.name');
        $url="./static/image/".$user."/";
        if (!is_dir("./static/image/".$user))//当路径不穿在
        {
            mkdir("./static/image/".$user);//创建路径
        }
        $imgname = $user.$date.".jpg";
        $ismoved= move_uploaded_file($_FILES['photo-1']['tmp_name'],$url.$imgname);
        if($result===true &&  $ismoved===true) {
            $album = AlbumModel::create(['album_name' => $request->param('albumname'), 'album_info' => $request->param('albuminfo'),
                'user_id' => Session::get('user_id'),'creat_time'=>$date,'album_photo'=>$imgname]);
            if ($album === null) {
                $status = 0;
                $message = "新建失败";
            } else {
                $status = 1;
                $message = "新建成功";
            }

            return ["status" => $status, "message" => $message];
        }
        else {

            return ["status" => $status, "message" => $result];
        }
        return ["status" => $status, "message" =>$result];
    }
    public  function  getAlbumlist(Request $request){
        $user_id = $request->param("user_id");
        $album = AlbumModel::all(["user_id"=>$user_id]);
        return ["data"=>$album];
    }



}