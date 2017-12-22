<?php
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Admin;

class login extends Controller {
	/**
     * 控制器基础方法
     */
    public function _initialize()
    {
		//设置过滤方法
        $this->request->filter(['strip_tags', 'htmlspecialchars']);
      
    }
	
	public function index() {
		$ajaxUrl = url('admin/login/check',"","");
		$indexUrl = url('admin/index/index',"","");
		$this->assign('username','');
		$this->assign('password','');
		$this->assign('ajaxUrl',$ajaxUrl);
		$this->assign('indexUrl',$indexUrl);

		return $this->fetch( 'login' );
	}
	
	
	public function check(){
		
		if ($this->request->isPost() && $this->request->isAjax()){
			$username = $this->request->post('username', '', 'trim');
			$password = $this->request->post('password', '', 'trim');
			$token = $this->request->post('__token__');
			$data = array();
			
			$validate = new \app\admin\validate\Login;
			if (!$validate->scene('login')->check(array('username'=>$username,'password'=>$password,'__token__'=>$token))) {
				$data['code']=1;
				$data['token']=$this->request->token('__token__', 'sha1');
            	$data['msg']=$validate->getError();
			}else{
				$user = Admin::getUser($username);
				if(empty($user)){
					$data['code']=2;
					$data['token']=$this->request->token('__token__', 'sha1');
            		$data['msg']='账号或密码错误';
				}else{
					if(md5($password)!=$user['password']){
						$data['code']=2;
						$data['token']=$this->request->token('__token__', 'sha1');
            			$data['msg']='账号或密码错误';
					}else{
						$data['code']=0;
            			$data['msg']='';
						session('user', $user['username']);
					}
				}
			}
			
			return $data;
		}else{
			$this->error('非法请求');
		}
		
	}

	
}