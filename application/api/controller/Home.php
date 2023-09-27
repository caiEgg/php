<?php

namespace app\api\controller;

use app\api\controller\CheckToken;
use think\Controller;
use think\Request;

class Home extends CheckToken
{
    public function index(){

//        首页顶部数据
        $list = ['totalAmount'=>90946,'todaySale'=>46565,'totalSeeCount'=>61839,'todaySee'=>1842,'totalSave'=>87894,'todaySave'=>3322,'totalPay'=>889540,'todayPay'=>8310];

        $this->response(200,'成功',$list);
    }
}
