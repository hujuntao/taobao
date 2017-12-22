<?php
namespace app\admin\validate;

use think\Validate;

class Brand extends Validate
{
    protected $rule =   [
		'id' => ['require', 'number'],
		'seller_id' => ['require', 'number'],
     	'brand_id'   => ['require', 'number','token'=>'__token__'],
	 	'brand_name'   => ['require'],
		'brand_description'  => ['require'],
		'brand_logo'  => ['require'],
		'brand_thumb'  => ['require'],
		'brand_banner'  => ['require'],
		'brand_enter_banner'  => ['require'],
		'is_recommend'  => ['require'],
		'cate_id'  => ['require']
    ];
    
    protected $message  =   [
    ];
	
	protected $scene = [
        'add'  =>  ['seller_id','brand_id','brand_name','brand_description','brand_logo','brand_thumb','brand_banner','brand_enter_banner','is_recommend','cate_id'],
		'edit'  =>  ['seller_id','brand_id','brand_name','brand_description','brand_logo','brand_thumb','brand_banner','brand_enter_banner','is_recommend','cate_id','id']
    ];
    
}