<?php
namespace app\admin\validate;

use think\Validate;

class Category extends Validate
{
    protected $rule =   [
     	'cate_name'   => ['require', 'max' => '6','token'=>'__token__'],
	 	'cate_icon'   => ['require', 'alpha'],
		'category'  => ['require'],
		'id'  => ['require']
    ];
    
    protected $message  =   [
		'category.require' => '请选择商品类目',
		'cate_name.require' => '请输入分类名称',
        'cate_name.max'     => '名称最多不能超过6个字符',
		'cate_icon.require' => '请输入分类图标',
        'cate_icon.alpha'   => '分类图标必须是字母'
    ];
	
	protected $scene = [
        'add'  =>  ['cate_name','cate_icon','category'],
		'edit'  =>  ['cate_name','cate_icon','category','id']
    ];
    
}