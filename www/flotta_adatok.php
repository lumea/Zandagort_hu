<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');
$default_latszik=0;

$_REQUEST['id']=(int)$_REQUEST['id'];
$res=mysql_query('select * from flottak where id='.$_REQUEST['id']) or hiba(__FILE__,__LINE__,mysql_error());
$flotta=mysql_fetch_array($res);

//en mindig lassam
if ($uid==1) $default_latszik=2;
//if ($adataim['szovetseg']==1) $default_latszik=2;

//mas hogy latja
//if ($uid==1) {$uid=12;$adataim=mysql_fetch_array(mysql_query('select * from userek where id='.$uid));$adataim['premium']=0;}

//ha nincs fog of war, akkor mindenki lassa
if ($fog_of_war==0) $default_latszik=2;

//megjegyzes: sajat reszflottam latszodjon: latszodik, mert a fog of war belekalkulalja

if ($flotta['id']==0) {
?>
/*{"letezik":0}*/
<?
kilep();
}


$szereped='tulaj';if ($flotta['kezelo']==$uid) $szereped='kezelo';
if ($flotta['kozos'] && $jogaim[5]) $szereped='kezelo';

$radarjog=$jogaim[10];
$nagyradarjog=$jogaim[11];

$latszik=$default_latszik;
if ($flotta[$szereped]==$uid) $latszik=2;
if ($latszik<2) {
	//latszik-e
	$er2=mysql_query('select lathatosag from lat_user_flotta where uid='.$uid.' and fid='.$flotta['id']);
	$aux2=mysql_fetch_array($er2);
	if ($aux2[0]) if ($aux2[0]>$latszik) $latszik=$aux2[0];
	if ($latszik<2) if ($radarjog) {//radar
		$er2=mysql_query('select lathatosag from lat_szov_flotta where szid='.$adataim['tulaj_szov'].' and fid='.$flotta['id']);
		$aux2=mysql_fetch_array($er2);
		if ($aux2[0]) if ($aux2[0]>$latszik) $latszik=$aux2[0];
	}
	//testverszoviken vegigmenni
	if ($latszik<2) if ($nagyradarjog) {//nagyradar
		$er2=mysql_query('select max(lathatosag) from lat_szov_flotta lszf, diplomacia_statuszok dsz where lszf.szid=dsz.kivel and dsz.ki='.$adataim['tulaj_szov'].' and dsz.mi='.DIPLO_TESTVER.' and lszf.fid='.$flotta['id']);
		$aux2=mysql_fetch_array($er2);
		if ($aux2[0]) if ($aux2[0]>$latszik) $latszik=$aux2[0];
	}
	//
}


