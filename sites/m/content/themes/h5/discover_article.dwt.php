<?php 
/*
Name: 发现文章详情
Description: 发现文章详情
*/
defined('IN_ECJIA') or header("HTTP/1.0 404 Not Found");exit('404 Not Found');
?>
{nocache}
<!-- {extends file="ecjia-touch.dwt.php"} -->

<!-- {block name="footer"} -->
<script type="text/javascript">

{if $is_weixin}
var config = '{$config}';
{/if}

ecjia.touch.index.init();
</script>
<!-- {/block} -->

<!-- {block name="main-content"} -->
<div class="ecjia-discover-detail">
	<div class="author-header"> 
		<h1 class="article-title">{$data.title}</h1> 
		<div class="author-head clear-fix" data-lazy="false"> 
			<img class="author-pic lazy-img" src="{if $data.store_id eq 0}{$theme_url}images/store_logo.png{else}{$data.store_logo}{/if}"> 
			<span class="author-info">{$data.store_name}</span> 
			<span class="m_l2 author-info">{$data.add_time}</span> 
		</div>
	</div>
	<div class="article-container">
		<div class="article-content clearfix article-p">{$content}</div>
		<!-- {if $data.article_related_goods} -->
			<!-- {foreach from=$data.article_related_goods item=goods} -->
			<div class="article-content clearfix goods-list"> 
				<div class="article-content-sku clearfix" data-lazy="false"> 
					<a class="nopjax external" href='{url path="goods/index/show"}&goods_id={$goods.goods_id}'><img class="content-sku-img lazy-img" src="{$goods.img.thumb}"></a>
					<div class="content-sku-right"> 
						<a class="nopjax external" href='{url path="goods/index/show"}&goods_id={$goods.goods_id}'><p class="content-sku-name line-clamp2">{$goods.name}</p></a>
						<div class="content-sku-bottom clearfix"> 
							<span class="sku-price ellipsis">{$goods.shop_price}</span>  
							<a class="nopjax external" href='{url path="goods/index/show"}&goods_id={$goods.goods_id}'><span class="buy-btn">{t domain="h5"}去购买{/t}</span></a>
						</div> 
					</div> 
				</div>  
			</div>
			<!-- {/foreach} -->
		<!-- {/if} -->
	</div>
	
	<!-- {if $data.store_id neq 0} -->
	<div class="author-panel"> 
		<div class="author-pic-box" data-lazy="false"> 
			<a class="nopjax external" href='{url path="merchant/index/init"}&store_id={$data.store_id}'><img class="author-pic lazy-img" src="{$data.store_logo}"></a>
		</div> 
		<div class="author-title clearfix"> 
			<span class="author-name ellipsis">{$data.store_name}</span> 
		</div> 
		<p class="author-desc"> 
			<span>{t domain="h5" 1={$data.total_articles}}共%1篇资讯{/t}</span> 
		</p>  
		<a class="nopjax external" href='{url path="merchant/index/init"}&store_id={$data.store_id}'>
			<div class="enter-store">{t domain="h5"}进入店铺{/t}</div> 
		</a>
	</div>
	<!-- {/if} -->
	
	<!-- {if $data.recommend_goods} -->
	<div class="floor"> 
        <div class="ecjia-margin-t">
			<div class="floor-title"> 
				<span class="floor-title-cn">{t domain="h5"}相关商品{/t}</span> 
				<span class="floor-title-en">Related Products</span> 
			</div> 
            <div class="form-group ecjia-form ecjia-like-goods-list">
                <div class="bd">
					<ul class="ecjia-list ecjia-like-goods-list">
						<!--{foreach from=$data.recommend_goods item=goods name=goods}-->
						<!-- {if $smarty.foreach.goods.index < 6 } -->
						<li>
							<a class="nopjax external" href='{url path="goods/index/show" args="goods_id={$goods.goods_id}&product_id={$goods.product_id}"}'>
								<img src="{$goods.img.url}" alt="{$goods.name}" title="{$goods.name}"/>
							</a>
							<p class="link-goods-name ecjia-goods-name-new">{$goods.name}</p>
							<div class="link-goods-price">
								<span class="goods-price">{$goods.shop_price}</span>
							</div>
						</li>
						<!--{/if}-->
						<!--{/foreach}-->
					</ul>
				</div>
            </div>
        </div>
        <a class="nopjax external" href="{RC_Uri::url('merchant/index/init')}&store_id={$data.store_id}">
        	<div class="view-more-goods">{t domain="h5"}查看更多商品{/t}</div> 
        </a>
	</div>
	<!-- {/if} -->
	
	<div class="floor comment-floor" name="floor-comment" id="floor-comment"> 
		<div class="background-fff">
			<div class="floor-title"> 
				<span class="floor-title-cn">{t domain="h5"}评论{/t}</span> 
				<span class="floor-title-en">Comments</span> 
				{if $data.comment_count neq 0}
				<span class="floor-title-count">{t domain="h5" 1={$data.comment_count}}共%1条{/t}</span>
				{/if}
			</div> 
			<div class="floor-content">
				<ul class="floor-content-list" data-toggle="asynclist" data-url="{url path='article/index/ajax_comment_list'}&article_id={$article_id}"></ul>
			</div>
        </div>
	</div>
	
	
	<nav class="nav-bt-fix">
		<div class="nav-bt-center">
	        <a class="article-appreciate external {if $data.is_like eq 1}active{/if}" href="javascript:;">
	        	<i class="iconfont icon-appreciate"><span>{$data.like_count}</span></i>
	        </a>
	        <a class="article-bianji external" href="javascript:;">
	        	<i class="iconfont icon-bianji1"></i><span class="write-comment">{t domain="h5"}写评论{/t}</span>
	        </a>
	        <a class="external" href="#floor-comment">
	        	<i class="iconfont icon-comment"><span>{$data.comment_count}</span></i>
	        </a>
        </div>    
    </nav>
    
    <div class="send-box">
        <div class="textarea-box">
            <textarea cols="30" rows="3" placeholder='{t domain="h5"}我来说两句...{/t}'></textarea>
            <a class="xin-btn xin-btn-small send-btn" href="javascript:void(0);">{t domain="h5"}发送{/t}</a>
        </div>
        <input type="hidden" name="add_comment" value="{RC_Uri::url('article/index/add_comment')}&article_id={$article_id}" />
    </div>
    <input type="hidden" name="like_article" value="{RC_Uri::url('article/index/like_article')}&article_id={$article_id}" />
    <div class="box_overlay"></div>
</div>
<input type="hidden" name="share_title" value="{$data.title}">
<input type="hidden" name="share_link" value="{$share_link}">
<input type="hidden" name="share_page" value="1">
<!-- {/block} -->
 {/nocache}