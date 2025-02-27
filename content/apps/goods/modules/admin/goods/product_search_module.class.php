<?php
//
//    ______         ______           __         __         ______
//   /\  ___\       /\  ___\         /\_\       /\_\       /\  __ \
//   \/\  __\       \/\ \____        \/\_\      \/\_\      \/\ \_\ \
//    \/\_____\      \/\_____\     /\_\/\_\      \/\_\      \/\_\ \_\
//     \/_____/       \/_____/     \/__\/_/       \/_/       \/_/ /_/
//
//   上海商创网络科技有限公司
//
//  ---------------------------------------------------------------------------------
//
//   一、协议的许可和权利
//
//    1. 您可以在完全遵守本协议的基础上，将本软件应用于商业用途；
//    2. 您可以在协议规定的约束和限制范围内修改本产品源代码或界面风格以适应您的要求；
//    3. 您拥有使用本产品中的全部内容资料、商品信息及其他信息的所有权，并独立承担与其内容相关的
//       法律义务；
//    4. 获得商业授权之后，您可以将本软件应用于商业用途，自授权时刻起，在技术支持期限内拥有通过
//       指定的方式获得指定范围内的技术支持服务；
//
//   二、协议的约束和限制
//
//    1. 未获商业授权之前，禁止将本软件用于商业用途（包括但不限于企业法人经营的产品、经营性产品
//       以及以盈利为目的或实现盈利产品）；
//    2. 未获商业授权之前，禁止在本产品的整体或在任何部分基础上发展任何派生版本、修改版本或第三
//       方版本用于重新开发；
//    3. 如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回并承担相应法律责任；
//
//   三、有限担保和免责声明
//
//    1. 本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的；
//    2. 用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未获得商业授权之前，我们不承
//       诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任；
//    3. 上海商创网络科技有限公司不对使用本产品构建的商城中的内容信息承担责任，但在不侵犯用户隐
//       私信息的前提下，保留以任何方式获取用户信息及商品信息的权利；
//
//   有关本产品最终用户授权协议、商业授权与技术服务的详细内容，均由上海商创网络科技有限公司独家
//   提供。上海商创网络科技有限公司拥有在不事先通知的情况下，修改授权协议的权力，修改后的协议对
//   改变之日起的新授权用户生效。电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和
//   等同的法律效力。您一旦开始修改、安装或使用本产品，即被视为完全理解并接受本协议的各项条款，
//   在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本
//   授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。
//
//  ---------------------------------------------------------------------------------
//
defined('IN_ECJIA') or exit('No permission resources.');

/**
 * 搜索商品（搜索商品）
 * @author luchongchong
 */
class admin_goods_product_search_module extends api_admin implements api_interface {
    public function handleRequest(\Royalcms\Component\HttpKernel\Request $request) {

		$this->authadminSession();
		if ($_SESSION['store_id'] <= 0 && $_SESSION['staff_id'] <= 0) {
			return new ecjia_error(100, 'Invalid session');
		}

    	$goods_sn	= trim($this->requestData('goods_sn',''));
    	if (empty($goods_sn)) {
    		return new ecjia_error('invalid_parameter', __('参数错误', 'goods'));
    	}
    	$size = $this->requestData('pagination.count', 15);
    	$page = $this->requestData('pagination.page', 1);

		$device		  = $this->device;
    	$device_code = $device['code'];
		
    	
    	//用户端商品展示基础条件
    	$filters = [
	    	'is_delete'		 		=> 0,	 //未删除的
	    	'is_on_sale'	 		=> 1,    //已上架的
	    	'is_alone_sale'	 		=> 1,	 //单独销售的
	    	'review_status'  		=> 2,    //审核通过的
	    	'no_need_cashier_goods'	=> true, //不需要收银台商品和散装商品
    	];
    	
    	//是否展示货品
    	if (ecjia::config('show_product') == 1) {
    		$filters['product'] = true;
    	}
    	//店铺id
    	if(!empty($_SESSION['store_id'])){
    		$filters['store_id'] = $_SESSION['store_id'];
    	}
    	//商品货号或货品号
    	$filters['goods_sn_or_product_sn'] = $goods_sn;
    	
    	//会员等级价格
    	$filters['user_rank'] = $_SESSION['user_rank'];
    	$filters['user_rank_discount'] = $_SESSION['discount'];
    	
    	//分页信息
    	$filters['size'] = $size;
    	$filters['page'] = $page;
    	
    	$collection = (new \Ecjia\App\Goods\GoodsSearch\GoodsApiCollection($filters))->getData();
    	
    	//字段处理
    	$goods_list=[];
    	if (!empty($collection['goods_list'])) {
    		foreach ($collection['goods_list'] as $row) {
    			$row['shop_price'] 			= $row['unformatted_shop_price'];
    			$row['formatted_shop_price']= $row['shop_price'];
    			$goods_list[] = $row;
    		}
    	}
    	
    	return array('data' => $goods_list, 'pager' => $collection['pager']);
    	
// 		$db_goods = RC_Model::model('goods/goods_viewmodel');

// 		$db_goods->view = array(
// 			'products' => array(
// 				'type'		=> Component_Model_View::TYPE_LEFT_JOIN,
// 				'alias'		=> 'p',
// 				'on'		=> 'g.goods_sn=p.product_sn'
// 			)
// 		);

// 		$field = 'g.goods_id, g.goods_name, g.shop_price, g.shop_price, g.goods_sn, g.goods_img, g.original_img, g.goods_thumb, p.goods_attr, p.product_sn';

//     	$where[] = "(goods_sn like '%".$goods_sn."%' OR  product_sn like '%".$goods_sn."%')";
//     	//散装商品不支持搜索；只能扫码
//     	$where[] = "(g.extension_code is null or g.extension_code ='')";
    	
//         if(!empty($_SESSION['store_id'])){
//             $where['store_id'] = $_SESSION['store_id'];
//         }
//         $codes = config('app-cashier::cashier_device_code');
//     	if (in_array($device_code, $codes)) {
//     		$where = array_merge($where, array('is_delete' => 0, 'is_on_sale' => 1, 'is_alone_sale' => 1));
//     		if (ecjia::config('review_goods')) {
//     			$where['review_status'] = array('gt' => 2);
//     		}
//     	}

//     	$arr = $db_goods->field($field)->join(array('products'))->where($where)->select();
//     	$product_search = array();
// 		if (!empty($arr)) {
// 			foreach ($arr as $k => $v){
// 				$product_search[] = array(
// 					'id'					=> $v['goods_id'],
// 					'name'					=> $v['goods_name'],
// 					'shop_price'			=> $v['shop_price'],
// 					'formatted_shop_price'	=> price_format($v['shop_price']),
// 					'goods_sn'				=> empty($v['product_sn']) ? $v['goods_sn'] : $v['product_sn'],
// 					'attribute'				=> $v['goods_attr'],
// 					'img' => array(
// 						'thumb'	=> !empty($v['goods_img']) ? RC_Upload::upload_url($v['goods_img']) : '',
// 						'url'	=> !empty($v['original_img']) ? RC_Upload::upload_url($v['original_img']) : '',
// 						'small'	=> !empty($v['goods_thumb']) ? RC_Upload::upload_url($v['goods_thumb']) : ''
// 					),
// 	    	 	);
// 			}
// 		}
// 		return $product_search;
    }
}
