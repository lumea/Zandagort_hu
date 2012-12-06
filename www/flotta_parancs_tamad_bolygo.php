<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['bolygo_id']=(int)$_REQUEST['bolygo_id'];
$_REQUEST['flotta_id']=(int)$_REQUEST['flotta_id'];

$er=mysql_query('select * from flottak where id='.$_REQUEST['flotta_id']) or hiba(__FILE__,__LINE__,mysql_error());
$flotta=mysql_fetch_array($er);
if (!$flotta) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen flotta.']);
if ($flotta['tulaj']!=$uid && $flotta['kezelo']!=$uid && ($flotta['kozos']!=1 || $jogaim[5]!=1 || $flotta['tulaj_szov']!=$adataim['tulaj_szov'])) kilep($lang[$lang_lang]['kisphpk']['Nem a tied a flotta.']);

if ($_REQUEST['bolygo_id']) {
	$er=mysql_query('select * from bolygok where id='.$_REQUEST['bolygo_id'].' and letezik=1') or hiba(__FILE__,__LINE__,mysql_error());
} else {
	$er=mysql_query('select * from bolygok where concat(kulso_nev," (",if(y>0,concat("'.$lang[$lang_lang]['kisphpk']['D'].' ",round(y/2)),if(y<0,concat("'.$lang[$lang_lang]['kisphpk']['É'].' ",round(-y/2)),0)),", ",if(x>0,concat("'.$lang[$lang_lang]['kisphpk']['K'].' ",round(x/2)),if(x<0,concat("'.$lang[$lang_lang]['kisphpk']['Ny'].' ",round(-x/2)),0)),")")="'.sanitstr($_REQUEST['nev']).'" and letezik=1');
}
$aux=mysql_fetch_array($er);
if (!$aux) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen bolygó.']);
if ($aux['tulaj_szov']==$flotta['tulaj_szov']) kilep($lang[$lang_lang]['kisphpk']['Saját és szövetséges bolygót nem támadhatsz meg.']);

mysql_query('update flottak set bolygo=0,statusz='.STATUSZ_TAMAD_BOLYGORA.',cel_bolygo='.$aux['id'].' where id='.$_REQUEST['flotta_id']) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('update flottak set uccso_parancs_by='.$uid.' where id='.$_REQUEST['flotta_id']);
if (flotta_fejvadasz_frissites($_REQUEST['flotta_id'])) flotta_minden_frissites($_REQUEST['flotta_id']);

insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));kilep();
?>