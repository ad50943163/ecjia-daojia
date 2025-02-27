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
 * 店铺信息
 * @author luchongchong
 */
class admin_merchant_info_module extends api_admin implements api_interface {
    
    public function handleRequest(\Royalcms\Component\HttpKernel\Request $request) {
		$this->authadminSession();
		if ($_SESSION['staff_id'] <= 0) {
		    return new ecjia_error(100, 'Invalid session');
		}
		
    	if($_SESSION['store_id'] > 0) {
			$where = array();
			/* if(substr($info['shop_logo'], 0, 1) == '.') {
				$info['shop_logo'] = str_replace('../', '/', $info['shop_logo']);
			} */
// 			$is_validated = (!empty($info['create_time']) && !empty($info['confirm_time'])) ? 1 : 0;
			
			$info = RC_DB::table('store_franchisee')->where('store_id', $_SESSION['store_id'])->first();
			
			RC_Loader::load_app_func('merchant', 'merchant');
			
			$shop_trade_time = get_store_trade_time($info['store_id']);
			
			$unformat_shop_trade_time = RC_DB::table('merchants_config')->where('store_id', $_SESSION['store_id'])->where('code', 'shop_trade_time')->pluck('value');
			
			$shop_closed = get_shop_close($info['shop_close'], $unformat_shop_trade_time);
			
			/*店长手机号码*/
			$shopkeeper_mobile = RC_DB::table('staff_user')->where('store_id', $_SESSION['store_id'])->where('parent_id', 0)->pluck('mobile');
			
			$seller_info = array(
    	  		'id'					=> $info['store_id'],
    	  		'seller_name'			=> $info['merchants_name'],
				'shop_closed'			=> $shop_closed,
				'shopkeeper_mobile'		=> empty($shopkeeper_mobile) ? '' : $shopkeeper_mobile,
    	  		'seller_logo'			=> RC_Upload::upload_url(RC_DB::table('merchants_config')->where('store_id', $_SESSION['store_id'])->where('code', 'shop_logo')->pluck('value')),
    	  		'seller_category'		=> RC_DB::table('store_category')->where('cat_id', $info['cat_id'])->pluck('cat_name'),
    	  		'seller_telephone'		=> RC_DB::table('merchants_config')->where('store_id', $_SESSION['store_id'])->where('code', 'shop_kf_mobile')->pluck('value'),
    	  		'seller_province'		=> ecjia_region::getRegionName($info['province']),
    	  		'seller_city'			=> ecjia_region::getRegionName($info['city']),
		        'seller_district'		=> ecjia_region::getRegionName($info['district']),
				'seller_street'			=> ecjia_region::getRegionName($info['street']),
    	  		'seller_address'		=> $info['address'],
				'validated_status'		=> $info['identity_status'],
    	  		'seller_description'	=> RC_DB::table('merchants_config')->where('store_id', $_SESSION['store_id'])->where('code', 'shop_description')->pluck('value'),
				'seller_notice'			=> RC_DB::table('merchants_config')->where('store_id', $_SESSION['store_id'])->where('code', 'shop_notice')->pluck('value'),
			    'seller_keyword'        => $info['shop_keyword'],
				'trade_time'			=> $shop_trade_time,
			    'manage_mode'           => $info['manage_mode'],//是否自营
			    'open_time'             => RC_Time::local_date('Y-m-d', $info['confirm_time']),//入驻时间
			);
			$result = $this->admin_priv('franchisee_manage');
			if (is_ecjia_error($result)) {
				$privilege = 1;
// 				Read & Write   3
// 				Read only      1
// 				No Access      0
			} else {
				$privilege = 3;
			}
    	 } else {
			$seller_info = array(
				'id'					=> 0,
    	  		'seller_name'			=> ecjia::config('shop_name'),
    	  		'seller_logo'			=> ecjia_config::has('shop_logo') ? RC_Upload::upload_url().'/'.ecjia::config('shop_logo') : '',
    	  		'seller_category'		=> null,
	    	  		'seller_telephone'		=> ecjia::config('service_phone'),

	    	  		'seller_province'		=> ecjia_region::getRegionName(ecjia::config('shop_province')),
	  			'seller_city'			=> ecjia_region::getRegionName(ecjia::config('shop_city')),
    	
    	  		'seller_address'		=> ecjia::config('shop_address'),
    	  		'seller_description'	=> strip_tags(ecjia::config('shop_notice'))
			);
			$result = $this->admin_priv('shop_config');
			if (is_ecjia_error($result)) {
				$privilege = 1;
			} else {
				$privilege = 3;
			}
		}
		$seller_qrcode = with(new Ecjia\App\Mobile\Qrcode\GenerateMerchant($seller_info['id'], $seller_info['seller_logo']))->getQrcodeUrl();
		$seller_info['seller_qrcode'] = $seller_qrcode. '?'.SYS_TIME;
		
		return array('data' => $seller_info, 'privilege' => $privilege);
    }
}

//end