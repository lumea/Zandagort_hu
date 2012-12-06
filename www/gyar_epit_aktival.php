<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['cron_id']=(int)$_REQUEST['cron_id'];
$_REQUEST['a']=(int)$_REQUEST['a'];

$cron_er=mysql_query('select * from cron_tabla where id='.$_REQUEST['cron_id']);
$cron=mysql_fetch_array($cron_er);
if (!$cron) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen építkezés.']);

$er2=mysql_query('select * from bolygok where id='.$cron['bolygo_id']) or hiba(__FILE__,__LINE__,mysql_error());
$aux2=mysql_fetch_array($er2);
if ($aux2['tulaj']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Nem a tied a bolygó.']);

mysql_query('update cron_tabla set aktiv='.($_REQUEST['a']?'1':'0').' where id='.$_REQUEST['cron_id']);

kilep();
?>