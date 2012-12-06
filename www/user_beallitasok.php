<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');
if (!$ismert) kilep();

$_REQUEST['mire']=(int)$_REQUEST['mire'];

if(in_array($_REQUEST['mit'],array('iparag_jelzok','gyar_ikonok','kozos_flottak_listaban'
,'email_noti_eplista','email_noti_epites_alatt','css_munkahelyi'
,'chat_hu','chat_en'
,'badge_pub'
))) mysql_query('update user_beallitasok set '.$_REQUEST['mit'].'='.$_REQUEST['mire'].' where user_id='.$uid);

kilep();
?>