if ($latszik>0) {
?>
/*{"letezik":<?=$latszik;?>,"te":<?=$uid?>,"tied":<?=($flotta[$szereped]==$uid || ($flotta['kozos'] && $jogaim[5] && $flotta['tulaj_szov']==$adataim['tulaj_szov']))?1:0;?>,"nev":"<?=addslashes($flotta['nev']);?>","esc_nev":"<?=addslashes(addslashes(htmlspecialchars($flotta['nev'])));?>","tulaj":"<?
if ($flotta['tulaj']==-1) echo 'Zandagort';
elseif ($flotta['tulaj']==0) echo ($lang_lang=='hu')?'kalózok':'pirates';
else {
	$res2=mysql_query('select nev from userek where id='.$flotta['tulaj']) or hiba(__FILE__,__LINE__,mysql_error());
	$aux2=mysql_fetch_array($res2);
	if ($aux2[0]) echo addslashes($aux2[0]);else echo '???';
}
?>","tulaj_id":<?=$flotta['tulaj'];?>,"kezelo":"<?
$res2=mysql_query('select nev from userek where id='.$flotta['kezelo']) or hiba(__FILE__,__LINE__,mysql_error());
$aux2=mysql_fetch_array($res2);
if ($aux2[0]) echo addslashes($aux2[0]);else echo '-';
?>","kezelo_id":<?=$flotta['kezelo'];
?>,"uccso_parancs_by":<?
$res2=mysql_query('select nev from userek where id='.$flotta['uccso_parancs_by']) or hiba(__FILE__,__LINE__,mysql_error());
$aux2=mysql_fetch_array($res2);
if ($aux2[0]) echo '"'.addslashes($aux2[0]).'"';else echo '"-"';
?>,"uccso_parancs_by_id":<?=$flotta['uccso_parancs_by'];
?>,"resztulajok":<?
echo '[';
$szum_egyenertek=sanitint(mysql2num('select sum(rfh.hp*h.ar) from resz_flotta_hajo rfh, hajok h where rfh.hajo_id=h.id and rfh.flotta_id='.$flotta['id']));
if ($szum_egyenertek>0) {
$res2=mysql_query('select if(u.id is null,-1,rfh.user_id),u.nev,round(sum(rfh.hp*h.ar)/'.$szum_egyenertek.'*1000) as ezrelek
from resz_flotta_hajo rfh
inner join hajok h on rfh.hajo_id=h.id
left join userek u on rfh.user_id=u.id
where rfh.flotta_id='.$flotta['id'].'
group by rfh.user_id
order by ezrelek desc, u.nev, rfh.user_id') or hiba(__FILE__,__LINE__,mysql_error());
$n=0;while($aux2=mysql_fetch_array($res2)) {
	$resztulajok[]=$aux2;
	$n++;if ($n>1) echo ',';
	echo '['.$aux2[0].',"'.addslashes($aux2[1]).'",'.$aux2[2].']';
}
}
echo ']';
?>,"x":<?
echo $flotta['x'];
?>,"y":<?
echo $flotta['y'];

?>,"vegjatek":<?
echo $vegjatek;

if ($latszik>1) {
/*********************************************** RESZLETEK ELEJE *************************************************/


if ($flotta[$szereped]==$uid || ($flotta['kozos'] && $jogaim[5] && $flotta['tulaj_szov']==$adataim['tulaj_szov'])) {/****************************************************** SAJAT ELEJE ***********************************************/

?>,"bolygo":{"id":<?
echo $flotta['bolygo'];
?>,"nev":"<?
$res2=mysql_query('select * from bolygok where id='.$flotta['bolygo']) or hiba(__FILE__,__LINE__,mysql_error());
$bolygo=mysql_fetch_array($res2);
if ($bolygo['id']) echo addslashes($bolygo['nev']);else echo '';
?>","osztaly":"<?
if ($bolygo['id']) echo $bolygo['osztaly'];else echo '';
?>"}<?
?>,"bazis_bolygo":{"id":<?
echo $flotta['bazis_bolygo'];
?>,"nev":"<?
$res2=mysql_query('select * from bolygok where id='.$flotta['bazis_bolygo']) or hiba(__FILE__,__LINE__,mysql_error());
$bazis_bolygo=mysql_fetch_array($res2);
if ($bazis_bolygo['id']) echo addslashes($bazis_bolygo['nev']);else echo '';
?>","osztaly":"<?
if ($bazis_bolygo['id']) echo $bazis_bolygo['osztaly'];else echo '';
?>"},"cel_bolygo":{"id":<?
echo $flotta['cel_bolygo'];
?>,"nev":"<?
$res2=mysql_query('select * from bolygok where id='.$flotta['cel_bolygo']) or hiba(__FILE__,__LINE__,mysql_error());
$cel_bolygo=mysql_fetch_array($res2);
if ($cel_bolygo) echo addslashes($cel_bolygo['kulso_nev']);else echo '';
?>","osztaly":"<?
if ($cel_bolygo) echo $cel_bolygo['osztaly'];else echo '';
?>"},"cel_flotta":{"id":<?
echo $flotta['cel_flotta'];
?>,"nev":"<?
$res2=mysql_query('select * from flottak where id='.$flotta['cel_flotta']) or hiba(__FILE__,__LINE__,mysql_error());
$cel_flotta=mysql_fetch_array($res2);
if ($cel_flotta['nev']) echo addslashes($cel_flotta['nev']);else echo '';
?>"},"bazis_x":<?
echo $flotta['bazis_x'];
?>,"bazis_y":<?
echo $flotta['bazis_y'];
?>,"cel_x":<?
echo $flotta['cel_x'];
?>,"cel_y":<?
echo $flotta['cel_y'];
?>,"statusz":<?
echo $flotta['statusz'];
?>,"tavolsag":<?
$tav=0;
switch($flotta['statusz']) {
	case STATUSZ_MEGY_XY:
		$tav=sqrt(pow($flotta['cel_x']-$flotta['x'],2)+pow($flotta['cel_y']-$flotta['y'],2));
	break;
	case STATUSZ_MEGY_BOLYGO:
	case STATUSZ_TAMAD_BOLYGORA:
	case STATUSZ_RAID_BOLYGORA:
		$tav=sqrt(pow($cel_bolygo['x']-$flotta['x'],2)+pow($cel_bolygo['y']-$flotta['y'],2));
	break;
	case STATUSZ_MEGY_FLOTTAHOZ:
	case STATUSZ_TAMAD_FLOTTARA:
		$tav=sqrt(pow($cel_flotta['x']-$flotta['x'],2)+pow($cel_flotta['y']-$flotta['y'],2));
	break;
	case STATUSZ_VISSZA:
		$tav=sqrt(pow($bazis_bolygo['x']-$flotta['x'],2)+pow($bazis_bolygo['y']-$flotta['y'],2));
	break;
}
echo round($tav/2);
?>,"hatralevo_ido":<?
echo ceil($tav/$flotta['sebesseg']);
?>,"tapasztalat":<?
echo $flotta['tp'];
?>,"moral":<?
echo mysql2num('select coalesce(round(sum(fh.ossz_hp*fh.moral/10)/sum(fh.ossz_hp)),0) from flotta_hajo fh, hajok h where fh.flotta_id='.$flotta['id'].' and fh.hajo_id=h.id');
?>,"hazai_palyan":<?
$res3=mysql_query('select tulaj_szov from bolygok where id='.$flotta['hexa_voronoi_bolygo_id']) or hiba(__FILE__,__LINE__,mysql_error());
$aux3=mysql_fetch_array($res3);
if ($flotta['tulaj_szov']==$aux3[0]) echo 1;else echo 0;
?>,"piros_teruleten":<?
$res3=mysql_query('select if(b.letezik=1 and b.tulaj!=0 and b.tulaj_szov!='.$flotta['tulaj_szov'].' and (coalesce(dsz.mi,0)='.DIPLO_HADI.' or dsz.mi is null) and (coalesce(u.karrier,0)!=3 or coalesce(u.speci,0)!=3),1,0)
from bolygok b
left join userek u on u.id=b.tulaj
left join diplomacia_statuszok dsz on dsz.ki='.$flotta['tulaj_szov'].' and dsz.kivel=b.tulaj_szov
where b.id='.$flotta['hexa_voronoi_bolygo_id']) or hiba(__FILE__,__LINE__,mysql_error());
$aux3=mysql_fetch_array($res3);
echo (int)$aux3[0];

?>,"kozos":<?
echo $flotta['kozos'];

//echo ',"melyik_bolygo_teruleten":"'.addslashes($aux3['nev']).'"';

?>,"flottak":<?
//szovi
echo mysql2jsonmatrix('select f.id,f.nev,if(f.tulaj='.$flotta['tulaj'].',1,0),u.id,u.nev from flottak f, userek u where f.id!='.$flotta['id'].' and f.tulaj_szov='.$flotta['tulaj_szov'].' and f.x='.$flotta['x'].' and f.y='.$flotta['y'].' and f.tulaj=u.id order by if(f.tulaj='.$flotta['tulaj'].',1,2),f.nev,f.id');
?>,"idegen_flottak":<?
//egyeb
//echo mysql2jsonmatrix('select f.id,f.nev,if(f.tulaj_szov='.$flotta['tulaj_szov'].',1,0),u.id,u.nev from flottak f, userek u where f.id!='.$flotta['id'].' and f.tulaj_szov!='.$flotta['tulaj_szov'].' and f.x='.$flotta['x'].' and f.y='.$flotta['y'].' and f.tulaj=u.id order by if(f.tulaj_szov='.$flotta['tulaj_szov'].',1,0),f.nev,f.id');
/*echo mysql2jsonmatrix('select f.id,f.nev,coalesce(dsz.mi,0),u.id,u.nev
from flottak f
inner join userek u on f.tulaj=u.id
left join diplomacia_statuszok dsz on dsz.ki='.$flotta['tulaj_szov'].' and dsz.kivel=f.tulaj_szov
where f.id!='.$flotta['id'].' and f.tulaj_szov!='.$flotta['tulaj_szov'].' and f.x='.$flotta['x'].' and f.y='.$flotta['y'].'
order by if(f.tulaj_szov='.$flotta['tulaj_szov'].',1,0),f.nev,f.id');*/
echo mysql2jsonmatrix('select f.id,f.nev,coalesce(dsz.mi,0),f.tulaj,u.nev
from flottak f
left join userek u on f.tulaj=u.id
left join diplomacia_statuszok dsz on dsz.ki='.$flotta['tulaj_szov'].' and dsz.kivel=f.tulaj_szov
where f.id!='.$flotta['id'].' and f.tulaj_szov!='.$flotta['tulaj_szov'].' and f.x='.$flotta['x'].' and f.y='.$flotta['y'].'
order by if(f.tulaj>0,1,if(f.tulaj=0,2,3)),coalesce(u.nev,""),f.tulaj,f.nev,f.id');
?>,"kovetkezo":<?
$res3=mysql_query('select id from flottak where concat(if(kezelo>0,"1","0"),nev)>"'.mysql_real_escape_string(($flotta['kezelo']>0?'1':'0').$flotta['nev']).'" and (tulaj='.$uid.' or kezelo='.$uid.') order by if(kezelo>0,1,0), nev, id limit 1') or hiba(__FILE__,__LINE__,mysql_error());
$aux3=mysql_fetch_array($res3);
if (!$aux3) {
	$res3=mysql_query('select id from flottak where (tulaj='.$uid.' or kezelo='.$uid.') order by if(kezelo>0,1,0), nev, id limit 1') or hiba(__FILE__,__LINE__,mysql_error());
	$aux3=mysql_fetch_array($res3);
}
if (!$aux3) echo 0;else echo $aux3[0];
?>,"elozo":<?
$res3=mysql_query('select id from flottak where concat(if(kezelo>0,"1","0"),nev)<"'.mysql_real_escape_string(($flotta['kezelo']>0?'1':'0').$flotta['nev']).'" and (tulaj='.$uid.' or kezelo='.$uid.') order by if(kezelo>0,1,0) desc, nev desc, id desc limit 1') or hiba(__FILE__,__LINE__,mysql_error());
$aux3=mysql_fetch_array($res3);
if (!$aux3) {
	$res3=mysql_query('select id from flottak where (tulaj='.$uid.' or kezelo='.$uid.') order by if(kezelo>0,1,0) desc, nev desc, id desc limit 1') or hiba(__FILE__,__LINE__,mysql_error());
	$aux3=mysql_fetch_array($res3);
}
if (!$aux3) echo 0;else echo $aux3[0];



}/****************************************************** SAJAT VEGE ***********************************************/

?>,"ossz_ertek":<?
$res2=mysql_query('select round(sum(ossz_hp/100*ar)),round(sum(if(hajo_id='.HAJO_TIPUS_SZONDA.',0,ossz_hp/100*ar))) from flotta_hajo fh, hajok h where fh.flotta_id='.$flotta['id'].' and fh.hajo_id=h.id') or hiba(__FILE__,__LINE__,mysql_error());
$aux2=mysql_fetch_array($res2);echo $aux2[0];
?>,"sebesseg":<?
echo $flotta['sebesseg'];
?>,"latotav":<?
echo $flotta['latotav'];
?>,"rejtes":<?
echo $flotta['rejtozes'];
?>,"hajok":<?
echo mysql2jsonmatrix('select fh.hajo_id,ceil(fh.ossz_hp/100),fh.ossz_hp,round(fh.ossz_hp*h.ar/100) from flotta_hajo fh,hajok h where fh.flotta_id='.$flotta['id'].' and fh.hajo_id=h.id order by fh.hajo_id');
?>,"reszflottahajok":<?
echo '[';
if ($szum_egyenertek>0) {
	foreach($resztulajok as $i=>$resztulaj) {
		if ($i>0) echo ',';
		echo '['.$resztulaj[0].',"'.addslashes($resztulaj[1]).'",'.$resztulaj[2].',';
		echo mysql2jsonmatrix('select rfh.hajo_id,ceil(rfh.hp/100),rfh.hp,round(rfh.hp*h.ar/100)
	from resz_flotta_hajo rfh
	inner join hajok h on rfh.hajo_id=h.id
	where rfh.flotta_id='.$flotta['id'].' and rfh.user_id='.$resztulaj[0].'
	order by rfh.hajo_id');
		echo ','.($resztulaj[0]==$uid?1:0);
		echo ']';
	}
}
echo ']';

/*********************************************** RESZLETEK VEGE *************************************************/
}
?>}*/
<?
} else {
?>
/*{"letezik":0}*/
<?
}

?>
<? insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);?>