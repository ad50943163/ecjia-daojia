<?php defined('IN_ECJIA') or exit('No permission resources.');?>
<!-- {extends file="ecjia.dwt.php"} -->

<!-- {block name="footer"} -->
<script type="text/javascript">
	ecjia.admin.store_edit.init();
</script>
<script type="text/javascript">
	//腾讯地图
	var map, markersArray = [];
	var latLng = new qq.maps.LatLng(31.22926, 121.40934);
	var map = new qq.maps.Map(document.getElementById("allmap"),{
	    center: latLng,
	    zoom: 16
	});
	$('input[name="longitude"]').val(121.40934);
    $('input[name="latitude"]').val(31.22926);
	setTimeout(function(){
	    var marker = new qq.maps.Marker({
	        position: latLng, 
	        map: map
	      });
	    markersArray.push(marker);
	}, 500);
	//添加监听事件 获取鼠标单击事件
	qq.maps.event.addListener(map, 'click', function(event) {
	    if (markersArray) {
	        for (i in markersArray) {
	            markersArray[i].setMap(null);
	        }
	        markersArray.length = 0;
	    }
	    $('input[name="longitude"]').val(event.latLng.lng)
	    $('input[name="latitude"]').val(event.latLng.lat)
	       var marker = new qq.maps.Marker({
	        position: event.latLng, 
	        map: map
	      });
	    markersArray.push(marker);    
	});
</script>
<!-- {/block} -->

<!-- {block name="main_content"} -->
<div>
	<h3 class="heading">
		<!-- {if $ur_here}{$ur_here}{/if} -->
		<!-- {if $action_link} -->
		<a class="data-pjax btn plus_or_reply" id="sticky_a" href="{$action_link.href}"><i class="fontello-icon-reply"></i>{$action_link.text}</a>
		<!-- {/if} -->
	</h3>
</div>

