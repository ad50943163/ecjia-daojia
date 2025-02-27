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
 * 订单数量
 * @author luchongchong
 *
 */
class admin_stats_orders_module extends api_admin implements api_interface
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
            $result = $this->admin_priv('sale_order_stats');
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
        $cache_key = 'admin_stats_orders_' . $_SESSION['store_id'] . '_' . md5($start_date . $end_date);
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

        /* 计算时间刻度*/
        $group_scale = ($end_date + 1 - $start_date) / 6;
        $stats_scale = ($end_date + 1 - $start_date) / 30;
        /* 计算出有多少天*/
        $day = round(($end_date - $start_date) / (24 * 60 * 60));

        $where = array();
        if (isset($_SESSION['store_id']) && $_SESSION['store_id'] > 0) {
            /*入驻商*/
            $where['store_id'] = $_SESSION['store_id'];
        }
        $where['oi.pay_status'] = 2;
        $member_orders          = 0;//会员数量
        $anonymity_orders       = 0;//非会员数量

        $field = 'oi.order_id, oi.pay_time, oi.user_id';

        /* 判断是否是入驻商*/
        if (isset($_SESSION['store_id']) && $_SESSION['store_id'] > 0) {
            $join = array('order_goods');
        } else {
            $join = null;
        }

        $stats           = $group = array();
        $temp_start_time = $start_date;
        $now_time        = RC_Time::gmtime();
        $j               = 1;
        while ($j <= 30) {
            if ($temp_start_time > $now_time) {
                break;
            }
            $temp_end_time = $temp_start_time + $stats_scale;
            if ($j == 30) {
                $temp_end_time = $temp_end_time - 1;
            }
            $temp_total_orders = 0;
            $result            = $db_orderinfo_view->field($field)
                ->join($join)
                ->where(array_merge($where, array('oi.pay_time >="' . $temp_start_time . '" and oi.pay_time<="' . $temp_end_time . '"')))
                ->order(array('oi.pay_time' => 'asc'))
                ->select();
            if (!empty($result)) {
                foreach ($result as $val) {
                    $temp_total_orders++;
                    if ($val['user_id'] > 0) {
                        $member_orders++;
                    } else {
                        $anonymity_orders++;
                    }
                }
                $stats[] = array(
                    'time'           => $temp_start_time,
                    'formatted_time' => RC_Time::local_date('Y-m-d H:i:s', $temp_start_time),
                    'orders'         => $temp_total_orders,
                    'value'          => $temp_total_orders,
                );
            } else {
                $stats[] = array(
                    'time'           => $temp_start_time,
                    'formatted_time' => RC_Time::local_date('Y-m-d H:i:s', $temp_start_time),
                    'orders'         => 0,
                    'value'          => 0,
                );
            }
            $temp_start_time += $stats_scale;
            $j++;
        }

        $i          = 1;
        $temp_group = $start_date;
        while ($i <= 7) {
            if ($i == 7) {
                $group[] = array(
                    'time'           => $end_date,
                    'formatted_time' => RC_Time::local_date('Y-m-d H:i:s', $end_date),
                );
                break;
            }
            $group[]    = array(
                'time'           => $temp_group,
                'formatted_time' => RC_Time::local_date('Y-m-d H:i:s', $temp_group),
            );
            $temp_group += $group_scale;
            $i++;
        }

        $order_query = RC_Loader::load_app_class('order_query', 'orders');
        /* 已付款*/
        $payed_orders = $db_orderinfo_view->join($join)->where(array_merge($where, array('oi.pay_status' => array(PS_PAYED, PS_PAYING))))->count('oi.order_id');
        /* 未发货*/
        $wait_ship_orders = $db_orderinfo_view->join($join)->where(array_merge($where, $order_query->order_await_ship('oi.')))->count('oi.order_id');
        /* 已发货*/
        $shipped_orders = $db_orderinfo_view->join($join)->where(array_merge($where, $order_query->order_shipped('oi.')))->count('oi.order_id');

        $data = array(
            'stats'            => $stats,
            'group'            => $group,
            'payed_orders'     => $payed_orders,
            'wait_ship_orders' => $wait_ship_orders,
            'shipped_orders'   => $shipped_orders,
            'member_orders'    => $member_orders,
            'anonymity_orders' => $anonymity_orders,
        );
        return $data;
    }
}

//end