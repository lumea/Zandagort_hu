<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['flotta_id']=(int)$_REQUEST['flotta_id'];

$er=mysql_query('select * from flottak where id='.$_REQUEST['flotta_id']) or hiba(__FILE__,__LINE__,mysql_error());
$flotta=mysql_fetch_array($er);
if (!$flotta) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen flotta.']);
if ($flotta['tulaj']!=$uid && $flotta['kezelo']!=$uid && ($flotta['kozos']!=1 || $jogaim[5]!=1 || $flotta['tulaj_szov']!=$adataim['tulaj_szov'])) kilep($lang[$lang_lang]['kisphpk']['Nem a tied a flotta.']);

$er_b=mysql_query('select * from bolygok where id='.$flotta['bazis_bolygo']) or hiba(__FILE__,__LINE__,mysql_error());
$aux_b=mysql_fetch_array($er_b);
if ($aux_b) {//van bazis
	if (($aux_b['x']==$flotta['x'])&&($aux_b['y']==$flotta['y'])) {//mar ott van
		$dsz=(int)mysql2num('select mi from diplomacia_statuszok where ki='.$aux_b['tulaj_szov'].' and kivel='.$flotta['tulaj_szov']);
		if ($flotta['tulaj_szov']==$aux_b['tulaj_szov'] || $dsz==DIPLO_TESTVER) {
			mysql_query('update flottak set bolygo='.$flotta['bazis_bolygo'].',statusz='.STATUSZ_ALLOMAS.' where id='.$_REQUEST['flotta_id']) or hiba(__FILE__,__LINE__,mysql_error());
		} else {
			mysql_query('update flottak set bolygo=0,statusz='.STATUSZ_ALL.' where id='.$_REQUEST['flotta_id']) or hiba(__FILE__,__LINE__,mysql_error());
		}
	} else {
		mysql_query('update flottak set bolygo=0,statusz='.STATUSZ_VISSZA.' where id='.$_REQUEST['flotta_id']) or hiba(__FILE__,__LINE__,mysql_error());
	}
} else {//nincs bazisa?
	mysql_query('update flottak set bolygo=0,statusz='.STATUSZ_ALL.' where id='.$_REQUEST['flotta_id']) or hiba(__FILE__,__LINE__,mysql_error());
}

mysql_query('update flottak set uccso_parancs_by='.$uid.' where id='.$_REQUEST['flotta_id']);
if (flotta_fejvadasz_frissites($_REQUEST['flotta_id'])) flotta_minden_frissites($_REQUEST['flotta_id']);

kilep();
?>