<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');

$er=mysql_query('select * from bolygok where tulaj='.$uid.' order by nev limit 1') or hiba(__FILE__,__LINE__,mysql_error());
$bolygo=mysql_fetch_array($er);
?>
var sajat_nev='<?=htmlspecialchars($adataim['nev'],ENT_QUOTES);?>';
var kezdo_x='<?=$bolygo['x'];?>';
var kezdo_y='<?=$bolygo['y'];?>';
<? insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);?>