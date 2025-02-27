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
 * 后台搜索商品返回商品列表
 * @author will.chen
 */
class goods_get_goods_list_api extends Component_Event_Api {
    /**
     * @param  $options['keyword'] 关键字
     *         $options['cat_id'] 分类id
     *         $options['brand_id'] 品牌id
     *
     * @return array | ecjia_error
     */
	public function call(&$options) {	
	    if (!is_array($options) 
	        || ((isset($options['keyword']) && empty($options['keyword'])) 
	        && !isset($options['cat_id']) 
	        && !isset($options['brand_id']))) {
	        return new ecjia_error('invalid_parameter', '参数无效');
	    }
	   	$row = $this->get_goods_list($options);
	    return $row;
	}
	
	/**
	 * 取得商品列表：用于把商品添加到组合、关联类、赠品类
	 * @param   object  $filters    过滤条件
	 */
	private function get_goods_list($filter) {
		$db = RC_Model::model('goods/goods_model');
		RC_Loader::load_app_func('admin_category', 'goods');
		$time = RC_Time::gmtime();

		$where = array();
		$where['is_delete'] = (isset($filter['is_delete']) && $filter['is_delete'] == '1') ? 1 : 0;
		$where[] = "(extension_code is null or extension_code='')";
		
		if (!empty($filter['store_id'])) {
		    $where['store_id'] = $filter['store_id'];
		}
		if (isset($filter['real_goods']) && $filter['real_goods'] > -1) {
			$where['is_real'] = intval($filter['real_goods']);
		}
		if (isset($filter['cat_id']) && $filter['cat_id'] > 0) {
			$where['cat_id'] = array_unique(array_merge(array($filter['cat_id']), array_keys(cat_list($filter['cat_id'], 0, false ))));
		}
		if (isset($filter['brand_id']) && $filter['brand_id'] > 0) {
			$where['brand_id'] = $filter['brand_id'];
		}
		if (isset($filter['intro_type']) && $filter['intro_type'] != '0') {
			$where['intro_type'] = 1;
		}
		if (isset($filter['intro_type']) && $filter['intro_type'] == 'is_promote') {
			$where['promote_start_date'] = array('elt' => $time);
			$where['promote_end_date'] = array('egt' => $time);
		}
		if (isset($filter['keyword']) && trim($filter['keyword']) != '') {
			$where[] = "(goods_name LIKE '%" . $filter['keyword'] . "%' OR goods_sn LIKE '%" . $filter['keyword'] . "%' OR goods_id LIKE '%" . $filter['keyword'] . "%') ";
		}
		if (isset($filter['suppliers_id']) && trim($filter['suppliers_id']) != '') {
			$where['suppliers_id'] = $filter['suppliers_id'];
		}
		if (isset($filter['in_ids'])) {
			$where['goods_id'] = $filter['in_ids'];
		}
		if (isset($filter['exclude'])) {
			$where[] = 'goods_id ' . db_create_in($filter['exclude']);
		}
		if (isset($filter['stock_warning'])) {
			$where[] = 'goods_number <= warn_number';
		}
		if (isset($filter['is_on_sale'])) {
			$where['is_on_sale'] = $filter['is_on_sale'];
		}
        if (isset($filter['review_status'])) {
            $where['review_status'] = $filter['review_status'];
        }

		/* 取得数据 */
		$row = $db->field('goods_id, goods_name, shop_price')->where($where)->limit(50)->select();
		return $row;
	}
}

// end