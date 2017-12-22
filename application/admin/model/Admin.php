<?php
namespace app\admin\model;

use think\Model;

class Admin extends Model
{
	protected $pk = 'id';
	
    public static function getUser($username){
		return self::get(['username' => $username]);
	}
	
	public static function addUser($username,$password,$ip){
		$user = self::create([
			'username'  =>  $username,
    		'password' =>  md5($password),
			'role' => 1,
			'ip' => $ip
		]);
		return $user->id;
	}
}


?>