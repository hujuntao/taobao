<?php
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Admin;

class Register extends Controller {
	/**
     * 控制器基础方法
     */
    public function _initialize()
    {
		//设置过滤方法
        $this->request->filter(['strip_tags', 'htmlspecialchars']);
      
    }
	
	public function index() {
		$ajaxUrl = url('admin/register/check',"","");
		$loginUrl = url('admin/login/index',"","");
		$this->assign('ajaxUrl',$ajaxUrl);
		$this->assign('loginUrl',$loginUrl);
		return $this->fetch( 'register' );
	}
	
	
	public function check(){
		
		if ($this->request->isPost() && $this->request->isAjax()){
			$username = $this->request->post('username', '', 'trim');
			$password = $this->request->post('password', '', 'trim');
			$password_confirm = $this->request->post('password_confirm', '', 'trim');
			$token = $this->request->post('__token__');

			$ip =$this->request->ip();
			$data = array();
			
			$validate = new \app\admin\validate\Login;
			if (!$validate->scene('login')->check(array('username'=>$username,'password'=>$password,'password_confirm'=>$password_confirm,'__token__'=>$token))) {
				$data['code']=1;
				
            	$data['msg']=$validate->getError();
			
			}else{
				$user = Admin::getUser($username);
				if(!empty($user)){
					$data['code']=2;
            		$data['msg']='该帐号已注册';
				}else{
					
					$new = Admin::addUser($username,$password,$ip);
					if(!empty($new)){
						$data['code']=0;
            			$data['msg']='';
					}else{
						$data['code']=3;
            			$data['msg']='注册失败';
					}
				}
			}
			
			return $data;
		}else{
			$this->error('非法请求');
		}
		
	}

	
}