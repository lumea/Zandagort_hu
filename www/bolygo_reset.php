<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if ($adataim['techszint']>3) kilep();

$_REQUEST['bolygo_id']=(int)$_REQUEST['bolygo_id'];

$er2=mysql_query('select * from bolygok where id='.$_REQUEST['bolygo_id']) or hiba(__FILE__,__LINE__,mysql_error());
$aux2=mysql_fetch_array($er2);
if ($aux2['tulaj']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Nem a tied a bolygó.']);

//kezdo anyahajobol ami megmaradt, visszatenni
$landolt_anyahajok=mysql2num('select db from bolygo_eroforras where bolygo_id='.$aux2['id'].' and eroforras_id='.HAJO_TIPUS_ANYA);
bolygo_reset($aux2['id'],$aux2['osztaly'],$aux2['terulet'],1);//tulajt_is!!!
mysql_query('update bolygo_eroforras set db='.$landolt_anyahajok.' where bolygo_id='.$aux2['id'].' and eroforras_id='.HAJO_TIPUS_ANYA);

kilep();
?>