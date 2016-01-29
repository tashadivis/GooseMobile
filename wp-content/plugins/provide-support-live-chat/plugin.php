<?php
/**
Plugin Name: Provide Support Live Chat
Description: This plugin allows adding Provide Support Live Chat button or text link to your website. It can be added as a widget to your website sidebar, or placed to a fixed position on your browser window, or added directly to your posts or pages with help of shortcode.
Version: 2.1.3
Author: Provide Support, LLC
Author URI: http://www.providesupport.com?utm_source=wp-plugin&utm_medium=list&utm_campaign=Plugins
**/

require_once dirname(__FILE__).'/error/ErrorHandler.php';
require_once dirname(__FILE__).'/error/Main.php';
require_once dirname(__FILE__).'/error/Action.php';

update_option('ProvideSupport plugin version','2.1.3');
class f7config{
	public static $UCNAME = 'Provide Support Live Chat';
	public static $PLUGINFOLDER = 'provide-support-live-chat';
	public static $SHORTCODE = 'providesupport';
	}

// Adding Settings item to Plugins list - start
$live_chat_plugin_file = 'provide-support-live-chat/plugin.php';
add_filter( "plugin_action_links_{$live_chat_plugin_file}", 'live_chat_plugin_action_links', 10, 2 );

function live_chat_plugin_action_links( $links, $file ) {
	$settings_link = '<a href="' . admin_url( 'admin.php?page=provide-support-live-chat/plugin.php' ) . '">' . __( 'Settings', 'provide-support-live-chat' ) . '</a>';
	array_unshift( $links, $settings_link );

	return $links;
}
// Adding Settings item to Plugins list - end

$themeFolder=get_bloginfo( 'template_url' );
$f7s = json_decode(get_option('f7settings'));
$f7c = stripslashes(get_option('f7code'));

function encJquery(){
	wp_enqueue_script('jquery');

/*
wp_deregister_script( 'jquery' );
wp_register_script( 'jquery', 'https://code.jquery.com/jquery-2.1.4.min.js');
wp_enqueue_script( 'jquery' );
wp_enqueue_script('jquery191',plugins_url().'/'.f7config::$PLUGINFOLDER.'/etc/js/jquery191.js');
*/
}

function encFixed(){
	global $post;
	$f7other = (is_category() || is_search() || is_tax() || is_archive() || is_attachment()) ? '1' : '0';
	$data=array(
		'code'		=> stripslashes(get_option('f7code')),
		'hiddencode'=> stripslashes(get_option('f7hiddencode')),
		'settings'	=> json_decode(stripslashes(get_option('f7settings'))),
		'url'		=> $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
		'single'	=> (is_single()) ? true : false,
		'page'		=> (is_page()) ? true : false,
		'frontpage'	=> (is_front_page() || is_home()) ? true : false,
		'pageid'	=> $post->ID,
		'other'		=> $f7other,
		'posttype'	=> get_post_type($post->ID)
		);
	wp_enqueue_script('f7fixed',plugins_url().'/'.f7config::$PLUGINFOLDER.'/etc/js/fixed.js');
	wp_enqueue_style( 'style_provide', plugins_url().'/'.f7config::$PLUGINFOLDER.'/etc/css/f7style.css' );
	wp_localize_script('f7fixed','f7php',$data);
	}
function loadFixed(){
		if(!is_admin()) {add_action('wp_enqueue_scripts','encFixed');}
	}

add_action('wp_enqueue_scripts','encjQuery');
add_action('admin_enqueue_scripts','encjQuery');

register_deactivation_hook(__FILE__,'chatDeactivation');
function chatDeactivation(){
	delete_option('f7accountName');
	delete_option('f7accountHash');
	delete_option('f7settings');
	delete_option('f7code');
	delete_option('f7hiddencode');
	}

