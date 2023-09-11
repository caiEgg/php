<?php

namespace app\api\controller;

use app\api\controller\CheckToken;
use think\Controller;
use think\Request;

class Home extends CheckToken
{
    public function index(){


        $this->response(200,'成功',$this->user_id);
    }
}
