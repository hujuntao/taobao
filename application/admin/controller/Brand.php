<?php
namespace app\admin\controller;
use app\admin\controller\Base;
use app\admin\Model\Brand as bd;
use app\admin\model\Items;
use app\admin\model\ItemsCate;


class Brand extends Base {

	
	public function index() {
		
		
		$this->assign('title','品牌-控制台');
		$this->assign('page_type','brand');
		$this->assign('brand_edit_url',url('admin/brand/edit',"",""));
		$this->assign('brand_del_url',url('admin/brand/del',"",""));
		$this->assign('addUrl',url('admin/brand/add',"",""));
		
		
		$this->assign('brand_name','');
		
		
		// 查询状态为1的用户数据 并且每页显示10条数据
		$list = bd::where('status',1)->paginate(10)->each(function($item, $key){
    			$item->category = implode(",", ItemsCate::all($item->cate_id)->column('cate_name'));
		});
		// 获取分页显示
		$page = $list->render();
		// 模板变量赋值
		$this->assign('list', $list);
		$this->assign('page', $page);
		
		return $this->fetch( 'index' );
	}
	
	
	public function add() {
		$this->assign('id','');
		$this->assign('brand_activity_url','');
		$this->assign('brand_id','');
		$this->assign('brand_name','');
		$category = ItemsCate::all();
		$this->assign('category',$category);
		$this->assign('brand_logo','');
		$this->assign('brand_enter_banner','');
		$this->assign('brand_banner','');
		$this->assign('brand_thumb','');
		$this->assign('brand_description','');
		$this->assign('seller_id','');
		$this->assign( 'ajaxUrl', url( 'admin/brand/getItem', "", "" ) );
		$this->assign('checkUrl', url( 'admin/brand/check', "", "" ) );
		$this->assign('brandUrl', url( 'admin/brand/index', "", "" ) );

		$this->assign('title','添加品牌-控制台');
		$this->assign('brand_title','添加品牌');
		$this->assign('page_type','brand');
		return $this->fetch( 'add' );
	}
	
	
		
	/**
	 * 采集品牌商品
	 * @access public
	 * @return mixed
	 */
	public function collect() {
		$this->assign( 'title', '采集品牌商品-控制台' );
		$this->assign( 'page_type', 'collect' );
		$this->assign( 'item_collect_url', url( 'admin/brand/getItems', "", "" ) );
		$this->assign( 'cate_name', '' );

		// 查询状态为1的用户数据 并且每页显示10条数据
		$list = bd::where('status',1)->paginate(10)->each(function($item, $key){
    			$item->category = implode(",", ItemsCate::all($item->cate_id)->column('cate_name'));
				$item_count  = Items::where(['brand_id'=>$item->id])->count();
    		    $item->item_count = $item_count;
		});
		
		// 获取分页显示
		$page = $list->render();
		// 模板变量赋值
		$this->assign('list', $list);
		$this->assign('page', $page);
		

		return $this->fetch( 'collect' );


	}
		
