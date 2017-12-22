<?php
namespace app\admin\controller;
use app\admin\controller\Base;


class Index extends Base {

	
	public function index() {
		
		$this->assign('title','控制台');
		$this->assign('page_type','main');
		return $this->fetch( 'index' );
	}
	
	


	
}