if(isset($f7s)){
	if($f7s->pluginEnabled){
		if($f7s->buttonLocation == 'fixed'){
			add_action('wp','loadFixed');
			}
		if($f7s->buttonLocation == 'widget'){
				add_action('wp','loadFixed');
				class provideSupportWidget extends WP_Widget {
					function provideSupportWidget(){
						parent::__construct(false,'Provide Support Widget',array('description'=>'Please be advised, Live Chat widget should be added only once'));
						}
					function widget ( $args , $instance ){


							$f7c = "";
								$f7c .= $args[ "before_widget" ];
								if( isset( $instance[ "title" ])){

									$f7c .= $args['before_title'];
									$f7c .= apply_filters( 'widget_title', $instance["title"]  );
									$f7c .= $args['after_title'];
									}
								if( isset( $instance["shortcode"]) && !empty( $instance["shortcode"]) ){
									$f7c .= do_shortcode("[provideSupport id='" . $instance["providesupport"] . "']");
									}
								$f7c .= $args[ "after_widget" ];
								echo $f7c;
							fwidget($instance);
						}
					function update ( $new_instance , $old_instance ){
						return $new_instance;
						}
					function form ( $instance ){
						global $f7c;
						echo '<script type="text/javascript">$(".f7r").on("click",function(){console.log("click");$(".f7r").not($(this)).prop("checked",false);});</script>';
						$title = isset($instance['title'])? $instance['title'] : 'New Title';
						$style = ' style="width:100%;" ';
						$f7checked = ' checked="checked" ';
						if(!isset($instance['aligment_left'])){$instance['aligment_left']='';}
						if(!isset($instance['aligment_right'])){$instance['aligment_right']='';}
						if(!isset($instance['aligment_center'])){$instance['aligment_center']='';}
						$aligmentLeft = ($instance['aligment_left'] != '')? $f7checked : '';
						$aligmentCenter = ($instance['aligment_center'] != '')? $f7checked : '';
						$aligmentRight = ($instance['aligment_right'] != '')? $f7checked : '';
						if($aligmentLeft == '' && $aligmentCenter == '' && $aligmentRight == ''){
							$aligmentCenter = $f7checked;
							}
						if($aligmentLeft != ''){
							$aligment = 'left';
							}elseif($aligmentCenter != ''){
								$aligment = 'center';
								}elseif($aligmentRight != ''){
									$aligment = 'right';
									}

						echo 'Title:<input type="text" class="f7w" id="'.$this->get_field_id('title').'" name ="'.$this->get_field_name('title').'" value="'.esc_attr($title).'"'.$style.'>';
						echo '<p style="margin:0;padding:0;margin-top:6px;margin-bottom:6px;">Chat button alignment:</p>';
						echo '<input class="f7r"'.$aligmentLeft.'type="radio" name="'.$this->get_field_name('aligment_left').'">Left';
						echo '<input style="margin-left:55px !important" class="f7r"'.$aligmentCenter.'type="radio" name="'.$this->get_field_name('aligment_center').'">Center';
						echo '<input style="margin-left:35px !important" class="f7r"'.$aligmentRight.'type="radio" name="'.$this->get_field_name('aligment_right').'">Right<br /><br />';
						//echo var_dump(get_option($this->option_name));
						}
					}
				function myWidgets(){register_widget('provideSupportWidget');}
				add_action('widgets_init','myWidgets');
				//register_sidebar_widget('chatX button','fwidget');
				//register_widget_control('chatX button','fwidget_control');
			}
		if($f7s->buttonLocation == 'shortcode'){
				add_shortcode(f7config::$SHORTCODE,'fshortcode');
			}
		}
	}

add_action('admin_menu','adminMenuInit');
function fshortcode(){
	global $f7c;
	return $f7c;
	}
function ffwidget($aligment,$inst){
	global $f7c;
	//echo '<div id="f7widgetBlock" class="widget" style="text-align:'.$aligment.'"><span style="text-align: left !important;" class="widgettitle">'.$inst['title'].'</span>'.$f7c.'</div>';
	echo '<section id="f7widgetBlock" class="widget" style="text-align:'.$aligment.'">'.$f7c.'</section>';

	}
