<?php

namespace app\api\validate;

use think\Validate;


class LoginValidate extends Validate
{
   protected $rule = [
       'username'=>'require',
       'password'=>'require'
   ];
   protected $message = [
       'username.require'=>'必须输入用户名',
       'password.require'=>'必须输入密码'
   ];
    /**
     * 设置验证场景
     * @var string[][]
     */
    protected $scene = [
        'login' => ['username', 'password' ],

    ];

}
