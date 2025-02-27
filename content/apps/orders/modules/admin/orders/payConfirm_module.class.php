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
 * 订单支付确认
 * @author will
 *
 */
class admin_orders_payConfirm_module extends api_admin implements api_interface
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

        RC_Loader::load_app_class('OrderStatusLog', 'orders', false);

        $order_id    = $this->requestData('order_id');
        $invoice_no  = $this->requestData('invoice_no');
        $action_note = $this->requestData('action_note');

        if (empty($order_id)) {
            return new ecjia_error('invalid_parameter', __('参数错误', 'orders'));
        }

        /* 查询订单信息 */
        $order = RC_Api::api('orders', 'order_info', array('order_id' => $order_id, 'order_sn' => ''));

        if (empty($order)) {
            return new ecjia_error('dose_not_exist', __('不存在的信息', 'orders'));
        }

        $payment_method  = new Ecjia\App\Payment\PaymentPlugin();
        $pay_info        = $payment_method->getPluginDataById($order['pay_id']);
        $payment_handler = $payment_method->channel($pay_info['pay_code']);

        /* 判断是否有支付方式以及是否为现金支付和酷银*/
        if (is_ecjia_error($payment_handler)) {
            return $payment_handler;
        }
        $payment_handler->set_orderinfo($order);

        RC_Api::api('commission', 'add_bill_queue', array('order_type' => 'buy', 'order_id' => $order_id));

        if ($pay_info['pay_code'] == 'pay_cash') {
            /* 进行确认*/
            $result = RC_Api::api('orders', 'order_operate', array('order_id' => $order_id, 'order_sn' => '', 'operation' => 'confirm', 'note' => array('action_note' => __('收银台订单确认', 'orders'))));
            if (is_ecjia_error($result)) {
                RC_Logger::getLogger('error')->info(sprintf(__('订单确认【订单id|%s】：%s', 'orders'), $order_id, $result->get_error_message()));
            }

            $order   = RC_Api::api('orders', 'order_info', array('order_id' => $order_id, 'order_sn' => ''));
            $operate = RC_Loader::load_app_class('order_operate', 'orders');
            $operate->operate($order, 'pay', __('收银台现金收款', 'orders'));

            //支付后扩展处理
            RC_Hook::do_action('order_payed_do_something', $order);

            //会员店铺消费过，记录为店铺会员
            if (!empty($order['user_id'])) {
                if (!empty($order['store_id'])) {
                    RC_Loader::load_app_class('add_storeuser', 'user', false);
                    add_storeuser::add_store_user(array('user_id' => $order['user_id'], 'store_id' => $order['store_id']));
                }
            }

            /* 更新支付流水记录*/
            RC_Api::api('payment', 'update_payment_record', [
                'order_sn' => $order['order_sn'],
                'trade_no' => ''
            ]);
            /* 配货*/
            $result = RC_Api::api('orders', 'order_operate', array('order_id' => $order_id, 'order_sn' => '', 'operation' => 'prepare', 'note' => array('action_note' => __('收银台配货', 'orders'))));
            if (is_ecjia_error($result)) {
                RC_Logger::getLogger('error')->info(sprintf(__('订单配货【订单id|%s】：%s', 'orders'), $order_id, $result->get_error_message()));
            }
            /* 分单（生成发货单）*/
            $result = RC_Api::api('orders', 'order_operate', array('order_id' => $order_id, 'order_sn' => '', 'operation' => 'split', 'note' => array('action_note' => __('收银台生成发货单', 'orders'))));
            if (is_ecjia_error($result)) {
                RC_Logger::getLogger('error')->info(sprintf(__('订单分单【订单id|%s】：%s', 'orders'), $order_id, $result->get_error_message()));
            } else {
                /*订单状态日志记录*/
                OrderStatusLog::generate_delivery_orderInvoice(array('order_id' => $order['order_id'], 'order_sn' => $order['order_sn']));
            }

            /* 发货*/
            $db_delivery_order = RC_Loader::load_app_model('delivery_order_model', 'orders');
            $delivery_id       = $db_delivery_order->where(array('order_sn' => $order['order_sn']))->order(array('delivery_id' => 'desc'))->get_field('delivery_id');

            $result = $this->delivery_ship($order_id, $delivery_id, $invoice_no, $action_note);

            if (is_ecjia_error($result)) {
                RC_Logger::getLogger('error')->info(sprintf(__('订单发货【订单id|%s】：%s', 'orders'), $order_id, $result->get_error_message()));
            } else {
                /*订单状态日志记录*/
                OrderStatusLog::delivery_ship_finished(array('order_id' => $order['order_id'], 'order_sn' => $order['order_sn']));
            }

            /* 确认收货*/
            $arr                 = array('shipping_status' => SS_RECEIVED);
            $arr['order_status'] = OS_SPLITED;
            $arr['pay_status']   = PS_PAYED;
            $arr['pay_time']     = RC_Time::gmtime();
            $arr['money_paid']   = $order['money_paid'] + $order['order_amount'];
            $arr['order_amount'] = 0;
            $res                 = update_order($order_id, $arr);
            if ($res) {
                /*订单状态日志记录*/
                OrderStatusLog::affirm_received(array('order_id' => $order['order_id']));
            }

            /* 记录log */
            order_action($order['order_sn'], OS_SPLITED, SS_RECEIVED, PS_PAYED, __('收银台确认收货', 'orders'));

            //更新商品销量
            RC_Api::api('goods', 'update_goods_sales', array('order_id' => $order_id));

            $order_info = RC_Api::api('orders', 'order_info', array('order_id' => $order_id, 'order_sn' => ''));
            $data       = array(
                'order_id'               => $order_info['order_id'],
                'money_paid'             => $order_info['money_paid'],
                'formatted_money_paid'   => $order_info['formated_money_paid'],
                'order_amount'           => $order_info['order_amount'],
                'formatted_order_amount' => $order_info['formated_order_amount'],
                'pay_code'               => $pay_info['pay_code'],
                'pay_name'               => $pay_info['pay_name'],
                'pay_status'             => 'success',
                'desc'                   => __('订单支付成功！', 'orders')
            );
            $print_data = $this->_getprintdata($order_info);
            return array('payment' => $data, 'print_data' => $print_data);
        }

        if (in_array($pay_info['pay_code'], array('pay_koolyun', 'pay_koolyun_alipay', 'pay_koolyun_unionpay', 'pay_koolyun_wxpay', 'pay_balance', 'pay_shouqianba'))) {
            /* 更新支付流水记录*/
            RC_Api::api('payment', 'update_payment_record', [
                'order_sn' => $order['order_sn'],
                'trade_no' => ''
            ]);
            /* 配货*/
            $result = RC_Api::api('orders', 'order_operate', array('order_id' => $order_id, 'order_sn' => '', 'operation' => 'prepare', 'note' => array('action_note' => __('收银台配货', 'orders'))));
            if (is_ecjia_error($result)) {
                RC_Logger::getLogger('error')->info('订单配货【订单id|' . $order_id . '】：' . $result->get_error_message());
            }
            /* 分单（生成发货单）*/
            $result = RC_Api::api('orders', 'order_operate', array('order_id' => $order_id, 'order_sn' => '', 'operation' => 'split', 'note' => array('action_note' => __('收银台生成发货单', 'orders'))));
            if (is_ecjia_error($result)) {
                RC_Logger::getLogger('error')->info('订单分单【订单id|' . $order_id . '】：' . $result->get_error_message());
            } else {
                /*订单状态日志记录*/
                OrderStatusLog::generate_delivery_orderInvoice(array('order_id' => $order['order_id'], 'order_sn' => $order['order_sn']));
            }

            /* 发货*/
            $db_delivery_order = RC_Loader::load_app_model('delivery_order_model', 'orders');
            $delivery_id       = $db_delivery_order->where(array('order_sn' => $order['order_sn']))->order(array('delivery_id' => 'desc'))->get_field('delivery_id');

            $result = $this->delivery_ship($order_id, $delivery_id, $invoice_no, $action_note);
            if (is_ecjia_error($result)) {
                RC_Logger::getLogger('error')->info(sprintf(__('订单发货【订单id|%s】：%s', 'orders'), $order_id, $result->get_error_message()));
            } else {
                /*订单状态日志记录*/
                OrderStatusLog::delivery_ship_finished(array('order_id' => $order['order_id'], 'order_sn' => $order['order_sn']));
            }

            /* 确认收货*/
            $arr                 = array('shipping_status' => SS_RECEIVED);
            $arr['pay_status']   = PS_PAYED;
            $arr['order_status'] = OS_SPLITED;
            $arr['pay_time']     = RC_Time::gmtime();
            $arr['money_paid']   = $order['money_paid'] + $order['order_amount'];
            $arr['order_amount'] = 0;
            $res                 = update_order($order_id, $arr);
            if ($res) {
                /*订单状态日志记录*/
                OrderStatusLog::affirm_received(array('order_id' => $order['order_id']));
            }

            /* 记录log */
            order_action($order['order_sn'], OS_SPLITED, SS_RECEIVED, PS_PAYED, __('收银台确认收货', 'orders'));

            //会员店铺消费过，记录为店铺会员
            if (!empty($order['user_id'])) {
                if (!empty($order['store_id'])) {
                    RC_Loader::load_app_class('add_storeuser', 'user', false);
                    add_storeuser::add_store_user(array('user_id' => $order['user_id'], 'store_id' => $order['store_id']));
                }
            }

            $order_info = RC_Api::api('orders', 'order_info', array('order_id' => $order_id, 'order_sn' => ''));
            //支付后扩展处理
            RC_Hook::do_action('order_payed_do_something', $order_info);
            $data       = array(
                'order_id'               => $order_info['order_id'],
                'money_paid'             => $order_info['money_paid'],
                'formatted_money_paid'   => $order_info['formated_money_paid'],
                'order_amount'           => $order_info['order_amount'],
                'formatted_order_amount' => $order_info['formated_order_amount'],
                'pay_code'               => $pay_info['pay_code'],
                'pay_name'               => $pay_info['pay_name'],
                'pay_status'             => 'success',
                'desc'                   => __('订单支付成功！', 'orders')
            );
            $print_data = $this->_getprintdata($order_info);
            return array('payment' => $data, 'print_data' => $print_data);
        }
    }


    private function delivery_ship($order_id, $delivery_id, $invoice_no, $action_note)
    {
        RC_Logger::getLogger('error')->info(sprintf(__('订单发货处理【订单id|%s】', 'orders'), $order_id));
        RC_Loader::load_app_func('global', 'orders');
        RC_Loader::load_app_func('admin_order', 'orders');
        $db_delivery       = RC_Loader::load_app_model('delivery_viewmodel', 'orders');
        $db_delivery_order = RC_Loader::load_app_model('delivery_order_model', 'orders');
        $db_goods          = RC_Loader::load_app_model('goods_model', 'goods');
        $db_products       = RC_Loader::load_app_model('products_model', 'goods');
        /* 定义当前时间 */
// 	define('GMTIME_UTC', RC_Time::gmtime()); // 获取 UTC 时间戳
        /* 取得参数 */
        $delivery               = array();
        $order_id               = intval(trim($order_id));            // 订单id
        $delivery_id            = intval(trim($delivery_id));        // 发货单id
        $delivery['invoice_no'] = isset($invoice_no) ? trim($invoice_no) : '';
        $action_note            = isset($action_note) ? trim($action_note) : '';

        /* 根据发货单id查询发货单信息 */
        if (!empty($delivery_id)) {
            $delivery_id    = intval($delivery_id);
            $delivery_order = delivery_order_info($delivery_id);
        }
        if (empty($delivery_order)) {
            return new ecjia_error('delivery_error', __('无法找到对应发货单！', 'orders'));
        }

        /* 查询订单信息 */
        $order = RC_Api::api('orders', 'order_info', array('order_id' => $order_id, 'order_sn' => ''));

        // RC_Model::model('orders/delivery_viewmodel')->view = array(
        //    		'goods' => array(
        //    			'type'  => Component_Model_View::TYPE_LEFT_JOIN,
        //    			'alias' => 'g',
        //    			'field' => 'dg.goods_id, dg.is_real, dg.product_id, SUM(dg.send_number) AS sums, IF(dg.product_id > 0, p.product_number, g.goods_number) AS storage, g.goods_name, dg.send_number',
        //    			'on'    => 'dg.goods_id = g.goods_id ',
        //    		),
        //  		'products' => array(
        // 				'type'  => Component_Model_View::TYPE_LEFT_JOIN,
        // 				'alias' => 'p',
        // 				'on'    => 'dg.product_id = p.product_id ',
        //  		)
        //    	);
        /* 检查此单发货商品库存缺货情况 */
        $virtual_goods = array();
        // $delivery_stock_result	= RC_Model::model('orders/delivery_viewmodel')->join(array('goods', 'products'))->where(array('dg.delivery_id' => $delivery_id))->group('dg.product_id')->select();

        $delivery_stock_result = RC_DB::table('delivery_goods as dg')
            ->leftJoin('goods as g', RC_DB::raw('dg.goods_id'), '=', RC_DB::raw('g.goods_id'))
            ->leftJoin('products as p', RC_DB::raw('dg.product_id'), '=', RC_DB::raw('p.product_id'))
            ->select(RC_DB::raw('dg.goods_id'), RC_DB::raw('dg.is_real'), RC_DB::raw('dg.product_id'), RC_DB::raw('SUM(dg.send_number) AS sums'), RC_DB::raw("IF(dg.product_id > 0, p.product_number, g.goods_number) AS storage"), RC_DB::raw('g.goods_name'), RC_DB::raw('dg.send_number'))
            ->where(RC_DB::raw('dg.delivery_id'), $delivery_id)
            ->groupBy(RC_DB::raw('dg.product_id'))
            ->get();


        /* 如果商品存在规格就查询规格，如果不存在规格按商品库存查询 */
        if (!empty($delivery_stock_result)) {
            foreach ($delivery_stock_result as $value) {
                if (($value['sums'] > $value['storage'] || $value['storage'] <= 0) &&
                    ((ecjia::config('use_storage') == '1' && ecjia::config('stock_dec_time') == SDT_SHIP) ||
                        (ecjia::config('use_storage') == '0' && $value['is_real'] == 0))) {
                    RC_Logger::getLogger('error')->info(__('缺货处理a', 'orders'));
                    return new ecjia_error('act_good_vacancy', sprintf(__('商品已缺货', 'orders'), $value['goods_name']));
                }

                /* 虚拟商品列表 virtual_card */
                if ($value['is_real'] == 0) {
                    $virtual_goods[] = array(
                        'goods_id'   => $value['goods_id'],
                        'goods_name' => $value['goods_name'],
                        'num'        => $value['send_number']
                    );
                }
            }
        } else {
            // $db_delivery->view = array(
            // 	'goods' => array(
            // 		'type'		=> Component_Model_View::TYPE_LEFT_JOIN,
            // 		'alias'		=> 'g',
            // 		'field'		=> 'dg.goods_id, dg.is_real, SUM(dg.send_number) AS sums, g.goods_number, g.goods_name, dg.send_number',
            // 		'on'		=> 'dg.goods_id = g.goods_id ',
            // 	)
            // );
            // $delivery_stock_result = $db_delivery->where(array('dg.delivery_id' => $delivery_id))->group('dg.goods_id')->select();

            $delivery_stock_result = RC_DB::table('delivery_goods as dg')
                ->leftJoin('goods as g', RC_DB::raw('dg.goods_id'), '=', RC_DB::raw('g.goods_id'))
                ->select(RC_DB::raw('dg.goods_id'), RC_DB::raw('dg.is_real'), RC_DB::raw('SUM(dg.send_number) AS sums'), RC_DB::raw('g.goods_number'), RC_DB::raw('g.goods_name'), RC_DB::raw('dg.send_number'))
                ->where(RC_DB::raw('dg.delivery_id'), $delivery_id)
                ->groupBy(RC_DB::raw('dg.goods_id'))
                ->get();

            foreach ($delivery_stock_result as $value) {
                if (($value['sums'] > $value['goods_number'] || $value['goods_number'] <= 0) &&
                    ((ecjia::config('use_storage') == '1' && ecjia::config('stock_dec_time') == SDT_SHIP) ||
                        (ecjia::config('use_storage') == '0' && $value['is_real'] == 0))) {
                    RC_Logger::getLogger('error')->info(__('缺货处理b', 'orders'));
                    return new ecjia_error('act_good_vacancy', sprintf(__('商品已缺货', 'orders'), $value['goods_name']));
                }

                /* 虚拟商品列表 virtual_card*/
                if ($value['is_real'] == 0) {
                    $virtual_goods[] = array(
                        'goods_id'   => $value['goods_id'],
                        'goods_name' => $value['goods_name'],
                        'num'        => $value['send_number']
                    );
                }
            }
        }

        /* 发货 */
        /* 处理虚拟卡 商品（虚货） */
        // if (is_array($virtual_goods) && count($virtual_goods) > 0) {
        //     RC_Loader::load_app_func('common', 'goods');
        //     foreach ($virtual_goods as $virtual_value) {
        //         virtual_card_shipping($virtual_value, $order['order_sn'], $msg, 'split');
        //     }
        // }

        /* 如果使用库存，且发货时减库存，则修改库存 */
        if (ecjia::config('use_storage') == '1' && ecjia::config('stock_dec_time') == SDT_SHIP) {
            foreach ($delivery_stock_result as $value) {
                /* 商品（实货）、超级礼包（实货） */
                if ($value['is_real'] != 0) {
                    /* （货品） */
                    if (!empty($value['product_id'])) {
                        $data = array(
                            'product_number' => $value['storage'] - $value['sums'],
                        );
                        $db_products->where(array('product_id' => $value['product_id']))->update($data);
                    } else {
                        $data = array(
                            'goods_number' => $value['storage'] - $value['sums'],
                        );
                        $db_goods->where(array('goods_id' => $value['goods_id']))->update($data);
                    }
                }
            }
        }

        /* 修改发货单信息 */
        $invoice_no              = str_replace(',', '<br>', $delivery['invoice_no']);
        $invoice_no              = trim($invoice_no, '<br>');
        $_delivery['invoice_no'] = $invoice_no;
        $_delivery['status']     = 0;    /* 0，为已发货 */
        $result                  = $db_delivery_order->where(array('delivery_id' => $delivery_id))->update($_delivery);

        if (!$result) {
            return new ecjia_error('act_false', __('操作失败', 'orders'));
        }

        /* 标记订单为已确认 “已发货” */
        /* 更新发货时间 */
        $order_finish = get_all_delivery_finish($order_id);

        $shipping_status        = ($order_finish == 1) ? SS_SHIPPED : SS_SHIPPED_PART;
        $arr['shipping_status'] = $shipping_status;
        $arr['shipping_time']   = RC_Time::gmtime(); // 发货时间
        $arr['invoice_no']      = trim($order['invoice_no'] . '<br>' . $invoice_no, '<br>');
        update_order($order_id, $arr);

        /* 发货单发货记录log */
        order_action($order['order_sn'], OS_CONFIRMED, $shipping_status, $order['pay_status'], __('收银台发货', 'orders'), null, 1);
        // 记录管理员操作
        if ($_SESSION['store_id'] > 0) {
            RC_Api::api('merchant', 'admin_log', array('text' => sprintf(__('发货，订单号是%s【来源掌柜】', 'orders'), $order['order_sn']), 'action' => 'setup', 'object' => 'order'));
        }
        RC_Logger::getLogger('error')->info(sprintf(__('判断是否全部发货%s', 'orders'), $order_finish));


        /* 如果当前订单已经全部发货 */
        if ($order_finish) {
            RC_Logger::getLogger('error')->info(__('订单发货，积分红包处理', 'orders'));
            /* 如果订单用户不为空，计算积分，并发给用户；发红包 */
            if ($order['user_id'] > 0) {


                /* 取得用户信息 */
                $user = user_info($order['user_id']);
                /* 计算并发放积分 */
                $integral      = integral_to_give($order);
                $integral_name = ecjia::config('integral_name');
                if (empty($integral_name)) {
                    $integral_name = __('积分', 'orders');
                }
                $options = array(
                    'user_id'     => $order['user_id'],
                    'rank_points' => intval($integral['rank_points']),
                    'pay_points'  => intval($integral['custom_points']),
                    'change_desc' => sprintf(__('订单%s赠送的%s', 'orders'), $order['order_sn'], $integral_name),
                    'from_type'   => 'order_give_integral',
                    'from_value'  => $order['order_sn'],
                );
                RC_Api::api('user', 'account_change_log', $options);
                /* 发放红包 */
                send_order_bonus($order_id);
            }
        }

        return true;
    }

    /**
     * 获取订单小票打印数据
     */
    private function _getprintdata($order_info = array())
    {

        $buy_print_data = array();
        if (!empty($order_info)) {
            $payment_record_info = $this->_paymentRecordInfo($order_info['order_sn'], 'buy');
            $order_goods         = $this->getOrderGoods($order_info['order_id']);
            $total_discount      = $order_info['discount'] + $order_info['integral_money'] + $order_info['bonus'];
            $money_paid          = $order_info['money_paid'] + $order_info['surplus'];

            //下单收银员
            $cashier_name = RC_DB::table('cashier_record as cr')
                ->leftJoin('staff_user as su', RC_DB::raw('cr.staff_id'), '=', RC_DB::raw('su.user_id'))
                ->where(RC_DB::raw('cr.order_id'), $order_info['order_id'])
                ->where(RC_DB::raw('cr.order_type'), 'buy')
                ->whereIn('cr.action', array('check_order', 'billing'))
                ->pluck('su.name');

            $user_info = [];
            //有没用户
            if ($order_info['user_id'] > 0) {
                $userinfo = $this->_get_user_info($order_info['user_id']);
                if (!empty($userinfo)) {
                    $user_info = array(
                        'user_name'            => empty($userinfo['user_name']) ? '' : trim($userinfo['user_name']),
                        'mobile'               => empty($userinfo['mobile_phone']) ? '' : trim($userinfo['mobile_phone']),
                        'user_points'          => $userinfo['pay_points'],
                        'user_money'           => $userinfo['user_money'],
                        'formatted_user_money' => price_format($userinfo['user_money'], false),
                    );
                }
            }

            $buy_print_data = array(
                'order_sn'                      => $order_info['order_sn'],
                'trade_no'                      => empty($payment_record_info['trade_no']) ? '' : $payment_record_info['trade_no'],
                'order_trade_no'                => empty($payment_record_info['order_trade_no']) ? '' : $payment_record_info['order_trade_no'],
                'trade_type'                    => 'buy',
                'pay_time'                      => empty($order_info['pay_time']) ? '' : RC_Time::local_date(ecjia::config('time_format'), $order_info['pay_time']),
                'goods_list'                    => $order_goods['list'],
                'total_goods_number'            => $order_goods['total_goods_number'],
                'total_goods_amount'            => $order_goods['taotal_goods_amount'],
                'formatted_total_goods_amount'  => price_format($order_goods['taotal_goods_amount'], false),
                'total_discount'                => $total_discount,
                'formatted_total_discount'      => price_format($total_discount, false),
                'money_paid'                    => $money_paid,
                'formatted_money_paid'          => price_format($money_paid, false),
                'integral'                      => intval($order_info['integral']),
                'integral_money'                => $order_info['integral_money'],
                'formatted_integral_money'      => price_format($order_info['integral_money'], false),
                'pay_name'                      => !empty($order_info['pay_name']) ? $order_info['pay_name'] : '',
                'payment_account'               => '',
                'user_info'                     => $user_info,
                'refund_sn'                     => '',
                'refund_total_amount'           => 0,
                'formatted_refund_total_amount' => '',
                'cashier_name'                  => empty($cashier_name) ? '' : $cashier_name
            );
        }

        return $buy_print_data;
    }

    /**
     * 支付交易记录信息
     * @param string $order_sn
     * @param string $trade_type
     * @return array
     */
    private function _paymentRecordInfo($order_sn = '', $trade_type = '')
    {
        $payment_revord_info = [];
        if (!empty($order_sn) && !empty($trade_type)) {
            $payment_revord_info = RC_DB::table('payment_record')->where('order_sn', $order_sn)->where('trade_type', $trade_type)->first();
        }
        return $payment_revord_info;
    }

    /**
     * 用户信息
     */
    private function _get_user_info($user_id = 0)
    {
        $user_info = RC_DB::table('users')->where('user_id', $user_id)->first();
        return $user_info;
    }

    /**
     * 订单商品
     */
    private function GetOrderGoods($order_id)
    {
        $field               = 'goods_id, goods_name, goods_number, (goods_number*goods_price) as subtotal';
        $order_goods         = RC_DB::table('order_goods')->where('order_id', $order_id)->select(RC_DB::raw($field))->get();
        $total_goods_number  = 0;
        $taotal_goods_amount = 0;
        $list                = [];
        if ($order_goods) {
            foreach ($order_goods as $row) {
                $total_goods_number  += $row['goods_number'];
                $taotal_goods_amount += $row['subtotal'];
                $list[]              = array(
                    'goods_id'           => $row['goods_id'],
                    'goods_name'         => $row['goods_name'],
                    'goods_number'       => $row['goods_number'],
                    'subtotal'           => $row['subtotal'],
                    'formatted_subtotal' => price_format($row['subtotal'], false),
                );
            }
        }

        return array('list' => $list, 'total_goods_number' => $total_goods_number, 'taotal_goods_amount' => $taotal_goods_amount);
    }
}


// end