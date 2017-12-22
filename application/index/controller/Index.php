<?php
namespace app\index\controller;
use think\Controller;
use app\admin\model\Items;

class Index extends Controller
{
    public function index()
		
    {
		$list = Items::where('status',1)->order('create_time','desc')->limit(20)->select()->each(function($item, $key){
			$price = $item->zk_final_price;
			$coupon_amount = $item->coupon_amount;
			$item->price = $price-$coupon_amount;
    		$item->discount =  round(($item->price)/$price*10,1);
		});
		$this->assign('list',$list);
		return $this->fetch('index');
    }
}
