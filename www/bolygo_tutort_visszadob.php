<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['bolygo_id']=(int)$_REQUEST['bolygo_id'];
$er2=mysql_query('select * from bolygok where id='.$_REQUEST['bolygo_id']) or hiba(__FILE__,__LINE__,mysql_error());
$aux2=mysql_fetch_array($er2);
if ($aux2['kezelo']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Nem te vagy a bolygó tutora.']);

mysql_query('update bolygok set kezelo=0 where id='.$_REQUEST['bolygo_id']) or hiba(__FILE__,__LINE__,mysql_error());

kilep();
?>