<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

//if (premium_szint()==0) kilep($lang[$lang_lang]['kisphpk']['Ehhez elő kell fizetned.']);

$_REQUEST['id']=(int)$_REQUEST['id'];
$_REQUEST['a']=(int)$_REQUEST['a'];

$q_er=mysql_query('select * from queue_epitkezesek where id='.$_REQUEST['id']);
$q=mysql_fetch_array($q_er);
if (!$q) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen parancs.']);

$er2=mysql_query('select * from bolygok where id='.$q['bolygo_id']) or hiba(__FILE__,__LINE__,mysql_error());
$aux2=mysql_fetch_array($er2);
if ($aux2['tulaj']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen parancs.']);

mysql_query('update queue_epitkezesek set aktiv='.($_REQUEST['a']?'1':'0').' where id='.$_REQUEST['id']);

kilep();
?>