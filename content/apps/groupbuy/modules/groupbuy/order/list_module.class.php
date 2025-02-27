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
 * 订单列表
 * @author royalwang
 */
class groupbuy_order_list_module extends api_front implements api_interface {
    public function handleRequest(\Royalcms\Component\HttpKernel\Request $request) {	
    	
    	if ($_SESSION['user_id'] < 1 ) {
    	    return new ecjia_error(100, 'Invalid session');
    	}
		
    	$user_id = $_SESSION['user_id'];
    	$api_version = $this->request->header('api-version');
    	//判断用户有没申请注销
    	if (version_compare($api_version, '1.25', '>=')) {
    		$account_status = Ecjia\App\User\Users::UserAccountStatus($user_id);
    		if ($account_status == Ecjia\App\User\Users::WAITDELETE) {
    			return new ecjia_error('account_status_error', __('当前账号已申请注销，不可查看此数据！', 'groupbuy'));
    		}
    	}
    	
    	
		$type = $this->requestData('type', '');
		$store_id = $this->requestData('store_id', 0);
		if (!empty($type) && !in_array($type, array('await_pay', 'await_ship', 'shipped', 'finished', 'unconfirmed', 'whole', 'allow_comment'))) {
			return new ecjia_error('invalid_parameter', __('参数无效', 'groupbuy'));
		}
		//type whole全部，await_pay待付款，await_ship待发货，shipped待收货，allow_comment待评价
		$size = $this->requestData('pagination.count', 15);
		$page = $this->requestData('pagination.page', 1);
		$keywords = $this->requestData('keywords');
		$keywords = trim($keywords);
		
		//$type = $type == 'whole' ? '' : $type;
		$type = empty($type) ? '' : $type;
		
		//用户端团购订单展示基础条件
		$filters = [
			'is_delete' 		=> 0,    //订单未删除
			'extension_code'	=> 'group_buy',
		];
		if ($user_id > 0) {
			$filters['user_id']= $user_id;
		}
		if ($store_id > 0) {
			$filters['store_id'] = $store_id;
		}
		if (!empty($keywords)) {
			$filters['keywords'] = $keywords;
		}
		if (!empty($type)) {
			$filters['api_composite_status'] = $type;
		}
		 //排序
        $order_sort = ['order_info.add_time' => 'desc'];
		if ($order_sort) {
			$filters['sort_by'] = $order_sort;
		}
		//分页信息
		$filters['size'] = $size;
		$filters['page'] = $page;
		
		$collection = (new \Ecjia\App\Orders\OrdersSearch\OrdersApiCollection($filters))->getData();
		
		return array('data' => $collection['order_list'], 'pager' => $collection['pager']);
		
		
// 		$options = array('type' => $type, 'store_id' => $store_id, 'page' => $page, 'size' => $size, 'keywords'=> $keywords, 'extension_code' => 'group_buy');
// 		$result = RC_Api::api('orders', 'order_list', $options);
		
// 		if (is_ecjia_error($result)) {
// 			return $result;
// 		}
// 		RC_Loader::load_app_func('admin_goods', 'goods');
// 		$has_deposit = 0;
// 		$order_deposit = 0;
// 		if (!empty($result['order_list'])) {
// 			foreach ($result['order_list'] as $key => $val) {
// 				$val['store_id'] = $val['seller_id'];
// 				$val['store_name'] = $val['seller_name'];
// 				unset($val['seller_id']);
// 				unset($val['seller_name']);
			
// 				if ($val['extension_code'] == 'group_buy' && $val['extension_id'] > 0) {
// 					$group_buy = group_buy_info($val['extension_id']);
// 					if ($group_buy['deposit'] > 0) {
// 						if ($group_buy['is_finished'] == GBS_SUCCEED) {
// 							$has_deposit = 1;
// 						}
// 						$order_deposit = $val['goods_number']*$group_buy['deposit'];
// 					}
// 				} 
// 				$val['has_deposit'] = $has_deposit;
// 				$val['order_deposit'] = $order_deposit;
// 				$val['formated_order_deposit'] = price_format($order_deposit, false);
// 				$val['formated_order_amount'] = price_format($val['order_amount'], false);
// 				$row[] = $val;
// 			}
// 		} else {
// 			$row = [];
// 		}
		
// 		$page_row = new ecjia_page($result['count'], $size, 6, '', $page);
		
		
// 		$pager = array(
// 			'total' => $page_row->total_records,
// 			'count' => $page_row->total_records,
// 			'more'	=> $page_row->total_pages <= $page ? 0 : 1,
// 		);
// 		return array('data' => $row, 'pager' => $pager);
	 }	
}

// end