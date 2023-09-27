<?php

namespace app\api\controller;

use app\api\controller\Cross;
use app\common\model\RightModel;
use app\common\model\RoleModel;
use app\common\model\RoleRightModel;
use app\common\model\UserModel;
use think\Controller;
use think\Exception;
use think\Request;
use app\api\validate\LoginValidate;
use Firebase\JWT\JWT;

use Firebase\JWT\Key;
class Login extends Cross
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(Request $request)
    {
        try{
            validate(LoginValidate::class)->scene('login')->check($this->request->param());
            $params = $request->param();
            $db = new UserModel();
            $result = $db->field('password',true)->where("username='{$params['username']}' AND password='{$params['password']}'")->find();

            if(!empty($result)){
                $key = '!@#$%*&';         //这里是自定义的一个随机字串，应该写在config文件中的，解密时也会用，相当于加密中常用的 盐  salt
                $token = array(
                    "iss" => $key,        //签发者 可以为空
                    "aud" => '',          //面象的用户，可以为空
                    "iat" => time(),      //签发时间
                    "nbf" => time() + 3,  //在什么时候jwt开始生效  （这里表示生成100秒后才生效）
                    "exp" => time() +200, //token 过期时间
                    "user_id" => $result['id']       //记录的userid的信息，这里是自已添加上去的，如果有其它信息，可以再添加数组的键值对
                );
                $jwt = JWT::encode($token, $key, 'HS256');
                $result['token'] = $jwt;
                $result ['rights'] = $this->getRight($result['id']);;
                $this->response(200,"登录成功",$result);
            }else{
                $this->response(401,"用户名或者密码错误");
            }
        }catch (Exception $exception){
            return $this->response(400,"请输入完整的信息",$exception->getMessage());
        }
    }
    protected function getRight(int $user_id){
        $db = new UserModel();
        $result =  $db->field('role_id')->where('id',$user_id)->find();
//        拿到了角色是1.现在要去角色权限表找一下有什么权限
        $db2 = new RoleRightModel();
        $result2 = $db2->field('right_id')->where('role_id',$result['role_id'])->select();

        // 权限id拿到了，看看权限是什么
        $db = new RightModel();
        $result = [];
        foreach($result2 as $key => $value) {
            $item = $db->where("id=$value->right_id")->find();
            array_push($result,$item);
        }

        $result3 =  $this->digui($result,$user_id);

        return $result3;

    }
    protected function digui($allResult,$user_id){
        $firstStepResult = [];
        foreach($allResult as $key => $allResultValue) {
            if($allResultValue->floor_step == 0){
                array_push($firstStepResult,$allResultValue);
            }

            foreach ($firstStepResult as $key2 =>$firstResultValue){
                $secondResult = [];
                foreach ($allResult as $key=>$allResultValue){
                    if($allResultValue->floor_step == 1 && $allResultValue->parent_id === $firstResultValue->id){
                        array_push($secondResult,$allResultValue);
                    }
                    $firstResultValue['children']= $secondResult;
                }
            }

        }
//       if($user_id!=11){
//           array_unshift($firstStepResult, array_pop($firstStepResult));
//       }
        return $firstStepResult;
    }

}
