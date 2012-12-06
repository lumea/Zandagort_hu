<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');
?>
/*{"nevek":<?

$radarjog=$jogaim[10];
$nagyradarjog=$jogaim[11];


if ($fog_of_war) {

if ($nagyradarjog) {
	echo mysql2jsonarray('select concat(nev," (",if(y>0,concat("'.$lang[$lang_lang]['kisphpk']['D'].' ",round(y/2)),if(y<0,concat("'.$lang[$lang_lang]['kisphpk']['É'].' ",round(-y/2)),0)),", ",if(x>0,concat("'.$lang[$lang_lang]['kisphpk']['K'].' ",round(x/2)),if(x<0,concat("'.$lang[$lang_lang]['kisphpk']['Ny'].' ",round(-x/2)),0)),")") from flottak f, (select fid,max(lathatosag) as lathatosag from
(select fid,lathatosag from lat_user_flotta where uid='.$uid.'
union all
select fid,lathatosag from lat_szov_flotta where szid='.$adataim['tulaj_szov'].'
union all
select fid,lathatosag from lat_szov_flotta lszf, diplomacia_statuszok dsz where dsz.ki='.$adataim['tulaj_szov'].' and dsz.kivel=lszf.szid and dsz.mi='.DIPLO_TESTVER.') t
group by fid) lt where f.id=lt.fid and nev like "'.sanitstr($_REQUEST['x']).'%" order by nev limit 10');
} elseif ($radarjog) {
	echo mysql2jsonarray('select concat(nev," (",if(y>0,concat("'.$lang[$lang_lang]['kisphpk']['D'].' ",round(y/2)),if(y<0,concat("'.$lang[$lang_lang]['kisphpk']['É'].' ",round(-y/2)),0)),", ",if(x>0,concat("'.$lang[$lang_lang]['kisphpk']['K'].' ",round(x/2)),if(x<0,concat("'.$lang[$lang_lang]['kisphpk']['Ny'].' ",round(-x/2)),0)),")") from flottak f, (select fid,max(lathatosag) as lathatosag from
(select fid,lathatosag from lat_user_flotta where uid='.$uid.'
union all
select fid,lathatosag from lat_szov_flotta where szid='.$adataim['tulaj_szov'].') t
group by fid) lt where f.id=lt.fid and nev like "'.sanitstr($_REQUEST['x']).'%" order by nev limit 10');
} else {
	echo mysql2jsonarray('select concat(nev," (",if(y>0,concat("'.$lang[$lang_lang]['kisphpk']['D'].' ",round(y/2)),if(y<0,concat("'.$lang[$lang_lang]['kisphpk']['É'].' ",round(-y/2)),0)),", ",if(x>0,concat("'.$lang[$lang_lang]['kisphpk']['K'].' ",round(x/2)),if(x<0,concat("'.$lang[$lang_lang]['kisphpk']['Ny'].' ",round(-x/2)),0)),")") from flottak f, lat_user_flotta lt where f.id=lt.fid and lt.uid='.$uid.' and nev like "'.sanitstr($_REQUEST['x']).'%" order by nev limit 10');
}

} else {

	echo mysql2jsonarray('select concat(nev," (",if(y>0,concat("'.$lang[$lang_lang]['kisphpk']['D'].' ",round(y/2)),if(y<0,concat("'.$lang[$lang_lang]['kisphpk']['É'].' ",round(-y/2)),0)),", ",if(x>0,concat("'.$lang[$lang_lang]['kisphpk']['K'].' ",round(x/2)),if(x<0,concat("'.$lang[$lang_lang]['kisphpk']['Ny'].' ",round(-x/2)),0)),")") from flottak f where nev like "'.sanitstr($_REQUEST['x']).'%" order by nev limit 10');

}

?>}*/
<?

?>
<? insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);?>