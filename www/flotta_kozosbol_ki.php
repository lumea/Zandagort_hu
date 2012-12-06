<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['flotta_id']=(int)$_REQUEST['flotta_id'];
$er2=mysql_query('select * from flottak where id='.$_REQUEST['flotta_id']) or hiba(__FILE__,__LINE__,mysql_error());
$aux2=mysql_fetch_array($er2);
if ($aux2['tulaj']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Nem a tied a flotta.']);

mysql_query('update flottak set kozos=0 where id='.$_REQUEST['flotta_id']) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('update flottak set uccso_parancs_by='.$uid.' where id='.$_REQUEST['flotta_id']);
if (flotta_fejvadasz_frissites($_REQUEST['flotta_id'])) flotta_minden_frissites($_REQUEST['flotta_id']);

kilep();
?>