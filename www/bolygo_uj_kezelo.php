<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if ($adataim['karrier']==3) if ($adataim['speci']==3) kilep($lang[$lang_lang]['kisphpk']['Fantom vagy, ezért nem mutathatod meg a bolygódat másoknak.']);

$_REQUEST['bolygo_id']=(int)$_REQUEST['bolygo_id'];
$er2=mysql_query('select * from bolygok where id='.$_REQUEST['bolygo_id']) or hiba(__FILE__,__LINE__,mysql_error());
$aux2=mysql_fetch_array($er2);
if ($aux2['tulaj']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Nem a tied a bolygó.']);

$er3=mysql_query('select * from userek where nev="'.sanitstr($_REQUEST['kezelo']).'"') or hiba(__FILE__,__LINE__,mysql_error());
$aux3=mysql_fetch_array($er3);
if (!$aux3) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen játékos.']);

if ($aux3['id']==$uid) {//sajat magad -> kezelo megszuntetese
	mysql_query('update bolygok set kezelo=0 where id='.$_REQUEST['bolygo_id']) or hiba(__FILE__,__LINE__,mysql_error());
} else {
	mysql_query('update bolygok set kezelo='.$aux3['id'].' where id='.$_REQUEST['bolygo_id']) or hiba(__FILE__,__LINE__,mysql_error());
}

kilep();
?>