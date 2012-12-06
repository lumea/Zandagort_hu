<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

//if (premium_szint()==0) kilep($lang[$lang_lang]['kisphpk']['Ehhez elő kell fizetned.']);

$_REQUEST['id']=(int)$_REQUEST['id'];
$_REQUEST['hova']=(int)$_REQUEST['hova'];

$q_er=mysql_query('select * from queue_epitkezesek where id='.$_REQUEST['id']);
$q=mysql_fetch_array($q_er);
if (!$q) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen parancs.']);

$er2=mysql_query('select * from bolygok where id='.$q['bolygo_id']) or hiba(__FILE__,__LINE__,mysql_error());
$aux2=mysql_fetch_array($er2);
if ($aux2['tulaj']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen parancs.']);


//LOCK
mysql_query('lock tables queue_epitkezesek qe write, queue_epitkezesek');
//
if ($_REQUEST['hova']>0) {
	$er2=mysql_query('select * from queue_epitkezesek where bolygo_id='.$q['bolygo_id'].' and sorszam>'.$q['sorszam'].' order by sorszam limit 1') or hiba(__FILE__,__LINE__,mysql_error());
} else {
	$er2=mysql_query('select * from queue_epitkezesek where bolygo_id='.$q['bolygo_id'].' and sorszam<'.$q['sorszam'].' order by sorszam desc limit 1') or hiba(__FILE__,__LINE__,mysql_error());
}
$aux2=mysql_fetch_array($er2);
if ($aux2) {
	mysql_query('update queue_epitkezesek qe set qe.sorszam='.$q['sorszam'].' where qe.id='.$aux2['id']);
	mysql_query('update queue_epitkezesek qe set qe.sorszam='.$aux2['sorszam'].' where qe.id='.$q['id']);
}
//UNLOCK
mysql_query('unlock tables');
//


insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));kilep();
?>