	/**
     * @param string        $id 品牌id
     * @return object
     * @route('del/:id')
     */
	public function del($id){
		if(bd::destroy($id)){
			return ['code'=>0];
		}else{
			return ['code'=>1];
		}
	}
	/**
     * @param string        $id 品牌id
     * @return mixed
     * @route('edit/:id')
     */
	public function edit($id) {
		
		if(!empty($id)){
			$brand=bd::get($id);
		
			//已存在
			if(!empty($brand)){
				$this->assign('title','编辑品牌-控制台');
				$this->assign('brand_title','编辑品牌');
				$this->assign('id',$id);
				$this->assign('brand_activity_url','');
				$this->assign('brand_id',$brand->brand_id);
				$this->assign('brand_name',$brand->brand_name);
				$cate_id = explode(',',$brand->cate_id);
				
		
				$category = ItemsCate::all();
				foreach($category as $key=>$cate){
    				if(in_array($cate->id, $cate_id)){
						$category[$key]->selected=true;
					}
				}
				
				
				$this->assign('category',$category);
				$this->assign('brand_logo',$brand->brand_logo);
				$this->assign('brand_enter_banner',$brand->brand_enter_banner);
				$this->assign('brand_banner',$brand->brand_banner);
				$this->assign('brand_thumb',$brand->brand_thumb);
				$this->assign('brand_description',$brand->brand_description);
				$this->assign('seller_id',$brand->seller_id);
				$this->assign( 'ajaxUrl', url( 'admin/brand/getItem', "", "" ) );
				$this->assign('checkUrl', url( 'admin/brand/check', "", "" ) );
				$this->assign('brandUrl', url( 'admin/brand/index', "", "" ) );
				
				$this->assign('page_type','brand');
				return $this->fetch('add');
			}else{
				$this->error('未查询到数据！');
			}
			
		}else{
			$this->error('你又淘气了！');
		}

		
		
	}
	
	
	/**
     * 获取品牌信息
     * @access public
     * @return object
     */
	
