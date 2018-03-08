<?php
@session_start();
@ob_start();
@ob_implicit_flush(0);

@error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);

define('MOZG', true);
define('ROOT_DIR', dirname (__FILE__));
define('ENGINE_DIR', ROOT_DIR.'/system');

header('Content-type: text/html; charset=windows-1251');
	
//AJAX
$ajax = $_POST['ajax'];

$logged = false;
$user_info = false;
include ENGINE_DIR.'/init.php';

//???? ???? ??????? ?? ??? ??????, ?? ????????? ?? ???????? ? ??????
if($_GET['reg']) $_SESSION['ref_id'] = intval($_GET['reg']);

//??????????? ????????
if(stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE 6.0')) $xBrowser = 'ie6';
elseif(stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE 7.0')) $xBrowser = 'ie7';
elseif(stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE 8.0')) $xBrowser = 'ie8';
if($xBrowser == 'ie6' OR $xBrowser == 'ie7' OR $xBrowser == 'ie8')
	header("Location: /badbrowser.php");

//????????? ???-?? ????? ????????
$CacheNews = mozg_cache('user_'.$user_info['user_id'].'/new_news');
if($CacheNews){
	$new_news = "<b class=\"headm_newac\">+{$CacheNews}</b>";
	$news_link = '/notifications';
}

//????? ?????????
$user_pm_num = $user_info['user_pm_num'];
if($user_pm_num)
	$user_pm_num = "<b class=\"headm_newac\">+{$user_pm_num}</b>";
else
	$user_pm_num = '';
  //////////////////////
$user_newg = $user_info['user_guest_num'];
if($user_newg)
	$user_newg = "<b class=\"headm_newac\">+{$user_newg}</b>";
else
	$user_newg = '';
	
//????? ??????
$user_friends_demands = $user_info['demands'];
if($user_friends_demands){
	$demands = "<b class=\"headm_newac\">+{$user_friends_demands}</b>";
	$requests_link = '/requests';
} else
	$demands = '';
	
//??
$user_support = $user_info['user_support'];
if($user_support)
	$support = "<div class=\"newNews2\" style=\"margin-left:50px\">{$user_support}</div>";
else
	$support = '';
	
//??????? ?? ????
if($user_info['user_new_mark_photos']){
	$new_photos_link = 'newphotos';
	$new_photos = "+{$user_info['user_new_mark_photos']}";
} else {
	$new_photos = '';
	$new_photos_link = $user_info['user_id'];
}

//???? ??????? AJAX ?? ????????? ???.
if($ajax == 'yes'){

	//???? ???? POST ?????? ? ???????? AJAX, ? $ajax ?? ????????? "yes" ?? ?? ??????????
	if($_SERVER['REQUEST_METHOD'] == 'POST' AND $ajax != 'yes')
		die('??????????? ??????');

	if($spBar)
		$ajaxSpBar = "$('#speedbar').show().html('{$speedbar}')";
	else
		$ajaxSpBar = "$('#speedbar').hide()";

	$result_ajax = <<<HTML
<script type="text/javascript">
document.title = '{$metatags['title']}';
{$ajaxSpBar};
document.getElementById('new_msg').innerHTML = '{$user_pm_num}';
document.getElementById('newg').innerHTML = '{$user_newg}';
document.getElementById('new_news').innerHTML = '{$new_news}';
document.getElementById('new_support').innerHTML = '{$support}';
document.getElementById('news_link').setAttribute('href', '/news{$news_link}');
document.getElementById('new_requests').innerHTML = '{$demands}';
document.getElementById('new_photos').innerHTML = '{$new_photos}';
document.getElementById('requests_link_new_photos').setAttribute('href', '/albums/{$new_photos_link}');
document.getElementById('requests_link').setAttribute('href', '/friends{$requests_link}');
</script>
{$tpl->result['info']}{$tpl->result['content']}
HTML;
	echo str_replace('{theme}', '/templates/'.$config['temp'], $result_ajax);

	$tpl->global_clear();
	$db->close();

	if($config['gzip'] == 'yes')
		GzipOut();
		
	die();
} 

	if($config['temp'] == 'mobile'){
	
	
		$new_actions = $demands+$new_news+$user_pm_num+$support;
		if($new_actions > 0)
			$tpl->set('{new-actions}', $new_actions);
		else
			$tpl->set('{new-actions}', '');
		
		if($user_info['user_photo'])
			$ava =$config['home_url'].'/uploads/users/'.$user_info['user_id'].'/50_'.$user_info['user_photo'];
		else 
			$ava = '/templates/Default/images/no_ava_50.gif';

		$tpl->set('{mobile-speedbar}', $speedbar);
		$tpl->set('{my-name}', $user_info['user_search_pref']);
		$tpl->set('{status-mobile}', $user_info['user_status']);
		$tpl->set('{my-ava}', $ava);
		
		
	}

//???? ????????? ? ?????? ??????????? ??? ??????? ? ???? ?? ??????????? ?? ?????????? ???????????
if($go == 'register' OR $go == 'main' AND !$logged)
	include ENGINE_DIR.'/modules/register_main.php';

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
	$tpl->load_template('main2.tpl');

}else{
	$tpl->set('{titles}', $metatag);
	$tpl->load_template('main.tpl');
}
   $user_id = $user_info['user_id'];


	$id = intval($_GET['id']);

	//Читаем кеш
	//Проверяем на наличие кеша, если нету то выводи из БД и создаём его 

 
//???? ???? ?????????
if($logged){
			
	$tpl->set_block("'\\[not-logged\\](.*?)\\[/not-logged\\]'si","");
	$tpl->set('[logged]','');
	$tpl->set('[/logged]','');
	$tpl->set('{my-page-link}', '/profile/'.$user_info['user_id']);
	$tpl->set('{my-id}', $user_info['user_id']);
	//?????? ? ??????
	$user_friends_demands = $user_info['demands'];
	if($user_friends_demands){
		$tpl->set('{demands}', $demands);
		$tpl->set('{requests-link}', $requests_link);
	} else {
		$tpl->set('{demands}', '');
		$tpl->set('{requests-link}', '');
	}
  
			
	
				$online_friends = $db->super_query("SELECT COUNT(*) AS cnt FROM `".PREFIX."_users` tb1, `".PREFIX."_friends` tb2 WHERE tb1.user_id = tb2.friend_id AND  tb2.user_id = '{$user_id}' AND tb1.user_last_visit >= '{$online_time}' AND subscriptions = 0");
			
				
	  if($online_friends['cnt']){
				$tpl->set('[online-friends]', '');
				$tpl->set('[/online-friends]', '');
        $tpl->set('{online-friends-num}', $online_friends['cnt']);
				$tpl->set('{online-friends}', '');
        	$tpl->set('{friends-num}', $row['user_friends_num']);
			} else
				$tpl->set_block("'\\[online-friends\\](.*?)\\[/online-friends\\]'si","");
	//???????
	if($CacheNews){
		$tpl->set('{new-news}', $new_news);
		$tpl->set('{news-link}', $news_link);
	} else {
		$tpl->set('{new-news}', '');
		$tpl->set('{news-link}', '');
	}
	
	//?????????
	if($user_pm_num)
		$tpl->set('{msg}', $user_pm_num);
	else 
		$tpl->set('{msg}', '');
  //chat
	
			$sql_friends = $db->super_query("SELECT SQL_CALC_FOUND_ROWS tb1.friend_id, tb2.user_id, user_search_pref, user_photo FROM `".PREFIX."_friends` tb1, `".PREFIX."_users` tb2 WHERE tb1.user_id = '{$user_info['user_id']}' AND tb1.friend_id = tb2.user_id  AND subscriptions = 0 ORDER by rand() DESC LIMIT 0, 100", 1);
			$chat = " ";
				foreach($sql_friends as $row_chat){
				if($row_chat['user_photo'])
						$ava = $config['home_url'].'uploads/users/'.$row_chat['friend_id'].'/50_'.$row_chat['user_photo'];
					else
						$ava = '{theme}/images/no_ava_50.png';
					$chat .= '<div class=\'im_chatuser cursor_pointer\' onclick=\'im_chat.opens("'.$row_chat['user_id'].'","'.$row_chat['user_search_pref'].'","");\' id=\'dialog'.$row_chat['user_id'].'\'>
<img src=\''.$ava.'\'>
<div class=\'im_nameu fl_l\'>'.$row_chat['user_search_pref'].'</div><span class=\'fl_r\'></span>
<span id=\'upNewMsg'.$row_chat['user_id'].'\'></span>
<div class=\'clear\'></div>
</div>';
				}
				$tpl->set('{chat}', $chat);
				$tpl->set('{chat_num}', $user_info['user_friends_num']);  
	//?????????
	if($user_newg)
		$tpl->set('{newg}', $user_newg);
	 else
		$tpl->set('{newg}', '');
   	
	//?????????
	if($user_support)
		$tpl->set('{new-support}', $support);
	else 
		$tpl->set('{new-support}', '');
	
	//??????? ?? ????
	if($user_info['user_new_mark_photos']){
		$tpl->set('{my-id}', 'newphotos');
		$tpl->set('{new_photos}', $new_photos);
	} else 
		$tpl->set('{new_photos}', '');

	//UBM
	if($CacheGift){
		$tpl->set('{new-ubm}', $new_ubm);
		$tpl->set('{ubm-link}', $gifts_link);
	} else {
		$tpl->set('{new-ubm}', '');
		$tpl->set('{ubm-link}', $gifts_link);
	}


} else {
	$tpl->set_block("'\\[logged\\](.*?)\\[/logged\\]'si","");
	$tpl->set('[not-logged]','');
	$tpl->set('[/not-logged]','');
	$tpl->set('{my-page-link}', '');
}






  
$tpl->set('{header}', $headers);
$tpl->set('{speedbar}', $speedbar);
$tpl->set('{info}', $tpl->result['info']);
$tpl->set('{content}', $tpl->result['content']);
$tpl->set('{usernew}', $tpl->result['usernew']);
if($spBar)
	$tpl->set_block("'\\[speedbar\\](.*?)\\[/speedbar\\]'si","");
else {
	$tpl->set('[speedbar]','');
	$tpl->set('[/speedbar]','');
}

$obshenie = $db->super_query("SELECT user_id,text FROM `".PREFIX."_obshenie` WHERE date>'NOW()-604800' ORDER BY RAND() LIMIT 1");

if($obshenie) {
	$avatar_obshenie = $db->super_query("SELECT user_photo, user_search_pref FROM `".PREFIX."_users` WHERE user_id = '{$obshenie['user_id']}'");
	$tpl->set('{avatar_obshenie}', '<a href="id'.$obshenie['user_id'].'"><img src="http://ok.wm-scripts.ru/uploads/users/'.$obshenie['user_id'].'/100_'.$avatar_obshenie['user_photo'].'"/></a>');
	$tpl->set('{name_obshenie}', '<a href="id'.$obshenie['user_id'].'">'.$avatar_obshenie['user_search_pref'].'</a>');
	$tpl->set('{text}', $obshenie['text']);
	$tpl->set('[obshenie]','');
	$tpl->set('[/obshenie]','');
} else {
	$tpl->set_block("'\\[obshenie\\](.*?)\\[/obshenie\\]'si","");
}

//BUILD JS
if($config['gzip_js'] == 'yes')
	if($logged)
		$tpl->set('{js}', '<script type="text/javascript" src="/min/index.php?charset=windows-1251&amp;g=general&amp;6"></script>');
	else
		$tpl->set('{js}', '<script type="text/javascript" src="/min/index.php?charset=windows-1251&amp;g=no_general&amp;6"></script>');
else
	if($logged)
		$tpl->set('{js}', '<script type="text/javascript" src="{theme}/js/jquery.lib.js"></script><script type="text/javascript" src="{theme}/js/main.js"></script><script type="text/javascript" src="{theme}/js/profile.js"></script>');
	else
		$tpl->set('{js}', '<script type="text/javascript" src="{theme}/js/jquery.lib.js"><script type="text/javascript" src="{theme}/js/jquery.js"></script><script type="text/javascript" src="{theme}/js/main.js"></script>');

$tpl->compile('main');

echo str_replace('{theme}', '/templates/'.$config['temp'], $tpl->result['main']);

      
   	  
$tpl->global_clear();
$db->close();

if($config['gzip'] == 'yes')
	GzipOut();
?>