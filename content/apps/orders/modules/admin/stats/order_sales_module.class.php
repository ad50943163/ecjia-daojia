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
 * 订单统计汇总
 * @author will.chen
 */
class admin_stats_order_sales_module extends api_admin implements api_interface
{
    public function handleRequest(\Royalcms\Component\HttpKernel\Request $request)
    {

        $this->authadminSession();
        if ($_SESSION['staff_id'] <= 0) {
            return new ecjia_error(100, __('Invalid session', 'orders'));
        }
        $device = $this->device;
        $codes  = config('app-cashier::cashier_device_code');
        if (!in_array($device['code'], $codes)) {
            $result = $this->admin_priv('order_stats');
            if (is_ecjia_error($result)) {
                return $result;
            }
        }

        //传入参数
        $start_date = $this->requestData('start_date');
        $end_date   = $this->requestData('end_date');
        if (empty($start_date) || empty($end_date)) {
            return new ecjia_error(101, __('参数错误', 'orders'));
        }

        $cache_key = 'admin_stats_order_sales_' . $_SESSION['store_id'] . '_' . md5($start_date . $end_date);
        $data      = RC_Cache::app_cache_get($cache_key, 'api');
        if (empty($data)) {
            $response = $this->orders_module($start_date, $end_date);
            RC_Cache::app_cache_set($cache_key, $response, 'api', 60);
            //流程逻辑结束
        } else {
            $response = $data;
        }
        return $response;
    }

    private function orders_module($start_date, $end_date)
    {
        $db_orderinfo_view       = RC_Model::model('orders/order_info_viewmodel');
        $db_orderinfo_view->view = array(
            'order_goods' => array(
                'type'  => Component_Model_View::TYPE_LEFT_JOIN,
                'alias' => 'og',
                'on'    => 'oi.order_id = og.order_id'
            )
        );

        $type       = $start_date == $end_date ? 'time' : 'day';
        $start_date = RC_Time::local_strtotime($start_date . ' 00:00:00');
        $end_date   = RC_Time::local_strtotime($end_date . ' 23:59:59');

        $where = array();
        if (isset($_SESSION['store_id']) && $_SESSION['store_id'] > 0) {
            /*入驻商*/
            $where['store_id'] = $_SESSION['store_id'];
        }
        $where['oi.pay_status'] = 2;
        $member_orders          = 0;//会员数量
        $anonymity_orders       = 0;//非会员数量

        $field = 'count(oi.order_id) as count, SUM(IF(oi.user_id > 0, 1, 0)) as member_orders, SUM(oi.discount) as discount, SUM(oi.bonus) as bonus, SUM(oi.integral_money) as integral_money, 
			SUM(oi.goods_amount - oi.discount + oi.tax + oi.shipping_fee + oi.insure_fee + oi.pay_fee + oi.pack_fee + oi.card_fee) AS total_fee';

        /* 判断是否是入驻商*/
        if (isset($_SESSION['store_id']) && $_SESSION['store_id'] > 0) {
            $join = array('order_goods');
        } else {
            $join = null;
        }

        $result = $db_orderinfo_view->join($join)->field($field)->where($where)->find();

        $data = array(
            'orders'                       => $result['count'],
            'total_sales_volume'           => $result['total_fee'],
            'member_orders'                => $result['member_orders'],
            'anonymity_orders'             => $result['count'] - $result['member_orders'],
            'discount_money'               => $result['discount'],
            'bonus_money'                  => $result['bonus'],
            'integral_money'               => $result['integral_money'],
            'formatted_total_sales_volume' => price_format($result['total_fee'], false),
            'formatted_discount_money'     => price_format($result['discount'], false),
            'formatted_bouns_money'        => price_format($result['bouns'], false),
            'formatted_integral_money'     => price_format($result['integral_money'], false),
        );

        return $data;
    }
}

//end
