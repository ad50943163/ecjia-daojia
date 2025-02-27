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

// autoload_psr4.php
$contentDir = realpath(__DIR__ . '/../');
$baseDir = realpath(__DIR__ . '/../../');

return array(
    'Ecjia\System'              => $contentDir . '/system/classes',

    'Ecjia\App\Adsense'         => $contentDir . '/apps/adsense/classes',
    'Ecjia\App\Affiliate'       => $contentDir . '/apps/affiliate/classes',
    'Ecjia\App\Api'             => $contentDir . '/apps/api/classes',
    'Ecjia\App\Article'         => $contentDir . '/apps/article/classes',
    'Ecjia\App\Attach'          => $contentDir . '/apps/attach/classes',
    'Ecjia\App\Agent'           => $contentDir . '/apps/agent/classes',
    'Ecjia\App\Bonus'           => $contentDir . '/apps/bonus/classes',
    'Ecjia\App\Captcha'         => $contentDir . '/apps/captcha/classes',
    'Ecjia\App\Cart'            => $contentDir . '/apps/cart/classes',
    'Ecjia\App\Cashier'         => $contentDir . '/apps/cashier/classes',
    'Ecjia\App\Comment'         => $contentDir . '/apps/comment/classes',
    'Ecjia\App\Commission'      => $contentDir . '/apps/commission/classes',
    'Ecjia\App\Connect'         => $contentDir . '/apps/connect/classes',
    'Ecjia\App\Cron'            => $contentDir . '/apps/cron/classes',
    'Ecjia\App\Express'         => $contentDir . '/apps/express/classes',
    'Ecjia\App\Favourable'      => $contentDir . '/apps/favourable/classes',
    'Ecjia\App\Finance'         => $contentDir . '/apps/finance/classes',
    'Ecjia\App\Franchisee'      => $contentDir . '/apps/franchisee/classes',
    'Ecjia\App\Friendlink'      => $contentDir . '/apps/friendlink/classes',
    'Ecjia\App\Goods'           => $contentDir . '/apps/goods/classes',
    'Ecjia\App\Goodslib'        => $contentDir . '/apps/goodslib/classes',
    'Ecjia\App\Groupbuy'        => $contentDir . '/apps/groupbuy/classes',
    'Ecjia\App\Installer'       => $contentDir . '/apps/installer/classes',
    'Ecjia\App\Integrate'       => $contentDir . '/apps/integrate/classes',
    'Ecjia\App\Intro'           => $contentDir . '/apps/intro/classes',
    'Ecjia\App\Invitecode'      => $contentDir . '/apps/invitecode/classes',
    'Ecjia\App\Logviewer'       => $contentDir . '/apps/logviewer/classes',
    'Ecjia\App\Mail'            => $contentDir . '/apps/mail/classes',
    'Ecjia\App\Main'            => $contentDir . '/apps/main/classes',
    'Ecjia\App\Maintain'        => $contentDir . '/apps/maintain/classes',
    'Ecjia\App\Market'          => $contentDir . '/apps/market/classes',
    'Ecjia\App\Memadmin'        => $contentDir . '/apps/memadmin/classes',
    'Ecjia\App\Merchant'        => $contentDir . '/apps/merchant/classes',
    'Ecjia\App\Mobile'          => $contentDir . '/apps/mobile/classes',
    'Ecjia\App\Notification'    => $contentDir . '/apps/notification/classes',
    'Ecjia\App\Orders'          => $contentDir . '/apps/orders/classes',
    'Ecjia\App\Payment'         => $contentDir . '/apps/payment/classes',
    'Ecjia\App\Platform'        => $contentDir . '/apps/platform/classes',
    'Ecjia\App\Printer'         => $contentDir . '/apps/printer/classes',
    'Ecjia\App\Promotion'       => $contentDir . '/apps/promotion/classes',
    'Ecjia\App\Push'            => $contentDir . '/apps/push/classes',
    'Ecjia\App\Quickpay'        => $contentDir . '/apps/quickpay/classes',
    'Ecjia\App\Refund'          => $contentDir . '/apps/refund/classes',
    'Ecjia\App\Setting'         => $contentDir . '/apps/setting/classes',
    'Ecjia\App\Shipping'        => $contentDir . '/apps/shipping/classes',
    'Ecjia\App\Shopguide'       => $contentDir . '/apps/shopguide/classes',
    'Ecjia\App\Sms'             => $contentDir . '/apps/sms/classes',
    'Ecjia\App\Staff'           => $contentDir . '/apps/staff/classes',
    'Ecjia\App\Stats'           => $contentDir . '/apps/stats/classes',
    'Ecjia\App\Store'           => $contentDir . '/apps/store/classes',
    'Ecjia\App\Theme'           => $contentDir . '/apps/theme/classes',
    'Ecjia\App\Tmplmsg'         => $contentDir . '/apps/tmplmsg/classes',
    'Ecjia\App\Touch'           => $contentDir . '/apps/touch/classes',
    'Ecjia\App\Toutiao'         => $contentDir . '/apps/toutiao/classes',
	'Ecjia\App\Track'         	=> $contentDir . '/apps/track/classes',
    'Ecjia\App\Ucclient'        => $contentDir . '/apps/ucclient/classes',
    'Ecjia\App\Ucserver'        => $contentDir . '/apps/ucserver/classes',
    'Ecjia\App\Upgrade'         => $contentDir . '/apps/upgrade/classes',
    'Ecjia\App\User'            => $contentDir . '/apps/user/classes',
    'Ecjia\App\Weapp'           => $contentDir . '/apps/weapp/classes',
    'Ecjia\App\Wechat'          => $contentDir . '/apps/wechat/classes',
    'Ecjia\App\Customer'        => $contentDir . '/apps/customer/classes',
    'Ecjia\App\Withdraw'        => $contentDir . '/apps/withdraw/classes',
    'Ecjia\App\Topic'           => $contentDir . '/apps/topic/classes',
    'Ecjia\App\Supplier'        => $contentDir . '/apps/supplier/classes',
    
    'Royalcms\Component\HttpKernel' => $contentDir . '/apps/api/classes/Royalcms/Component/HttpKernel',
    'Royalcms\Component\Cron' => $contentDir . '/apps/cron/classes/Royalcms/Component/Cron',
    'Royalcms\Component\Shoppingcart' => $contentDir . '/apps/cart/classes/Royalcms/Component/Shoppingcart',
    'Royalcms\Component\Printer' => $contentDir . '/apps/printer/classes/Royalcms/Component/Printer',
    'Royalcms\Component\Ucenter' => $contentDir . '/apps/ucclient/classes/Ucenter/Royalcms/Component/Ucenter',
);

//end