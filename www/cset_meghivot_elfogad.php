<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['id']=(int)$_REQUEST['id'];
$meghivo=mysql2row('select * from cset_szoba_meghivok where cset_szoba_id='.$_REQUEST['id'].' and user_id='.$uid);
if (!$meghivo) kilep();

mysql_query('insert ignore into cset_szoba_user (cset_szoba_id,user_id) values('.$meghivo['cset_szoba_id'].','.$uid.')');
mysql_query('delete from cset_szoba_meghivok where cset_szoba_id='.$meghivo['cset_szoba_id'].' and user_id='.$uid);

insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));kilep();
?>