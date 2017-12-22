<?php
namespace app\admin\model;
use think\Model;
use app\admin\model\Items;
class ItemsCate extends Model
{
    protected $pk = 'id';
	// 开启自动写入时间戳字段
	protected $autoWriteTimestamp = 'timestamp';

	public static function getCateList($data=array()){
		if(empty($data)){
			$data['data'][]= ['id','>',0];
			$data['list_rows']=15;
		}
		if(!isset($data['list_rows'])){
			$data['list_rows']=15;
		}
		$list = self::where($data['data'])->paginate($data['list_rows'])->each(function($item, $key){
			
			$item_count  = Items::where('cate_id',$item['id'])->count();
    		$item['item_count'] = $item_count;
			return $item;
			});
		$page =  $list->render();
		return array(
			'list'=> $list,
			'page'=> $page
		);
	}
	
	
	public static function getCate($data=array()){
		if(!empty($data)){
			return self::get($data);
		}
	}
	
	public static function addCate($cate_name,$cate_icon,$category){
		if(!empty($cate_name) && empty(self::getCate(array('cate_name'=>$cate_name)))){
			$user = self::create([
				'cate_name'  =>  $cate_name,
    			'cate_icon' =>  $cate_icon,
				'category' => $category
			]);
			return $user->id;
		}
	}
	
	public static function editCate($id,$cate_name,$cate_icon,$category){
		if(!empty($id) && !empty(self::getCate(array('id'=>$id)))){
			$user = self::update([
				'id'=>$id,
				'cate_name'  =>  $cate_name,
    			'cate_icon' =>  $cate_icon,
				'category' => $category
			]);
			return $user->id;
		}
	}
}


?>