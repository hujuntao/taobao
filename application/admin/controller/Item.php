<?php
namespace app\admin\controller;
use app\admin\controller\Base;
use app\admin\model\ItemsCate;
use app\admin\model\Items;
use app\admin\Model\Brand;

use Config;

class Item extends Base {

	public function index() {
		$this->assign( 'title', '所有商品-控制台' );
		$this->assign( 'page_type', 'item' );
		$this->assign( 'item_del_url', url( 'admin/item/del', "", "" ) );


		$item_title = $this->request->get( 'item_title', '', 'trim' );
		$data = array();
		if ( !empty( $item_title ) ) {
			$this->assign( 'item_title', $item_title );
			$data[ 'data' ][] = [ 'title', 'like', '%' . $item_title . '%' ];
		} else {
			$this->assign( 'item_title', '' );
		}

		$list = Items::where('status',1)->order('create_time', 'desc')->paginate()->each(function($item, $key){
    		$item->category = ItemsCate::where('FIND_IN_SET("'.$item->category.'", category)')->value('cate_name');
			$item->brand_name= Brand::where('id',$item->brand_id)->value('brand_name');
		});
		// 获取分页显示
		$page = $list->render();
		$this->assign( 'list', $list);
		$this->assign( 'page', $page );

		return $this->fetch( 'index' );
	}
	/**
	 * 采集商品
	 * @access public
	 * @return mixed
	 */
	public function collect() {
		$this->assign( 'title', '采集商品-控制台' );
		$this->assign( 'page_type', 'collect' );



		$this->assign( 'item_collect_url', url( 'admin/item/getItems', "", "" ) );


		$cate_name = $this->request->get( 'cate_name', '', 'trim' );
		$data = array();
		if ( !empty( $cate_name ) ) {
			$this->assign( 'cate_name', $cate_name );
			$data[ 'data' ][] = [ 'cate_name', 'like', '%' . $cate_name . '%' ];
		} else {
			$this->assign( 'cate_name', '' );
		}

		$db = ItemsCate::getCateList( $data );
		$this->assign( 'list', $db[ 'list' ] );
		$this->assign( 'page', $db[ 'page' ] );

		return $this->fetch( 'collect' );


	}

	public function update() {
		$appkey = config( 'taobao.app_key' );
		$secret = config( 'taobao.app_secret' );

		$c = new \TopClient;
		$c->appkey = $appkey;
		$c->secretKey = $secret;
		$c->format = 'json';
		$req = new \TbkItemGetRequest;

		$cates = ItemsCate::all();
		$cates = $cates->toArray();
		foreach ( $cates as $cate ) {

			$req->setFields( "num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url,seller_id,volume,nick" );
			$req->setSort( "tk_rate_des" );
			$req->setQ( $cate[ 'cate_name' ] );
			$req->setPageNo( "1" );
			$req->setPageSize( "50" );
			$resp = $c->execute( $req );
			if ( !isset( $resp->code ) ) {
				$lists = $resp->results->n_tbk_item;
				foreach ( $lists as $list ) {
					$num_iid = $list->num_iid;
					$has = Items::get( [ 'num_iid' => $num_iid ] );

					if ( !$has ) {
						$list->cate_id = $cate[ 'id' ];
						$list->small_images = '';
						Items::create( json_decode( json_encode( $list ), true ) );

					}
					unset( $num_iid );
					unset( $has );

				}
				unset( $lists );
			}
			unset( $resp );


		}

	}

	/**
	 * 商品采集
	 * @access public
	 * @access param int $id
	 * @return object
	 */
	public function getItems( $id ) {
		if ( $this->request->isPost() && $this->request->isAjax() ) {
			$itemsCate = ItemsCate::getCate( array( 'id' => $id ) );
			if ( !empty( $id ) && !empty( $itemsCate ) ) {
				$page_no = $this->request->post( 'page_no', '', 'trim' );
				$page_size = $this->request->post( 'page_size', '', 'trim' );
				$appkey = config( 'taobao.app_key' );
				$secret = config( 'taobao.app_secret' );
				$pid = config( 'taobao.pid' );
				$pid = explode( "_", $pid );
				$pid = end( $pid );
				$pattern = "/满(\d?.+)元减(\d?.+)元/";
				$page_no = empty( $page_no ) ? 1 : $page_no;
				$page_size = empty( $page_size ) ? 20 : $page_size;

				$c = new \TopClient;
				$c->appkey = $appkey;
				$c->secretKey = $secret;
				$c->format = 'json';

				$req = new \TbkDgItemCouponGetRequest;
				$req->setAdzoneId( $pid );
				call_user_func_array(array($req, "setCat"), explode(',',$itemsCate[ 'category' ]));
				//$req->setCat( $itemsCate[ 'category' ] );
				$req->setPageSize( $page_size );
				$req->setPageNo( $page_no );
				$resp = $c->execute( $req );
		
				if ( !isset( $resp->code ) ) {
					$lists = $resp->results->tbk_coupon;
					foreach ( $lists as $list ) {
						$num_iid = $list->num_iid;
						$has = Items::get( [ 'num_iid' => $num_iid ] );

						if ( !$has ) {
							$list->cate_id = $id;
							$coupon_info = $list->coupon_info;
							preg_match( $pattern, $coupon_info, $info );
						
							$list->coupon_start_fee = $info[ 1 ];
							$list->coupon_amount = $info[ 2 ];
							$list->small_images = '';
							Items::create( json_decode( json_encode( $list ), true ) );

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