	public function getItem(){
		$url = $this->request->post('url', '', 'trim');
		$pattern = '/activityId=(\d+)/';
		preg_match( $pattern, $url, $match );
		$data = array();
		if(!empty($match[1])){
			$activityId= $match[1];
			$json = file_get_contents('http://ju.taobao.com/json/tg/ajaxGetBrandsV2.json?brandIds='.$activityId);
			$json =  json_decode($json);
			if(!empty($json->brandList[0])){
				$baseInfo = $json->brandList[0]->baseInfo;
				$materials = $json->brandList[0]->materials;
				$extend =$materials->extend;
			
				$data = array(
					'seller_id'  => $baseInfo->sellerId,
					'brand_id' => $baseInfo->brandId,
					'brand_name' => $baseInfo->brandName,
					'brand_description' => $materials->brandDesc,
					'brand_logo' => $materials->brandLogoUrl,
					'brand_thumb' => $materials->enterImgUrl,
					'brand_enter_banner' => $materials->newBannerImgUrl,
					'brand_banner' => '//gju2.alicdn.com/tps/'.$extend->newBrandEnterImgUrl

				);
				
				
			}
			
			
			
		}
		return $data;
		
		
	}
	
	
	
	
	/**
     * 验证新增/编辑品牌数据
     * @access public
     * @return object
     */
	public function check(){
		
		if ($this->request->isPost() && $this->request->isAjax()){
			
			
			$req = [
				'id' => $this->request->post('id', '', 'trim'),
				'seller_id' => $this->request->post('seller_id', '', 'trim'),
				'brand_id' => $this->request->post('brand_id', '', 'trim'),
				'brand_name' => $this->request->post('brand_name', '', 'trim'),
				'brand_description' => $this->request->post('brand_description', '', 'trim'),
				'brand_logo' => $this->request->post('brand_logo', '', 'trim'),
				'brand_thumb' => $this->request->post('brand_thumb', '', 'trim'),
				'brand_enter_banner' => $this->request->post('brand_enter_banner', '', 'trim'),
				'brand_banner' => $this->request->post('brand_banner', '', 'trim'),
				'is_recommend' => $this->request->post('is_recommend', '', 'trim'),
				'cate_id' => implode(",", $this->request->post('cate_id/a', '', 'trim')),
				'__token__' => $this->request->post('__token__')
			];
			
			$validate = new \app\admin\validate\Brand;

			//新增品牌
			if(empty($req['id'])){
				unset($req['id']);
				if (!$validate->scene('add')->check($req)) {
					$data['code']=1;
					$data['token']=$this->request->token('__token__', 'sha1');
            		$data['msg']=$validate->getError();
			
				}else{
					$has=bd::get(array('brand_id'=>$req['brand_id']));
					//品牌已存在
					if(!empty($has)){
						$data['code']=2;
						$data['token']=$this->request->token('__token__', 'sha1');
            			$data['msg']='该品牌已存在';
					}else{
						$new = bd::create($req);
						if(!empty($new)){
							$data['code']=0;
            				$data['msg']='';
						}else{
							$data['code']=3;
							$data['token']=$this->request->token('__token__', 'sha1');
            				$data['msg']='新增品牌失败';
						}
						
					}
				}
			}else{
				if (!$validate->scene('edit')->check($req)) {
					$data['code']=1;
					$data['token']=$this->request->token('__token__', 'sha1');
            		$data['msg']=$validate->getError();
				}else{
					$has=bd::get(['id'=>$req['id']]);
					
					//名称已存在
					if(empty($has)){
						$data['code']=2;
						$data['token']=$this->request->token('__token__', 'sha1');
            			$data['msg']='该品牌不存在';
					}else{
						$new = bd::update($req);
				
						if(!empty($new)){
							$data['code']=0;
            				$data['msg']='';
						}else{
							$data['code']=3;
							$data['token']=$this->request->token('__token__', 'sha1');
            				$data['msg']='编辑品牌失败';
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
	 * 品牌商品采集
	 * @access public
	 * @access param int $id
	 * @return object
	 */
	public function getItems( $id ) {
		if ( $this->request->isPost() && $this->request->isAjax() ) {
			$brand = bd::get($id);
			if ( !empty( $id ) && !empty( $brand ) ) {
				$page_no = $this->request->post( 'page_no', '', 'trim' );
				$page_size = $this->request->post( 'page_size', '', 'trim' );
				$q = $this->request->post( 'q', '', 'trim' );
				$appkey = config( 'taobao.app_key' );
				$secret = config( 'taobao.app_secret' );
				$pid = config( 'taobao.pid' );
				$pid = explode( "_", $pid );
				$pid = end( $pid );
				$pattern = "/满(\d?.+)元减(\d?.+)元/";
				$page_no = empty( $page_no ) ? 1 : $page_no;
				$page_size = empty( $page_size ) ? 20 : $page_size;
				$q = empty($q)?$brand['brand_name']:$q;
				/*$cat= ItemsCate::all($brand['cate_id'])->column('category');
				$cat = implode(',',$cat);*/
			
				$c = new \TopClient;
				$c->appkey = $appkey;
				$c->secretKey = $secret;
				$c->format = 'json';

				$req = new \TbkDgItemCouponGetRequest;
				$req->setAdzoneId( $pid );
				//call_user_func_array(array($req, "setCat"), explode(',',$cat));
				//$req->setCat($cat);
				$req->setQ($q);
				$req->setPageSize( $page_size );
				$req->setPageNo( $page_no );
				$resp = $c->execute( $req );
				
		
		
				if ( !empty( $resp->results->tbk_coupon ) ) {
					$lists = $resp->results->tbk_coupon;
					foreach ( $lists as $list ) {
						$num_iid = $list->num_iid;
						$has = Items::get( [ 'num_iid' => $num_iid ] );

						if ( !$has ) {
							$list->brand_id= $id;
							$list->cate_id = $id;
							$coupon_info = $list->coupon_info;
							preg_match( $pattern, $coupon_info, $info );
						
							$list->coupon_start_fee = $info[ 1 ];
							$list->coupon_amount = $info[ 2 ];
							$list->small_images = '';
							$r = Items::create( json_decode( json_encode( $list ), true ) );
						
						}
						unset( $coupon_info, $info, $num_iid, $has );

					}

					return [ 'code' => 0, 'msg' => '' ,'data'=>['page'=>$page_no,'size'=>count( $lists )]];


				} else {
					return [ 'code' => 1, 'msg' => '未采集到数据' ];
				}
			} else {
				return [ 'code' => 2, 'msg' => '采集分类不存在' ];
			}


		} else {
			$this->error( '非法请求' );

		}
	}


	
}