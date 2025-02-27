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
 * 关键字统计
 * @author wutifang
*/
class mh_keywords_stats extends ecjia_merchant {
	public function __construct() {
		parent::__construct();
		RC_Loader::load_app_func('global', 'stats');

		/*加载所有全局 js/css */
		RC_Script::enqueue_script('bootstrap-placeholder');
		RC_Script::enqueue_script('jquery-validate');
		RC_Script::enqueue_script('jquery-form');
		RC_Script::enqueue_script('smoke');
		RC_Script::enqueue_script('jquery-chosen');
		RC_Style::enqueue_style('chosen');
		RC_Script::enqueue_script('jquery-uniform');
		RC_Style::enqueue_style('uniform-aristo');
		
		//时间控件
		RC_Script::enqueue_script('bootstrap-datepicker', RC_Uri::admin_url('statics/lib/datepicker/bootstrap-datepicker.min.js'));
		RC_Style::enqueue_style('datepicker', RC_Uri::admin_url('statics/lib/datepicker/datepicker.css'));
		
		/*自定义JS*/
		RC_Style::enqueue_style('stats-css', RC_App::apps_url('statics/css/stats.css', __FILE__), array());
		RC_Script::enqueue_script('keywords', RC_App::apps_url('statics/js/merchant_keywords.js', __FILE__), array(), false, 1);
        RC_Script::localize_script('keywords_lang', 'js_lang', config('app-stats::jslang.statistic_page'));

        ecjia_merchant_screen::get_current_screen()->set_parentage('stats', 'stats/mh_keywords_stats.php');
	}
	
	public function init() {
		$this->admin_priv('stats_search_keywords');
		
		ecjia_merchant_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('报表统计', 'stats'), RC_Uri::url('stats/mh_keywords_stats/init')));
		ecjia_merchant_screen::get_current_screen()->add_nav_here(new admin_nav_here(__('搜索关键字', 'stats')));
		ecjia_merchant_screen::get_current_screen()->add_help_tab(array(
			'id'		=> 'overview',
			'title'		=> __('概述', 'stats'),
			'content'	=> '<p>' . __('欢迎访问ECJia智能后台搜索引擎页面，系统上所有的搜索引擎信息都会显示在此页面上。', 'stats') . '</p>'
		));
		ecjia_merchant_screen::get_current_screen()->set_help_sidebar(
			'<p><strong>' . __('更多信息：', 'stats') . '</strong></p>' .
			'<p><a href="https://ecjia.com/wiki/帮助:ECJia智能后台:搜索引擎" target="_blank">'. __('关于搜索关键字帮助文档', 'stats') .'</a></p>'
		);
		$this->assign('ur_here', __('搜索关键字', 'stats'));
		$this->assign('action_link', array('text' => __('下载搜索关键字报表', 'stats'), 'href' => RC_Uri::url('stats/mh_keywords_stats/download')));
		
		$start_date = !empty($_GET['start_date']) ? $_GET['start_date'] : RC_Time::local_date(ecjia::config('date_format'), strtotime('-7 days')-8*3600);
		$end_date   = !empty($_GET['end_date']) ? $_GET['end_date'] : RC_Time::local_date(ecjia::config('date_format'));
		
		$this->assign('start_date', $start_date);
		$this->assign('end_date', $end_date);
		$this->assign('search_action', RC_Uri::url('stats/mh_keywords_stats/init'));
		
		$keywords_data = $this->get_keywords_list();
		$this->assign('keywords_data', $keywords_data);
		
		$this->display('keywords_stats.dwt');
	}
	
	public function download() {
		$this->admin_priv('stats_search_keywords', ecjia::MSGTYPE_JSON);
		
		$keywords_list = $this->get_keywords_list(false);
		$filename = __('关键字统计', 'stats').'_'.$keywords_list['date'];
		
		header("Content-type: application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=$filename.xls");
		
		$data = __('关键词', 'stats')."\t".__('搜索引擎', 'stats')."\t".__('搜索次数', 'stats')."\t".__('日期', 'stats')."\t\n";
		
		if (!empty($keywords_list['item'])) {
			foreach ($keywords_list['item'] as $v) {
				$data .= RC_Format::filterEmoji($v['keyword']) . "\t";
				$data .= $v['searchengine'] . "\t";
				$data .= $v['count'] . "\t";
				$data .= $v['date'] . "\t\n";
			}
		}
		echo mb_convert_encoding($data."\t", "GBK", "UTF-8");
		exit;
	}
	
	/**
	 * 获取数据
	 */
	private function get_keywords_list($is_page = true) {
	    $page_num = 20;
		$db_keywords = RC_DB::table('store_keywords');
		
		$start_date = empty($_GET['start_date']) 	? RC_Time::local_date(ecjia::config('date_format'), RC_Time::local_strtotime('-7 days')) : $_GET['start_date'];
		$end_date 	= empty($_GET['end_date']) 		? RC_Time::local_date(ecjia::config('date_format'), RC_Time::local_strtotime('today')) 	: $_GET['end_date'];
		$db_keywords->where('date', '>=', $start_date)->where('date', '<=', $end_date);
		
		$db_keywords->select('keyword', 'count', 'date')->where('store_id', $_SESSION['store_id'])->orderby('count', 'desc');
		$count = $db_keywords->count();
		$page = new ecjia_merchant_page($count, $page_num, 5);
		
		if ($is_page) {
			$db_keywords->take($page->page_size)->skip($page->start_id-1);
		}
		$data = $db_keywords->get();
		return array('item' => $data, 'page' => $page->show(2), 'desc' => $page->page_desc(), 'date' => $start_date.'-'.$end_date);
	}
}

// end