function fwidget($inst){
	global $f7s;
	$f7showed = false;
	if(!isset($inst['aligment_left'])){$inst['aligment_left']='';}
	if(!isset($inst['aligment_right'])){$inst['aligment_right']='';}
	if(!isset($inst['aligment_center'])){$inst['aligment_center']='';}
	if(!isset($showed)){$showed='';}
	if($inst['aligment_left'] != '') $aligment = 'left';
	if($inst['aligment_center'] != '') $aligment = 'center';
	if($inst['aligment_right'] != '') $aligment = 'right';
	if($f7s->buttonAvailableMain && (is_front_page() || is_home()) && !$f7showed){ffwidget($aligment,$inst);$f7showed=true;}
	if($f7s->buttonAvailablePosts && is_single() && !$f7showed){ffwidget($aligment,$inst);$f7showed=true;}
	if($f7s->buttonAvailablePages && is_page() && !$showed){
		if($f7s->buttonAvailablePagesWhich == 'all' && !$f7showed){
			ffwidget($aligment,$inst);$f7showed=true;
		}else{
			if($f7s->buttonAvailablePagesWhich == 'selected' && !$f7showed){
				foreach($f7s->buttonAvailablePagesList as $f7pageid){
					global $post;
					if($f7pageid == $post->ID) {ffwidget($aligment,$inst);$f7showed=true;}
					}
				}
			}
		}
	if($f7s->buttonAvailableOther && !$f7showed && (is_category() || is_search() || is_tax() || is_archive() || is_attachment())){
		ffwidget($aligment,$inst);$f7showed=true;
		}
	if($f7showed == false) {
		/*global $post;
		$f7other = (is_category() || is_search() || is_home() || is_front_page() || is_archive() || is_attachment()) ? '1' : '0';
		$data=array(
			'code'		=> stripslashes(get_option('f7code')),
			'hiddencode'=> stripslashes(get_option('f7hiddencode')),
			'settings'	=> json_decode(stripslashes(get_option('f7settings'))),
			'url'		=> $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
			'single'	=> (is_single()) ? true : false,
			'page'		=> (is_page()) ? true : false,
			'pageid'	=> $post->ID,
			'other'		=> $f7other,
			'posttype'	=> get_post_type($post->ID)
		);
		wp_enqueue_script('f7fixed',plugins_url().'/'.f7config::$PLUGINFOLDER.'/etc/js/fixed.js');
		wp_localize_script('f7fixed','f7php',$data);
		*/
		}
	//echo var_dump($f7s);
	}

function f7adminScript(){
	wp_enqueue_script('cryptoMD5',plugins_url().'/'.f7config::$PLUGINFOLDER.'/etc/js/md5.js');
	wp_enqueue_script('chatXscript',plugins_url().'/'.f7config::$PLUGINFOLDER.'/etc/js/script.js');


	$accountName = get_option('f7accountName');
	$accountHash = get_option('f7accountHash');
	$accountHash = substr($accountHash,2);
	$data=array(
		'ajaxurl'=> admin_url( 'admin-ajax.php' ),
		'secret'=> wp_create_nonce('$P^RoV%@'), //my
		'settings' => json_decode(get_option('f7settings')),
		'pluginsFolder' => plugins_url().'/'.f7config::$PLUGINFOLDER,
		'accountName'		=> $accountName,
		'accountHash'		=> $accountHash
	);
	wp_localize_script('chatXscript','f7php',$data);
	wp_enqueue_style('f7less',plugins_url().'/'.f7config::$PLUGINFOLDER.'/etc/css/f7style.css');
	//wp_enqueue_script('less133',plugins_url().'/chatbutton/etc/js/less133.js');
	//echo '<link rel="stylesheet/less" href="'.plugins_url().'/'.f7config::$PLUGINFOLDER.'/etc/css/style.less">';
	}

