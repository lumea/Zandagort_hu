<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['id']=(int)$_REQUEST['id'];
$szoba=mysql2row('select * from cset_szobak where id='.$_REQUEST['id'].' and tulaj='.$uid);
if (!$szoba) kilep();

$meghivott=mysql2row('select * from userek where nev="'.sanitstr($_REQUEST['kit']).'"');
if (!$meghivott) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen játékos.']);
if ($meghivott['id']==$uid) kilep();

mysql_query('insert ignore into cset_szoba_meghivok (cset_szoba_id,user_id) values('.$szoba['id'].','.$meghivott['id'].')');

insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));kilep();
?>