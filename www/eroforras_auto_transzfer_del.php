<?
include('csatlak.php');
include('ujkuki.php');

if (premium_szint()<2) kilep();

header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['id']=(int)$_REQUEST['id'];

$er=mysql_query('select * from cron_tabla_eroforras_transzfer where id='.$_REQUEST['id']) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
if (!$aux) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen megbízás.']);
$er=mysql_query('select * from bolygok where id='.$aux['honnan_bolygo_id']) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
if (!$aux) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen megbízás.']);
if ($aux['tulaj']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen megbízás.']);

mysql_query('delete from cron_tabla_eroforras_transzfer where id='.$_REQUEST['id']) or hiba(__FILE__,__LINE__,mysql_error());

kilep();
?>