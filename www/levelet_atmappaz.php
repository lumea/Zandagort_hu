<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if (premium_szint()<2) kilep();

$_REQUEST['id']=(int)$_REQUEST['id'];

$er=mysql_query('select * from levelek where id='.$_REQUEST['id']) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);

if ($aux['tulaj']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Ez nem a te leveled.']);

mysql_query('update levelek set mappa="'.sanitstr($_REQUEST['mappa']).'" where id='.$_REQUEST['id']) or hiba(__FILE__,__LINE__,mysql_error());
kilep();
?>