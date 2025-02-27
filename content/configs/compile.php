<?php

$contentDir = realpath(__DIR__ . '/../');
$baseDir = dirname($contentDir);

return [

    /*
    |--------------------------------------------------------------------------
    | Additional Compiled Classes
    |--------------------------------------------------------------------------
    |
    | Here you may specify additional classes to include in the compiled file
    | generated by the `royalcms optimize` command. These should be classes
    | that are included on basically every request into the application.
    |
    */

    'files' => [
        $baseDir . '/vendor/royalcms/qrcode/Royalcms/Component/QrCode/QrCodeServiceProvider.php',
        $baseDir . '/vendor/royalcms/sms/Royalcms/Component/Sms/SmsServiceProvider.php',
        $baseDir . '/vendor/royalcms/omnipay/Royalcms/Component/Omnipay/OmnipayServiceProvider.php',
        $baseDir . '/vendor/royalcms/alidayu/Royalcms/Component/Alidayu/AlidayuServiceProvider.php',
        $baseDir . '/vendor/royalcms/image/Royalcms/Component/Image/ImageServiceProvider.php',
        $baseDir . '/vendor/royalcms/wechat/Royalcms/Component/WeChat/WeChatServiceProvider.php',
        $baseDir . '/vendor/royalcms/ip-address/Royalcms/Component/IpAddress/Ip.php',
        $baseDir . '/vendor/royalcms/class-loader/Royalcms/Component/ClassLoader/ClassManager.php',
        $baseDir . '/vendor/royalcms/purifier/Royalcms/Component/Purifier/PurifierServiceProvider.php',
        $baseDir . '/vendor/royalcms/xml-response/Royalcms/Component/XmlResponse/XmlResponseServiceProvider.php',
        $baseDir . '/vendor/royalcms/tcpdf/Royalcms/Component/Tcpdf/TcpdfServiceProvider.php',
        $baseDir . '/vendor/royalcms/excel/Royalcms/Component/Excel/ExcelServiceProvider.php',
        $baseDir . '/vendor/royalcms/wechat-miniprogram/Royalcms/Component/WeChat/MiniProgram/MiniProgramServiceProvider.php',

        $contentDir . '/apps/cron/classes/Royalcms/Component/Cron/CronServiceProvider.php',
        $contentDir . '/apps/cart/classes/Royalcms/Component/Shoppingcart/ShoppingcartServiceProvider.php',
        $contentDir . '/apps/printer/classes/Royalcms/Component/Printer/PrinterServiceProvider.php',

        $contentDir . '/system/classes/Providers/SystemServiceProvider.php',

        $contentDir . '/apps/adsense/classes/AdsenseServiceProvider.php',
        $contentDir . '/apps/affiliate/classes/AffiliateServiceProvider.php',
        $contentDir . '/apps/api/classes/ApiServiceProvider.php',
        $contentDir . '/apps/article/classes/ArticleServiceProvider.php',
//        $contentDir . '/apps/attach/classes/AttachServiceProvider.php',
        $contentDir . '/apps/agent/classes/AgentServiceProvider.php',
        $contentDir . '/apps/bonus/classes/BonusServiceProvider.php',
        $contentDir . '/apps/captcha/classes/CaptchaServiceProvider.php',
        $contentDir . '/apps/cart/classes/CartServiceProvider.php',
        $contentDir . '/apps/cashier/classes/CashierServiceProvider.php',
        $contentDir . '/apps/comment/classes/CommentServiceProvider.php',
        $contentDir . '/apps/commission/classes/CommissionServiceProvider.php',
        $contentDir . '/apps/connect/classes/ConnectServiceProvider.php',
        $contentDir . '/apps/cron/classes/CronServiceProvider.php',
        $contentDir . '/apps/express/classes/ExpressServiceProvider.php',
        $contentDir . '/apps/favourable/classes/FavourableServiceProvider.php',
        $contentDir . '/apps/finance/classes/FinanceServiceProvider.php',
        $contentDir . '/apps/franchisee/classes/FranchiseeServiceProvider.php',
        $contentDir . '/apps/friendlink/classes/FriendlinkServiceProvider.php',
        $contentDir . '/apps/goods/classes/GoodsServiceProvider.php',
        $contentDir . '/apps/goodslib/classes/GoodslibServiceProvider.php',
        $contentDir . '/apps/groupbuy/classes/GroupbuyServiceProvider.php',
        $contentDir . '/apps/installer/classes/InstallerServiceProvider.php',
        $contentDir . '/apps/intro/classes/IntroServiceProvider.php',
        $contentDir . '/apps/logviewer/classes/LogviewerServiceProvider.php',
        $contentDir . '/apps/mail/classes/MailServiceProvider.php',
        $contentDir . '/apps/main/classes/MainServiceProvider.php',
        $contentDir . '/apps/maintain/classes/MaintainServiceProvider.php',
        $contentDir . '/apps/market/classes/MarketServiceProvider.php',
        $contentDir . '/apps/merchant/classes/MerchantServiceProvider.php',
        $contentDir . '/apps/mobile/classes/MobileServiceProvider.php',
        $contentDir . '/apps/notification/classes/NotificationServiceProvider.php',
        $contentDir . '/apps/orders/classes/OrdersServiceProvider.php',
        $contentDir . '/apps/payment/classes/PaymentServiceProvider.php',
        $contentDir . '/apps/platform/classes/PlatformServiceProvider.php',
        $contentDir . '/apps/printer/classes/PrinterServiceProvider.php',
        $contentDir . '/apps/promotion/classes/PromotionServiceProvider.php',
        $contentDir . '/apps/push/classes/PushServiceProvider.php',
        $contentDir . '/apps/quickpay/classes/QuickpayServiceProvider.php',
        $contentDir . '/apps/refund/classes/RefundServiceProvider.php',
        $contentDir . '/apps/setting/classes/SettingServiceProvider.php',
        $contentDir . '/apps/shipping/classes/ShippingServiceProvider.php',
        $contentDir . '/apps/shopguide/classes/ShopguideServiceProvider.php',
        $contentDir . '/apps/sms/classes/SmsServiceProvider.php',
        $contentDir . '/apps/staff/classes/StaffServiceProvider.php',
        $contentDir . '/apps/stats/classes/StatsServiceProvider.php',
        $contentDir . '/apps/store/classes/StoreServiceProvider.php',
        $contentDir . '/apps/theme/classes/ThemeServiceProvider.php',
//        $contentDir . '/apps/tmplmsg/classes/TmplmsgServiceProvider.php',
        $contentDir . '/apps/touch/classes/TouchServiceProvider.php',
        $contentDir . '/apps/upgrade/classes/UpgradeServiceProvider.php',
        $contentDir . '/apps/user/classes/UserServiceProvider.php',
        $contentDir . '/apps/weapp/classes/WeappServiceProvider.php',
        $contentDir . '/apps/wechat/classes/WechatServiceProvider.php',
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled File Providers
    |--------------------------------------------------------------------------
    |
    | Here you may list service providers which define a "compiles" function
    | that returns additional files that should be compiled, providing an
    | easy way to get common files from any packages you are utilizing.
    |
    */

    'providers' => [
        'Royalcms\Component\App\AppServiceProvider',
        'Royalcms\Component\Database\DatabaseServiceProvider',
        'Royalcms\Component\NativeSession\NativeSessionServiceProvider',
        'Royalcms\Component\Package\PackageServiceProvider',
        'Royalcms\Component\Gettext\GettextServiceProvider',
        'Royalcms\Component\Cache\CacheServiceProvider',
        'Royalcms\Component\Exception\ExceptionServiceProvider',
        'Royalcms\Component\Hook\HookServiceProvider',
        'Royalcms\Component\Script\ScriptServiceProvider',
        'Royalcms\Component\Timer\TimerServiceProvider',
        'Royalcms\Component\SmartyView\SmartyCompileServiceProvider',
        'Royalcms\Component\Rewrite\RewriteServiceProvider',
        'Royalcms\Component\Error\ErrorServiceProvider',
        'Royalcms\Component\DefaultRoute\DefaultRouteServiceProvider',
        'Royalcms\Component\Variable\VariableServiceProvider',
        'Royalcms\Component\Repository\RepositoryServiceProvider',
        'Royalcms\Component\Agent\AgentServiceProvider',
        'Royalcms\Component\Storage\StorageServiceProvider',
        'Royalcms\Component\Environment\EnvironmentServiceProvider',
        'Royalcms\Component\Upload\UploadServiceProvider',

        'Ecjia\System\Providers\SystemServiceProvider',

    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled Config files, Excludes other config files.
    |--------------------------------------------------------------------------
    |
    | Here you may list service providers which define a "compiles" function
    | that returns additional files that should be compiled, providing an
    | easy way to get common files from any packages you are utilizing.
    |
    */
    'exclude_configs' => [
        '*::system',
        '*::namespaces',
        '*::provider',
        '*::facade',
        '*::database',
        '*::cache',
        '*::app',
        '*::session',
        '*::view',
        '*::route',
        '*::site',
        '*::storage',
        '*::queue',
        '*::release',
        '*::pay',
        '*::upload',

        'smarty-view::smarty',
        'excel::export',
    ],

];
