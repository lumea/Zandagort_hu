<?
include('csatlak.php');
include('ujkuki.php');

if (premium_szint()<2) kilep();

header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['forras_id']=(int)$_REQUEST['forras_id'];
$_REQUEST['cel_nev']=sanitstr($_REQUEST['cel_nev']);
$_REQUEST['ef_id']=(int)$_REQUEST['ef_id'];
$_REQUEST['darab']=sanitint($_REQUEST['darab']);

$er=mysql_query('select * from eroforrasok where id='.$_REQUEST['ef_id'].' and szallithato=1') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
if (!$aux) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen erőforrás.']);

if ($_REQUEST['darab']<=0) kilep($lang[$lang_lang]['kisphpk']['Válassz ki legalább egy adag erőforrást.']);

$er=mysql_query('select * from bolygok where id='.$_REQUEST['forras_id'].' and letezik=1') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);$forras_bolygo=$aux;
if (!$aux) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen bolygó.']);
if ($aux['tulaj']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Nem a tied a bolygó.']);

$er=mysql_query('select * from bolygok where letezik=1 and concat(nev," (",if(y>0,concat("'.$lang[$lang_lang]['kisphpk']['D'].' ",round(y/2)),if(y<0,concat("'.$lang[$lang_lang]['kisphpk']['É'].' ",round(-y/2)),0)),", ",if(x>0,concat("'.$lang[$lang_lang]['kisphpk']['K'].' ",round(x/2)),if(x<0,concat("'.$lang[$lang_lang]['kisphpk']['Ny'].' ",round(-x/2)),0)),")")="'.$_REQUEST['cel_nev'].'"') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);$_REQUEST['cel_id']=$aux['id'];
if (!$aux) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen bolygó.']);
if ($aux['tulaj']!=$forras_bolygo['tulaj']) kilep($lang[$lang_lang]['kisphpk']['Csak saját bolygóra tudsz küldeni.']);

if ($_REQUEST['forras_id']==$_REQUEST['cel_id']) kilep($lang[$lang_lang]['kisphpk']['Csak két különböző bolygó között tudsz szállítani.']);

mysql_query('insert into cron_tabla_eroforras_transzfer (honnan_bolygo_id,hova_bolygo_id,eroforras_id,darab) values('.$_REQUEST['forras_id'].','.$_REQUEST['cel_id'].','.$_REQUEST['ef_id'].','.$_REQUEST['darab'].')') or hiba(__FILE__,__LINE__,mysql_error());

kilep();
?>