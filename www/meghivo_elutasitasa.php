<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['id']=(int)$_REQUEST['id'];
$er=mysql_query('select * from szovetsegek where id='.$_REQUEST['id']) or hiba(__FILE__,__LINE__,mysql_error());
$szovetseg=mysql_fetch_array($er);
if (!$szovetseg) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen szövetség.']);

$er=mysql_query('select * from szovetseg_meghivok where hova='.$szovetseg['id'].' and kit='.$uid) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
if (!$aux) kilep($lang[$lang_lang]['kisphpk']['Nincs meghívód ebbe a szövetségbe.']);

mysql_query('delete from szovetseg_meghivok where kit='.$uid.' and hova='.$szovetseg['id']) or hiba(__FILE__,__LINE__,mysql_error());

kilep();
?>