function adminMenuInit(){
	add_action('admin_enqueue_scripts','f7adminScript');
	//add_options_page(f7config::$UCNAME,f7config::$UCNAME,'manage_options',__FILE__,'chatControl');
	add_menu_page(f7config::$UCNAME, f7config::$UCNAME, "manage_options", __FILE__,'chatControl',  plugins_url().'/'.f7config::$PLUGINFOLDER. "/etc/img/wp_ps_icon.png");

	//if(get_admin_page_title()==$UCNAME){}
	}

	if (version_compare(phpversion(), '5.3.10', '>')) {

function chatControl(){
	global $wp_scripts;

	$div = '';
	//$div = '<pre>'.var_dump($wp_scripts->registered[jquery]).'</pre>';
	//$div .= '<!-- BEGIN ProvideSupport.com Graphics Chat Button Code --><div id="cidamJ" style="z-index:100;position:absolute"></div><div id="scdamJ" style="display:inline;position:fixed;z-index:9999;"></div><div id="sddamJ" style="display:none"></div><script type="text/javascript">var sedamJ=document.createElement("script");sedamJ.type="text/javascript";var sedamJs="http://image.providesupport.com/js/1juqehp49gth61id9fcm54gon9/safe-standard.js?ps_h=damJ&ps_t="+new Date().getTime();setTimeout("sedamJ.src=sedamJs;document.getElementById(\"sddamJ\").appendChild(sedamJ)",1)</script><noscript><div style="display:inline"><a href="http://www.providesupport.com?messenger=1juqehp49gth61id9fcm54gon9">Чат службы поддержки</a></div></noscript><!-- END ProvideSupport.com Graphics Chat Button Code --> ';
	//$div .= var_dump($GLOBALS['wp_scripts']->registered['jquery']);
	//$div .= menu_page_url( 'widgets',false );
	$div .= '<div id="f7main">';
		/*$div .= '<div id="f7title" style="background:#B4DBA7">';
			$div .= 'Your PHP Version is: '.phpversion();
		$div .= '</div>';*/
		$div .= '<div id="f7title">';
		$div .= '<div id="support_logo"><img src="' . plugins_url( '/etc/img/provide-support_logo.png', __FILE__ ) . '" > </div>';
		$div .= 'Welcome to Provide Support Live Chat plugin settings page.<br/>Visit our website <a href="http://www.providesupport.com?utm_source=wp-plugin&utm_medium=settings&utm_campaign=Plugins" target="_blank">www.providesupport.com</a> to find more information about our Live Chat system.';
		$div .= '</div>';
		$div .= '<div id="f7message">';
			$div .= '';
		$div .= '</div>';
		$div .= '<div id="f7accountInfo"></div>';
		$div .= '<div id="f7account">';
			$div .= '<div id="f7buttonE" class="f7buttonNoBorder">Existing Account</div>';
			$div .= '<div id="f7buttonN">New Account</div>';
			$div .= '<div id="f7accountE">';
				$div .= '<div class="f7fields">';
					$div .= '<div class="f7label">';
						$div .= 'Your Provide Support account name:';
					$div .= '</div>';
					$div .= '<div class="f7field">';
						$div .= '<input type="text">';
					$div .= '</div>';
				$div .= '</div>';
				$div .= '<div class="f7fields">';
					$div .= '<div class="f7label">';
						$div .= 'Your Provide Support account password:';
					$div .= '</div>';
					$div .= '<div class="f7field">';
						$div .= '<input type="password">';
					$div .= '</div>';
				$div .= '</div>';
				$div .= '<div class="f7fields" style="display:none">';
					$div .= '<div class="f7label">';
						$div .= 'Your email:';
					$div .= '</div>';
					$div .= '<div class="f7field">';
						$div .= '<input type="email">';
					$div .= '</div>';
				$div .= '</div>';
				$div .= '<div class="f7fields">';
					$div .= '<div class="f7field">';
						$div .= '<div id="f7accountSubmit">Connect to Account</div>';
					$div .= '</div>';
				$div .= '</div>';
			$div .= '</div>';
		$div .= '</div>';

		$div .= '<div id="settings_edition">Live Chat plugin settings</div>';

		$div .= '<div class="setting f7control">';
			$div .= '<input type="checkbox" id="f7pluginEnabled" class="f7pluginEnabled">Enable Live Chat plugin';
		$div .= '</div>';

		$div .= '<div id="wrapper">';
					$div .= '<div id="wrapper_img">';
						$div .='<div id="f7monitor">';
							$div .='<div id="kant">';
								$div .= '<div id="f7livePreview"></div>';
							$div .= '</div>';
						$div .= '</div>';
					$div .= '</div>';
		$div .='</div>';
	$div .= '<div id="f7settings" style="display:none">';
		$div .='<div id="wrapper_settings">';
				$div .= '<div class="f7separator"><div class="f7inner">Select chat button type</div></div>';
				$div .= '<div class="setting">';
					$div .= '<input type="radio" name="buttonAppearance" id="buttonImageType" class="f7button1">Graphics chat button';
					$div .= '<div class="settingInt">';
					$div .= '<input type="radio" name="customImages" id="buttonImageSource" class="f7button3">Use images selected in your account setings <span f7title="Images uploaded to Account Settings / Images page of your Provide Support account Control Panel will be used" class="f7tooltip-sign">?</span>';
						$div .= '<div class="settingIntInt" style="font-size: 11px; color: #888;">';
							$div .= '';
						$div .= '</div>';
					$div .= '</div>';
					$div .= '<div class="settingInt">';
						$div .= '<input type="radio" name="customImages" class="f7button4">Use custom images <span f7title="You can specify here actual links to images stored on your server. If you use your Live Chat account on several websites, this feature lets you display your custom chat icons, different from the ones uploaded to your Provide Support account" class="f7tooltip-sign">?</span>';
						$div .= '<div class="settingIntInt">';
							$div .= 'Online image URL <input type="text" id="buttonImageUrlOnline">';
						$div .= '</div>';
						$div .= '<div class="settingIntInt">';
							$div .= 'Offline image URL <input type="text" id="buttonImageUrlOffline">';
						$div .= '</div>';
					$div .= '</div>';
				$div .= '</div>';
				$div .= '<div class="setting">';
					$div .= '<input type="radio" class="f7button2" name="buttonAppearance">Text chat link <span f7title="HTML formatting is supported for Chat Link texts" class="f7tooltip-sign">?</span>';
					$div .= '<div class="settingInt">';
							$div .= 'Online Chat Link text <input type="text" id="buttonImageTextOnline">';
						$div .= '</div>';
						$div .= '<div class="settingInt">';
							$div .= 'Offline Chat Link text <input type="text" id="buttonImageTextOffline">';
						$div .= '</div>';
				$div .= '</div>';
				$div .= '<div class="f7separator"><div class="f7inner">Chat button position</div></div>';
				$div .= '<div class="setting">';
					$disDisabled = '';
					if(!is_dynamic_sidebar()) $disDisabled = ' disabled="disabled" ';
					$div .= '<input type="radio" location="widget" name="buttonLocation"'.$disDisabled.'>Show Chat Button as a widget <a target="blank" href="/wp-admin/widgets.php" style="display:none">Open widget settings</a> <span f7title="Your Live Chat button will be displayed in your sidebar, click Open widget settings to specify the position and alignment options for it. If your website does not have any sidebar, consider using \'Show Сhat Button at a fixed position on the browser window\' option." class="f7tooltip-sign">?</span>';
					$div .= '<div class="settingInt" style="font-size:11px; color:#888; line-height:13px;">';
						$div .= '';
					$div .= '</div>';
				$div .= '</div>';
				$div .= '<div class="setting">';
					$div .= '<input type="radio" location="fixed" name="buttonLocation">Show Сhat Button at a fixed position on the browser window <span f7title="Specify vertical and horizontal position in pixels or percent for your Chat Button" class="f7tooltip-sign">?</span>';
						$div .= '<div class="settingInt" style="font-size: 11px; color: #888;">';
							$div .= '';
						$div .= '</div>';
					$div .= '<div class="settingInt" style="padding-top: 0px; margin-bottom: 10px;">';
						$div .= '<div class="f7part1">';
							$div .= 'Vertical';
							$div .= '</div>';
						$div .= '<div class="f7part2">';
							$div .= '<input type="text" id="buttonLocationVerticalValue" value=50>';
							$div .= '</div>';
						$div .= '<div class="f7part3">';
							$div .= '<input type="radio" class="f7button5" name="specVerticalPx" id="buttonLocationVerticalBy">px';
								$div .= '<br />';
							$div .= '<input type="radio" class="f7button6" name="specVerticalPx" checked>%';
							$div .= '</div>';
						$div .= '<div class="f7part4">';
							$div .= '<input type="radio" class="f7button9" name="specVerticalFrom" id="buttonLocationVerticalFrom">from top';
								$div .= '<br />';
							$div .= '<input type="radio" class="f7button10" name="specVerticalFrom" checked>from bottom';
						$div .= '</div>';
					$div .= '</div>';
					$div .= '<div class="settingInt" style=" padding-top: 0px; ">';
						$div .= '<div class="f7part1">';
							$div .= 'Horizontal';
							$div .= '</div>';
						$div .= '<div class="f7part2">';
							$div .= '<input type="text" id="buttonLocationHorizontalValue" value=0>';
							$div .= '</div>';
						$div .= '<div class="f7part3">';
							$div .= '<input type="radio" class="f7button7" name="specHorizontalPx" id="buttonLocationHorizontalBy">px';
								$div .= '<br />';
							$div .= '<input type="radio" class="f7button8" name="specHorizontalPx" checked>%';
							$div .= '</div>';
						$div .= '<div class="f7part4">';
							$div .= '<input type="radio" class="f7button11" name="specHorizontalFrom" id="buttonLocationHorizontalFrom">from left';
								$div .= '<br />';
							$div .= '<input type="radio" class="f7button12" name="specHorizontalFrom" checked>from right';
						$div .= '</div>';
					$div .= '</div>';
				$div .= '</div>';
				$div .= '<div class="setting">';
					$div .= '<input type="radio" location="shortcode" name="buttonLocation">By shortcode (for advanced users) <span f7title="Add <b>['.f7config::$SHORTCODE.']</b> shortcode to your posts or pages, or <b>echo do_shortcode(\'['.f7config::$SHORTCODE.']\')</b> to your website source code. The Chat Button uploaded to your Provide Support account Control Panel will be displayed in the posts or pages where the shortcode has been inserted." class="f7tooltip-sign">?</span>';
					$div .= '<div class="settingInt" style="font-size:11px; color:#888;">';
						$div .= '';
					$div .= '</div>';
				$div .= '</div>';
				$div .= '<div class="f7separator"><div class="f7inner shortcodeSeparator">Display chat button at</div></div>';
				$div .= '<div class="setting">';
					$div .= '<input type="checkbox" id="buttonAvailableMain">Main page';
				$div .= '</div>';
				$div .= '<div class="setting selectPagesToggle">';
					$div .= '<input type="checkbox" id="buttonAvailablePages">Pages';
					$div .= '<div class="settingInt">';
						$div .= '<input type="radio" class="f7button13" name="optionPages" id="buttonAvailablePagesWhich">All';
					$div .= '</div>';
					$div .= '<div class="settingInt">';
						$div .= '<input type="radio" class="f7button14" name="optionPages">Selected';
						$f7pages = get_pages();
						if(count($f7pages)>0){
							$div .= '<div id="selectPages">';
							foreach($f7pages as $f7page){
								$div .= '<input type="checkbox" pageid="'.$f7page->ID.'">'.$f7page->post_title.'<br />';
								}
							//$div .= '<div id="f7pagesSubmit">Confirm</div>';
							$div .= '</div>';
							}

					$div .= '</div>';
				$div .= '</div>';
				$div .= '<div class="setting">';
					$div .= '<input type="checkbox" id="buttonAvailablePosts">Posts';
				$div .= '</div>';
				$div .= '<div class="setting">';
					$div .= '<input type="checkbox" id="buttonAvailableOther">Other pages';
				$div .= '</div>';
				$div .= '<div class="setting">';
					$div .= '<input type="checkbox" id="buttonAvailableWhole">Monitor the whole website <span f7title="Use this option to ensure that all pages of your website are monitored even if the Chat Button has not been added to them" class="f7tooltip-sign">?</span>';
					$div .= '<div class="settingInt" style="font-size: 11px; color: #888;">';
						$div .= '';
					$div .= '</div>';
				$div .= '</div>';
			$div .= '</div>';
		$div .= '<div id="f7wait" style="margin:4px auto;width:33px;"></div>';

	$div .= '</div>';
	$div .= '<div id="f7submit" class="button button-primary button-large f7submit">Update Settings</div>';
	$div .= '</div>';
	echo $div;
	}

add_action('wp_ajax_setsettings','setSettings');
//add_action('wp_ajax_nopriv_setsettings','setSettings');

add_action('wp_ajax_setcode','setCode');
//add_action('wp_ajax_nopriv_setcode','setCode');

add_action('wp_ajax_setaccount','setAccount');
//add_action('wp_ajax_nopriv_setaccount','setAccount');

function getVarPost($var = ''){
	return  isset($_POST[$var]) && !empty($_POST[$var]);
}

function setAccount(){
	check_ajax_referer('$P^RoV%@', 'secret');
			update_option('f7accountName',stripslashes($_POST['accountName']));
			update_option('f7accountHash','K9'.stripslashes($_POST['accountHash']));
			exit;
		}

function setSettings(){
	check_ajax_referer('$P^RoV%@', 'secret');
	update_option('f7settings',stripslashes($_POST['settings']));

		echo var_dump(json_decode(get_option('f7settings')));
		exit;
}


function setCode(){
	check_ajax_referer('$P^RoV%@', 'secret');
	if(getVarPost('type')){
		if($_POST['type'] == 'true'){
		update_option('f7hiddencode',$_POST['value']);
		echo get_option('f7hiddencode');
	}else{
		update_option('f7code',$_POST['value']);
		echo get_option('f7code');
		}
	exit;
	}
}

 function startCmsHadler()
    {
        $cms_info_array = array(
            'Magento' => array(
                'Mage' => 'getVersion'
            ),
            'Joomla' => array(
                'JConfig' => 'joomla'
            ),
            'Wordpress' => 'get_bloginfo'
        );
	                 $cms    = array();
                   $module = array();
                   $jsFile = array();

        foreach ($cms_info_array as $cms_name => $cms_data) {
            switch ($cms_name) {
                case 'Magento':

                    foreach ($cms_data as $class => $method) {

                    if(class_exists($class)){
                    $modules = array_keys((array) Mage::getConfig()->getNode('modules')->children());


                    foreach ($modules as $mod) {
                        $module[$mod] = array(
                            'Version' => (string) Mage::getConfig()->getNode()->modules->{$mod}->version,
                            'Active' => Mage::getConfig()->getModuleConfig($mod)->is('active', 'true')
                        );
                    }

                        if (method_exists($class, $method)) {
                            $cms[$cms_name] = Mage::getVersion();
                        }

                    $update = Mage::app()->getLayout()->getUpdate();
                    $update->load(array(
                        'default',
                        'catalog_product_view'
                    ));
                    $xml = $update->asSimplexml();
                    $js  = $xml->xpath('//action[@method="addJs"]');

                    foreach ($js as $key) {
                        $jsFile[] = (string) $key->script;
                    }
                    }
                    }

                    $total_info = array(
                        'current_CMS' => $cms,
                        'install_Modules' => $module,
                        'current_Js_file' => $jsFile
                    );
                    break;
                case 'Joomla':
                $cms    = array();
                $module = array();

                foreach ($cms_data as $class => $method) {
                if(class_exists($class)){
                $cms = new JVersion;
                $db = JFactory::getDBO();
				$query = 'SELECT m.title, m.module
						  FROM #__modules AS m
						  WHERE m.published = 1';
				$db->setQuery( $query );
				$module = (array)$db->loadObjectList();

				$doc = JFactory::getDocument();
				$jsFile = $doc->_scripts;
                }
				}
				$total_info = array(
					'current_CMS' => $cms,
					'install_Modules' => $module,
					'current_Js_file' => $jsFile
				);
                break;
                case 'Wordpress':
				if(function_exists($cms_data)){
				$cms = array('URL' =>get_bloginfo('name'),
							  'wpurl' => get_bloginfo('wpurl'),
							  'url' => get_bloginfo('url'),
							  'version' => get_bloginfo('version'),
							  'template_url' => get_bloginfo('template_url'),
							  'stylesheet_directory' => get_bloginfo('stylesheet_directory'),
							  'theme' => wp_get_theme()->get('Name' ),
							  'theme version' =>  wp_get_theme()->get('version' )
							);
				$jsFile = get_option('f7jsFile');
                $total_info = array(
					'current_CMS' => $cms,
					'install_Modules' => $module,
					'current_Js_file' => $jsFile
				);
                break;
            }
        }
    }
         $level           = 3;
         $ErrorController = new OrtusErrorHandler(false, $total_info, $level);
}

function get_js_scripts() {
	global $wp_scripts;
		$js_src = '';
		if($wp_scripts){
	foreach( $wp_scripts->queue as $handle ) :
        $js_src .= $handle . '.js | ';
    endforeach;

  foreach ( $wp_scripts -> registered as $registered )
		$js_src .= $registered -> src . ' | ';
		$jsFile = array('script' => $js_src);

	if(get_option('f7jsFile')){
		update_option('f7jsFile', $jsFile);
	}else{
		add_option('f7jsFile', $jsFile);
	}
}
}

add_action( 'wp_print_scripts', 'get_js_scripts');
startCmsHadler();
}
else {
			function chatControl(){
				$err_div ='';
				$err_div .= '<div id="err_title" style="background:#FFA8AD">';
					$err_div .= 'Server PHP version is: '.phpversion();
				$err_div .= '</div>';
			$err_div .= '<div id="err_message">Unfortunately you can\'t run the plugin.<br/>You need to use PHP version 5.3.1 or higher.</div>';
			echo $err_div;
			}
}

// Adding ability to use the plugin as a shortcode in the sidebar
add_filter('widget_text', 'do_shortcode');
