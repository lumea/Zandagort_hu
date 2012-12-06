<?
include('csatlak.php');
include('ujkuki.php');

if (premium_szint()<2) kilep();

header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['forras_id']=(int)$_REQUEST['forras_id'];
$_REQUEST['ef_id']=(int)$_REQUEST['ef_id'];
$_REQUEST['darab']=sanitint($_REQUEST['darab']);

if ($adataim['karrier']==1 && $adataim['speci']==2) {//kereskedo
	$_REQUEST['regio_slot']=(int)$_REQUEST['regio_slot'];
	if ($_REQUEST['regio_slot']<1) $_REQUEST['regio_slot']=1;
	if ($_REQUEST['regio_slot']>2) $_REQUEST['regio_slot']=2;
} else {//nem kereskedo
	$_REQUEST['regio_slot']=1;
}


$er=mysql_query('select * from eroforrasok where id='.$_REQUEST['ef_id'].' and tozsdezheto=1') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
if (!$aux) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen erőforrás.']);

if ($_REQUEST['darab']<=0) kilep($lang[$lang_lang]['kisphpk']['Válassz ki legalább egy adag erőforrást.']);

$er=mysql_query('select * from bolygok where id='.$_REQUEST['forras_id'].' and letezik=1') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);$forras_bolygo=$aux;
if (!$aux) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen bolygó.']);
if ($aux['tulaj']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Nem a tied a bolygó.']);

mysql_query('insert into cron_tabla_eroforras_transzfer (honnan_bolygo_id,hova_bolygo_id,eroforras_id,darab,regio_slot) values('.$_REQUEST['forras_id'].',0,'.$_REQUEST['ef_id'].','.$_REQUEST['darab'].','.$_REQUEST['regio_slot'].')');

kilep();
?>