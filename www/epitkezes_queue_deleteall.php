<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

//if (premium_szint()==0) kilep($lang[$lang_lang]['kisphpk']['Ehhez elő kell fizetned.']);

$_REQUEST['bolygo_id']=(int)$_REQUEST['bolygo_id'];

$er2=mysql_query('select * from bolygok where id='.$_REQUEST['bolygo_id']) or hiba(__FILE__,__LINE__,mysql_error());
$aux2=mysql_fetch_array($er2);
if ($aux2['tulaj']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Nem a tied a bolygó.']);

mysql_query('delete from queue_epitkezesek where bolygo_id='.$_REQUEST['bolygo_id']) or hiba(__FILE__,__LINE__,mysql_error());

kilep();
?>