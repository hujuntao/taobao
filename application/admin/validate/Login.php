<?php
namespace app\admin\validate;

use think\Validate;

class Login extends Validate
{
    protected $rule =   [
     'username'  => ['require', 'length' => '6,180','token'=>'__token__'],
     'password'   => ['require', 'length' => '6,180','confirm'],
	 'password_confirm'   => ['require', 'length' => '6,180']
    ];
    
    protected $message  =   [
        'username.require' => '请输入账号',
        'username.length'     => '账号为6~18个字符',
		'password.require' => '请输入密码',
        'password.length'     => '密码为6~18个字符',
		'password_confirm.require' => '请再次输入密码',
        'password_confirm.length'   => '密码为6~18个字符',
        'password.confirm'   => '两次密码输入不一致'
    ];
	
	protected $scene = [
        'login'  =>  ['username','password'=> ['require', 'length' => '6,180']],
		'register'  =>  ['username','password','password_confirm']
    ];
    
}