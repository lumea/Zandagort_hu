<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if ($adataim['szovetseg']>0) {
	if ($jogaim[7]==0) kilep($lang[$lang_lang]['kisphpk']['Nincs diplomáciai jogod.']);
	$ki_vagy_id=$adataim['szovetseg'];
} else $ki_vagy_id=-$uid;

$_REQUEST['id']=(int)$_REQUEST['id'];

$er=mysql_query('select * from diplomacia_ajanlatok where id='.$_REQUEST['id']) or hiba(__FILE__,__LINE__,mysql_error());
$ajanlat=mysql_fetch_array($er);
if (!$ajanlat) kilep();
if ($ajanlat['ki']!=$ki_vagy_id) {
	if ($ki_vagy_id>0) kilep($lang[$lang_lang]['kisphpk']['Ezt az ajánlatot nem ti küldtétek.']);
	kilep($lang[$lang_lang]['kisphpk']['Ezt az ajánlatot nem te küldted.']);
}

mysql_query('delete from diplomacia_ajanlatok where id='.$ajanlat['id']) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('delete from diplomacia_szovegek where id='.$ajanlat['szoveg_id']) or hiba(__FILE__,__LINE__,mysql_error());

kilep();
?>