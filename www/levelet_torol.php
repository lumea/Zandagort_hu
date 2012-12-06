<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if (!$ismert) kilep();

$_REQUEST['id']=(int)$_REQUEST['id'];

$er=mysql_query('select * from levelek where id='.$_REQUEST['id']) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);

if ($aux['tulaj']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Ez nem a te leveled.']);

mysql_query('delete from levelek where id='.$_REQUEST['id']) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('delete from cimzettek where level_id='.$_REQUEST['id']) or hiba(__FILE__,__LINE__,mysql_error());
insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));kilep();
?>