<div class="row-fluid">
	<div class="span12">
		<div class="tabbable tabs-left">

			<ul class="nav nav-tabs tab_merchants_nav">
                <!-- {foreach from=$menu item=val} -->
                <li {if $val.active}class="active"{/if}><a href="{$val.url}" {if $val.active}data-toggle="tab"{/if}>{$val.menu}</a></li>
                <!-- {/foreach} -->
            </ul>

			<div class="tab-content tab_merchants">
				<div class="tab-pane active " style="min-height:300px;">
				<form class="form-horizontal" id="form-privilege" name="theForm" action="{$form_action}" method="post" enctype="multipart/form-data" >
        			<fieldset>
        			    <div class="control-group formSep">
        					<label class="control-label">{t domain="store"}店铺名称：{/t}</label>
        					<div class="controls">
        						<input class="span6" name="merchants_name" type="text" value="{$store.merchants_name}" />
        						<span class="input-must">*</span>
        					</div>
        				</div>
        			    <div class="control-group formSep" >
        					<label class="control-label">{t domain="store"}商家分类：{/t}</label>
        					<div class="controls">
        						<select name="store_cat">
        							<option value="0">{t domain="store"}请选择……{/t}</option>
        							<!-- {html_options options=$cat_list selected=$store.cat_id} -->
        						</select>
        						<span class="input-must">*</span>
        					</div>
        				</div>
        				<div class="control-group formSep">
        					<label class="control-label">{t domain="store"}店铺关键词：{/t}</label>
        					<div class="controls">
        						<input class="span6" name="shop_keyword" type="text" value="{$store.shop_keyword}" />
        					</div>
        				</div>

        				<div class="control-group formSep">
        					<label class="control-label">{t domain="store"}联系手机：{/t}</label>
        					<div class="controls">
        						<input class="span6" name="contact_mobile" type="text" value="{$store.contact_mobile}" />
        						<span class="input-must">*</span>
        					</div>
        					<div class="m_t30 controls help-block">{t domain="store"}请正确填写手机号用于接收商家登录的账号和密码{/t}</div>
        				</div>
        				<div class="control-group formSep">
        					<label class="control-label">{t domain="store"}电子邮箱：{/t}</label>
        					<div class="controls">
        						<input class="span6" name="email" type="text" value="{$store.email}" />
        						<span class="input-must">*</span>
        					</div>
        				</div>
        				<!-- 地区 -->
        				<div class="control-group formSep">
        					<label class="control-label">{t domain="store"}选择地区：{/t}</label>
        					<div class="controls choose_list">
        						<select class="region-summary-provinces w120" name="province" id="selProvinces" data-url="{url path='setting/region/init'}" data-toggle="regionSummary" data-type="2" data-target="region-summary-cities" >
        							<option value='0'>{t domain="store"}请选择...{/t}</option>
        							<!-- {foreach from=$province item=region} -->
        							<option value="{$region.region_id}" {if $region.region_id eq $store.province}selected{/if}>{$region.region_name}</option>
        							<!-- {/foreach} -->
        						</select>
        						<select class="region-summary-cities w120" name="city" id="selCities" data-url="{url path='setting/region/init'}" data-toggle="regionSummary" data-type="3" data-target="region-summary-district">
        							<option value='0'>{t domain="store"}请选择...{/t}</option>
        							<!-- {foreach from=$city item=region} -->
        							<option value="{$region.region_id}" {if $region.region_id eq $store.city}selected{/if}>{$region.region_name}</option>
        							<!-- {/foreach} -->
        						</select>
        						<select class="region-summary-district w120" name="district" id="seldistrict" data-url="{url path='setting/region/init'}" data-toggle="regionSummary" data-type="4" data-target="region-summary-street">
        							<option value='0'>{t domain="store"}请选择...{/t}</option>
        							<!-- {foreach from=$district item=region} -->
        							<option value="{$region.region_id}" {if $region.region_id eq $store.district}selected{/if}>{$region.region_name}</option>
        							<!-- {/foreach} -->
        						</select>
        						<select class="region-summary-street w120" name="street" id="selstreet" >
        							<option value='0'>{t domain="store"}请选择...{/t}</option>
        							<!-- {foreach from=$street item=region} -->
        							<option value="{$region.region_id}" {if $region.region_id eq $store.district}selected{/if}>{$region.region_name}</option>
        							<!-- {/foreach} -->
        						</select>
        						<span class="input-must">*</span>
        					</div>
        				</div>
        				<div class="control-group formSep">
        					<label class="control-label">{t domain="store"}通讯地址：{/t}</label>
        					<div class="controls">
        						<input class="span6" name="address" type="text" value="{$store.address}" />
        						<span class="input-must">*</span>
                                <div class="input-must">
                                    <button class="btn btn-info small-btn" data-toggle="get-gohash" data-url="{url path='store/admin/getgeohash'}">{t domain="store"}获取精准坐标{/t}</button>
                                </div>
        					</div>
        				</div>

        				<div class="control-group formSep">
        					<label class="control-label">{t domain="store"}店铺精确位置：{/t}</label>
        					<div class="controls" style="overflow:hidden;">
        						<div class="span6" id="allmap" style="height:320px;"></div>
        					</div>
                            <div class="m_t30 controls help-block">{t domain="store"}点击选择店铺精确位置，拖动查看地图其他区域{/t}</div>
        				</div>


        				<div class="control-group formSep">
        					<label class="control-label">{t domain="store"}经纬度：{/t}</label>
                            <div class="controls">
                                <div class="l_h30 long f_l"> <input type="text" name="longitude" readonly="true" value="{$store.longitude}"></div>
            					<div class="l_h30 latd f_l m_l10"><input type="text" name="latitude" readonly="true" value="{$store.latitude}"></div>
            					<span class="input-must">*</span>
                            </div>
        				</div>

        				<div class="control-group formSep">
        					<label class="control-label">{t domain="store"}店铺模式：{/t}</label>
        					<div class="controls chk_radio">
								<label class="ecjiafd-iblock"><div class="uni-radio"><input name="manage_mode" type="radio" value="self" checked="checked"/></div><span>{t domain="store"}自营{/t}</span></label>
        					</div>
        				</div>
        				<div class="control-group formSep">
        					<label class="control-label">{t domain="store"}商品审核：{/t}</label>
        					<div class="controls chk_radio">
        					    <label class="ecjiafd-iblock"><div class="uni-radio"><input name="shop_review_goods" type="radio" value="0" {if  $store.shop_review_goods eq '' || $store.shop_review_goods eq 0 }checked="checked" {/if}/></div><span>{t domain="store"}关闭{/t}</span></label>
								<label class="ecjiafd-iblock"><div class="uni-radio"><input name="shop_review_goods" type="radio" value="1" {if  $store.shop_review_goods eq '1'}checked="checked" {/if}/></div><span>{t domain="store"}开启{/t}</span></label>
        					</div>
        					<div class="controls help-block">{t domain="store"}当商店设置中商品审核关闭时，对单个店铺设置失效。{/t}</div>
        				</div>
        				<div class="control-group formSep">
        					<label class="control-label">{t domain="store"}店铺开关：{/t}</label>
        					<div class="controls chk_radio">
        					    <label class="ecjiafd-iblock"><div class="uni-radio"><input name="shop_close" type="radio" value="0" {if  $store.shop_close eq 0}checked="checked" {/if}/></div><span>{t domain="store"}开{/t}</span></label>
								<label class="ecjiafd-iblock"><div class="uni-radio"><input name="shop_close" type="radio" value="1" {if  $store.shop_close eq 1}checked="checked" {/if}/></div><span>{t domain="store"}关{/t}</span></label>
        					</div>
        					<div class="controls help-block">{t domain="store"}当商店设置中开启“商家强制认证”后，未认证通过的商家不能开店和显示{/t}</div>
        				</div>

        				<div class="control-group">
        					<div class="controls">
        						<input type="hidden"  name="store_id" value="{$store.store_id}" />
        						<input type="hidden"  name="step" value="{$step}" />
        						<button class="btn btn-gebo" type="submit">{t domain="store"}确定{/t}</button>
        					</div>
        				</div>
        			</fieldset>
        		</form>
			    </div>
			</div>
		</div>
	</div>
</div>

<!-- {/block} -->
