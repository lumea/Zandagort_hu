<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');

$_REQUEST['id']=(int)$_REQUEST['id'];

$er=mysql_query('select id from levelek where felado>0 and tulaj='.$uid.' and id>'.$_REQUEST['id'].' order by ido,id') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
$aux[0]=(int)$aux[0];

if ($aux[0]==0) {
$er=mysql_query('select id from levelek where felado>0 and tulaj='.$uid.' and id<'.$_REQUEST['id'].' order by ido desc,id desc') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
$aux[0]=(int)$aux[0];
}
?>
/*{"id":<?=$aux[0];?>}*/
<? insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);?>