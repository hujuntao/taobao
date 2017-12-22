<?php
namespace app\ admin\ controller;
use think\ Controller;


class Base extends Controller {
	/**
	 * 控制器基础方法
	 */
	public function initialize() {
		$this->is_login();
		$nav = [
			'category' => [ 'index' => url( 'admin/category/index', "", "" ), 'add' => url( 'admin/category/add', "", "" ) , 'standard' => url( 'admin/itemcats/index', "", "" ) ],
			'item' => [ 'index' => url( 'admin/item/index', "", "" ), 'collect' => url( 'admin/item/collect', "", "" ) ],
			'brand' => [ 'index' => url( 'admin/brand/index', "", "" ),'add' => url( 'admin/brand/add', "", "" ), 'collect' => url( 'admin/brand/collect', "", "" ) ]
		];
		$this->assign( 'nav', $nav );
	}




	public function is_login() {
		if ( !session( '?user' ) ) {
			$this->redirect( 'admin/login/index' );
		}

	}


}