<?php
namespace app\admin\controller;
use app\admin\controller\Base;
use app\admin\Model\ItemsCate;

class Category extends Base {

	
	public function index() {
		$this->assign('title','分类-控制台');
		$this->assign('page_type','category');
		$this->assign('cate_edit_url',url('admin/category/update',"",""));
		$this->assign('cate_del_url',url('admin/category/del',"",""));
		$this->assign('addUrl',url('admin/category/add',"",""));
		
		$cate_name = $this->request->get('cate_name', '', 'trim');
		$data = array();
		if(!empty($cate_name)){
			$this->assign('cate_name',$cate_name);
			$data['data'][]=['cate_name','like','%'.$cate_name.'%'];
		}else{
			$this->assign('cate_name','');
		}
		
		$db = ItemsCate::getCateList($data);
		$this->assign('list',$db['list']);
		$this->assign('page',$db['page']);
		return $this->fetch( 'index' );
	}
	public function add() {
		$ajaxUrl = url('admin/category/check',"","");
		$cateUrl = url('admin/category/index',"","");
		$this->assign('ajaxUrl',$ajaxUrl);
		$this->assign('cateUrl',$cateUrl);
		$this->assign('title','新增分类-控制台');
		$this->assign('cate_title','新增分类');
		$this->assign('cate_id','');
		$this->assign('cate_name','');
		$this->assign('cate_icon','');
		
		$itemcats = $this->getItemcats();
		$this->assign('itemcats',$itemcats);
		
		$this->assign('page_type','category');
		return $this->fetch( 'add' );
	}

	
	public function update($id) {
		
		if(!empty($id)){
			$cate=ItemsCate::getCate(array('id'=>$id));
			//已存在
			if(!empty($cate)){
				$this->assign('title','编辑分类-控制台');
				$this->assign('cate_title','编辑分类');
				$ajaxUrl = url('admin/category/check',"","");
				$cateUrl = url('admin/category/index',"","");
				$category = $cate['category'];
				
				$category = explode(',',$cate['category']);
		
				$itemcats = $this->getItemcats($category);
		
				$this->assign('itemcats',$itemcats);
				$this->assign('ajaxUrl',$ajaxUrl);
				$this->assign('cateUrl',$cateUrl);
				$this->assign('cate_id',$cate['id']);
				$this->assign('cate_name',$cate['cate_name']);
				$this->assign('cate_icon',$cate['cate_icon']);
				$this->assign('page_type','category');
				return $this->fetch('add');
			}else{
				$this->error('未查询到数据！');
			}
			
		}else{
			$this->error('你又淘气了！');
		}

		
		
	}
	
	public function check(){
		
		if ($this->request->isPost() && $this->request->isAjax()){
			$id = $this->request->post('id', '', 'trim');
			$cate_name = $this->request->post('cate_name', '', 'trim');
			$cate_icon = $this->request->post('cate_icon', '', 'trim');
			$category = $this->request->post('category/a', '', 'trim');
			$category  = empty($category)?'':  implode(",",$category);
		
			$token = $this->request->post('__token__');
			
			$validate = new \app\admin\validate\Category;

			//新增分类
			if(empty($id)){
				if (!$validate->scene('add')->check(array('cate_name'=>$cate_name,'cate_icon'=>$cate_icon,'category'=>$category,'__token__'=>$token))) {
					$data['code']=1;
					$data['token']=$this->request->token('__token__', 'sha1');
            		$data['msg']=$validate->getError();
					
			
				}else{
					$cate=ItemsCate::getCate(array('cate_name'=>$cate_name));
					//名称已存在
					if(!empty($cate)){
						$data['code']=2;
						$data['token']=$this->request->token('__token__', 'sha1');
            			$data['msg']='该分类已存在';
					}else{
						$new = ItemsCate::addCate($cate_name,$cate_icon,$category);
						if(!empty($new)){
							$data['code']=0;
            				$data['msg']='';
						}else{
							$data['code']=3;
							$data['token']=$this->request->token('__token__', 'sha1');
            				$data['msg']='新增分类失败';
						}
						
					}
				}
			}else{
				if (!$validate->scene('edit')->check(array('id'=>$id,'cate_name'=>$cate_name,'cate_icon'=>$cate_icon,'category'=>$category,'__token__'=>$token))) {
					$data['code']=1;
					$data['token']=$this->request->token('__token__', 'sha1');
								
            		$data['msg']=$validate->getError();
				}else{
					$cate=ItemsCate::getCate(array('id'=>$id));
					
					//名称已存在
					if(empty($cate)){
						$data['code']=2;
						$data['token']=$this->request->token('__token__', 'sha1');
            			$data['msg']='该分类不存在';
					}else{
						$new = ItemsCate::editCate($id,$cate_name,$cate_icon,$category);
				
						if(!empty($new)){
							$data['code']=0;
            				$data['msg']='';
						}else{
							$data['code']=3;
							$data['token']=$this->request->token('__token__', 'sha1');
            				$data['msg']='编辑分类失败';
						}
						
					}
				}
			}
			
			
			return $data;
		}else{
			$this->error('非法请求');
		}
		
	}
	 /**
     * 获取商品类目
     * @access private
	 * @param array $select 
     * @return array
     */
	
	 private function getItemcats($select = array()) {
		$Itemcats = config('Itemcats.item_cat');
		 if(!empty($select)){
			 foreach($select as $arr){
				 foreach( $Itemcats as $key => $itemcat){
					 if($itemcat['cid']==$arr){
						 $Itemcats[$key]['selected']=true;
						  break;
					 }
					 
				 }
				 
			 }
		 }
        return $Itemcats;
    }


	
}