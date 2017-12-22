<?php
namespace app\admin\model;

use think\Model;
use app\admin\Model\ItemsCate;

class Items extends Model
{
     protected $pk = 'id';
	// 开启自动写入时间戳字段
	protected $autoWriteTimestamp = 'timestamp';

	public static function getItemList($data=array()){
		if(empty($data)){
			$data['data'][]= ['id','>',0];
			$data['list_rows']=15;
		}
		if(!isset($data['list_rows'])){
			$data['list_rows']=15;
		}
		$list = self::where($data['data'])->paginate($data['list_rows'])->each(function($item, $key){
			$cate  = ItemsCate::getCate(array('id'=>$item['cate_id']));
    		$item['category'] = $cate['cate_name'];
			return $item;
			});
		
		$page =  $list->render();
		return array(
			'list'=> $list,
			'page'=> $page
		);
	}
		
}


?>