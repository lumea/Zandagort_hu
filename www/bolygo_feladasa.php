<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$er2=mysql_query('select count(1) from bolygok where tulaj='.$uid) or hiba(__FILE__,__LINE__,mysql_error());
$aux2=mysql_fetch_array($er2);
if ($aux2[0]<2) kilep($lang[$lang_lang]['kisphpk']['Legalább egy bolygót meg kell tartanod.']);

$_REQUEST['bolygo_id']=(int)$_REQUEST['bolygo_id'];

$er2=mysql_query('select * from bolygok where id='.$_REQUEST['bolygo_id']) or hiba(__FILE__,__LINE__,mysql_error());
$cel_bolygo=mysql_fetch_array($er2);
if ($cel_bolygo['tulaj']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Nem a tied a bolygó.']);

if ($cel_bolygo['moral']<100) kilep($lang[$lang_lang]['kisphpk']['Csak 100% morálnál lehet bolygót eldobni.']);


$veszteseg_szazalek=$veszteseg_tablazat_a_vedelmi_pont_fuggvenyeben[floor($cel_bolygo['vedelmi_bonusz']/200)];
if ($cel_bolygo['vedelmi_bonusz']>=800) $veszteseg_szazalek=1;//foglalas ellen vedett bolygo eldobasa -> reset

mysql_query('update bolygo_eroforras be, eroforrasok e set be.db=0 where be.bolygo_id='.$cel_bolygo['id'].' and be.eroforras_id=e.id and e.tipus=3');
mysql_query('update bolygo_eroforras be, eroforrasok e set be.db=round(be.db*'.(1-$veszteseg_szazalek).') where be.bolygo_id='.$cel_bolygo['id'].' and be.eroforras_id=e.id and e.raktarozhato=1');
mysql_query('update bolygo_gyar bgy set bgy.db=round(bgy.db*'.(1-$veszteseg_szazalek).') where bgy.bolygo_id='.$cel_bolygo['id']);
mysql_query('update bolygo_gyar set aktiv_db=least(db,aktiv_db) where bolygo_id='.$cel_bolygo['id']);
//bontasi/epitesi listat is leosztani!!! -> trukkozes ellen
mysql_query('update cron_tabla set darab=floor(darab*'.(1-$veszteseg_szazalek).') where bolygo_id='.$cel_bolygo['id']);
bgye_frissites($cel_bolygo['id']);

insert_into_bolygo_transzfer_log($cel_bolygo['id'],$cel_bolygo['uccso_emberi_tulaj'],$cel_bolygo['uccso_emberi_tulaj_szov'],$cel_bolygo['tulaj'],$cel_bolygo['tulaj_szov'],0,0,0,$cel_bolygo['pontertek'],round((1-$veszteseg_szazalek)*$cel_bolygo['pontertek']),round($veszteseg_szazalek*$cel_bolygo['pontertek']));

mysql_query('update bolygok set tulaj=0,tulaj_szov=0,kezelo=0,fobolygo=0 where id='.$_REQUEST['bolygo_id']) or hiba(__FILE__,__LINE__,mysql_error());
bolygo_tulaj_valtozas($_REQUEST['bolygo_id'],$uid,0,$adataim['tulaj_szov'],0);

kilep();
?>