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

if ($fog_of_war) {

$radarjog=$jogaim[10];
$nagyradarjog=$jogaim[11];
if ($nagyradarjog) {
	$er=mysql_query('select f.* from flottak f, (select fid,max(lathatosag) as lathatosag from
(select fid,lathatosag from lat_user_flotta where uid='.$uid.'
union all
select fid,lathatosag from lat_szov_flotta where szid='.$adataim['tulaj_szov'].'
union all
select fid,lathatosag from lat_szov_flotta lszf, diplomacia_statuszok dsz where dsz.ki='.$adataim['tulaj_szov'].' and dsz.kivel=lszf.szid and dsz.mi='.DIPLO_TESTVER.') t
group by fid) lt where f.id=lt.fid and concat(nev," (",if(y>0,concat("'.$lang[$lang_lang]['kisphpk']['D'].' ",round(y/2)),if(y<0,concat("'.$lang[$lang_lang]['kisphpk']['É'].' ",round(-y/2)),0)),", ",if(x>0,concat("'.$lang[$lang_lang]['kisphpk']['K'].' ",round(x/2)),if(x<0,concat("'.$lang[$lang_lang]['kisphpk']['Ny'].' ",round(-x/2)),0)),")")="'.sanitstr($_REQUEST['nev']).'"');
} elseif ($radarjog) {
	$er=mysql_query('select f.* from flottak f, (select fid,max(lathatosag) as lathatosag from
(select fid,lathatosag from lat_user_flotta where uid='.$uid.'
union all
select fid,lathatosag from lat_szov_flotta where szid='.$adataim['tulaj_szov'].') t
group by fid) lt where f.id=lt.fid and concat(nev," (",if(y>0,concat("'.$lang[$lang_lang]['kisphpk']['D'].' ",round(y/2)),if(y<0,concat("'.$lang[$lang_lang]['kisphpk']['É'].' ",round(-y/2)),0)),", ",if(x>0,concat("'.$lang[$lang_lang]['kisphpk']['K'].' ",round(x/2)),if(x<0,concat("'.$lang[$lang_lang]['kisphpk']['Ny'].' ",round(-x/2)),0)),")")="'.sanitstr($_REQUEST['nev']).'"');
} else {
	$er=mysql_query('select f.* from flottak f, lat_user_flotta lt where f.id=lt.fid and lt.uid='.$uid.' and concat(nev," (",if(y>0,concat("'.$lang[$lang_lang]['kisphpk']['D'].' ",round(y/2)),if(y<0,concat("'.$lang[$lang_lang]['kisphpk']['É'].' ",round(-y/2)),0)),", ",if(x>0,concat("'.$lang[$lang_lang]['kisphpk']['K'].' ",round(x/2)),if(x<0,concat("'.$lang[$lang_lang]['kisphpk']['Ny'].' ",round(-x/2)),0)),")")="'.sanitstr($_REQUEST['nev']).'"');
}

} else {
	$er=mysql_query('select f.* from flottak f where concat(nev," (",if(y>0,concat("'.$lang[$lang_lang]['kisphpk']['D'].' ",round(y/2)),if(y<0,concat("'.$lang[$lang_lang]['kisphpk']['É'].' ",round(-y/2)),0)),", ",if(x>0,concat("'.$lang[$lang_lang]['kisphpk']['K'].' ",round(x/2)),if(x<0,concat("'.$lang[$lang_lang]['kisphpk']['Ny'].' ",round(-x/2)),0)),")")="'.sanitstr($_REQUEST['nev']).'"');
}

$aux=mysql_fetch_array($er);
if (!$aux) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen flotta.']);
if ($aux['tulaj_szov']==$flotta['tulaj_szov']) kilep($lang[$lang_lang]['kisphpk']['Saját és szövetséges flottát nem támadhatsz meg.']);

mysql_query('update flottak set bolygo=0,statusz='.STATUSZ_TAMAD_FLOTTARA.',cel_flotta='.$aux['id'].' where id='.$_REQUEST['flotta_id']) or hiba(__FILE__,__LINE__,mysql_error());
mysql_query('update flottak set uccso_parancs_by='.$uid.' where id='.$_REQUEST['flotta_id']);
if (flotta_fejvadasz_frissites($_REQUEST['flotta_id'])) flotta_minden_frissites($_REQUEST['flotta_id']);

kilep();
?>