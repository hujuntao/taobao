<?php
namespace app\admin\controller;
use app\admin\controller\Base;


class Itemcats extends Base {

	
	public function index() {
		
		$this->assign('title','标准商品类目-控制台');
		$Itemcats = config('Itemcats.item_cat');
		$this->assign('list',$Itemcats);
		$this->assign('page_type','itemcats');
		return $this->fetch( 'index' );
	}
	
	


	
}