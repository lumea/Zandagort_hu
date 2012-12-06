<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['id']=(int)$_REQUEST['id'];
$szoba=mysql2row('select * from cset_szobak where id='.$_REQUEST['id'].' and tulaj='.$uid);
if (!$szoba) kilep();

$_REQUEST['kit']=(int)$_REQUEST['kit'];
mysql_query('delete from cset_szoba_meghivok where cset_szoba_id='.$szoba['id'].' and user_id='.$_REQUEST['kit']);

insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));kilep();
?>