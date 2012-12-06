<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

//if (premium_szint()==0) kilep($lang[$lang_lang]['kisphpk']['Ehhez elő kell fizetned.']);

$_REQUEST['id']=(int)$_REQUEST['id'];

$q_er=mysql_query('select * from queue_epitkezesek where id='.$_REQUEST['id']);
$q=mysql_fetch_array($q_er);
if (!$q) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen parancs.']);

$er2=mysql_query('select * from bolygok where id='.$q['bolygo_id']) or hiba(__FILE__,__LINE__,mysql_error());
$aux2=mysql_fetch_array($er2);
if ($aux2['tulaj']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen parancs.']);

mysql_query('delete from queue_epitkezesek where id='.$q['id']) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('update queue_epitkezesek set sorszam=sorszam-1 where bolygo_id='.$q['bolygo_id'].' and sorszam>'.$q['sorszam']) or hiba(__FILE__,__LINE__,mysql_error());

kilep();
?>