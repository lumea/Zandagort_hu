<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');

$_REQUEST['offset']=(int)$_REQUEST['offset'];
$_REQUEST['felado_szuro']=(int)$_REQUEST['felado_szuro'];if (!in_array($_REQUEST['felado_szuro'],array(1,2,3))) $_REQUEST['felado_szuro']=1;
$szuro='';
if ($_REQUEST['felado_szuro']==1) $szuro.=' and felado>0';//privat
if ($_REQUEST['felado_szuro']==2) $szuro.=' and felado=0';//rendszer
//csata

$spec_szuro='';
if (premium_szint()==2) {
if ($_REQUEST['felado_szuro']==1) {//szemelyes uzi specko szuroi
	//targy
	$_REQUEST['targy']=sanitstr($_REQUEST['targy']);
	if (strlen($_REQUEST['targy'])>0) $spec_szuro.=' and (targy like "%'.$_REQUEST['targy'].'%" or uzenet like "%'.$_REQUEST['targy'].'%")';
	//felado
	$_REQUEST['felado']=sanitstr($_REQUEST['felado']);
	if (strlen($_REQUEST['felado'])>0) {
		$felado_tipus=0;$felado_id=0;
		$er=mysql_query('select id from userek where nev="'.$_REQUEST['felado'].'"') or hiba(__FILE__,__LINE__,mysql_error());
		$aux=mysql_fetch_array($er);
		if ($aux) {
			$felado_tipus=CIMZETT_TIPUS_USER;
		} else {
			$er=mysql_query('select id from szovetsegek where nev="'.$_REQUEST['felado'].'"') or hiba(__FILE__,__LINE__,mysql_error());
			$aux=mysql_fetch_array($er);
			if ($aux) $felado_tipus=CIMZETT_TIPUS_SZOVETSEG;
		}
		if ($felado_tipus>0) {
			if ($felado_tipus==CIMZETT_TIPUS_SZOVETSEG) $spec_szuro.=' and cimzettek like "%,s'.$aux[0].',%"';//szovi csak cimzett lehet
			else $spec_szuro.=' and (felado='.$aux[0].' or cimzettek like "%,'.$aux[0].',%")';
		} else {
			$spec_szuro.=' and (0)';
		}
	}
	//mappa
	$_REQUEST['mappa']=sanitstr($_REQUEST['mappa']);
	if (strlen($_REQUEST['mappa'])>0) $spec_szuro.=' and mappa="'.$_REQUEST['mappa'].'"';
}
}

?>
/*{"levelek":[<?

if ($_REQUEST['felado_szuro']==3) {//csatajelentesek


//if ($uid==1 || $uid==2) {
if (false) {//zanda szemszogebol nezni, ez a vegjatekban lehet neha hasznos
echo substr(mysql2jsonmatrix('select cs.id,cs.x,cs.y,left(cs.mikor,16),cs.resztvett_egyenertek,cs.megsemmisult_egyenertek,
csf.flotta_id,csf.nev,csf.tulaj,csf.tulaj_nev,csf.tulaj_szov,csf.tulaj_szov_nev,0,"",csf.egyenertek_elotte,csf.egyenertek_utana,
if(csf.tulaj='.$uid.',1,if(csf.tulaj_szov='.$adataim['tulaj_szov'].',2,3)),csataim.olvasott
from (select id as csata_id,1 as olvasott from '.$database_mmog_nemlog.'.hist_csatak where zanda=1
order by id desc limit '.$_REQUEST['offset'].',10) csataim
inner join '.$database_mmog_nemlog.'.hist_csatak cs
inner join '.$database_mmog_nemlog.'.hist_csata_flotta csf
where csataim.csata_id=cs.id and csataim.csata_id=csf.csata_id
order by cs.id desc,if(csf.tulaj='.$uid.',1,2),csf.flotta_id'),1,-1);
} else {
echo substr(mysql2jsonmatrix('select cs.id,cs.x,cs.y,left(cs.mikor,16),cs.resztvett_egyenertek,cs.megsemmisult_egyenertek
,csf.flotta_id,csf.nev,csf.tulaj,csf.tulaj_nev,csf.tulaj_szov,csf.tulaj_szov_nev,0,"",csf.egyenertek_elotte,csf.egyenertek_utana
,if(csf.tulaj='.$uid.',1,if(csf.tulaj_szov='.$adataim['tulaj_szov'].',2,if(csf.tulaj=0,0,if(csf.tulaj<0,-1,3+coalesce(ds.mi,0))))),csataim.olvasott
,csf.iranyito,csf.iranyito_nev
from (select csata_id,olvasott from csata_user where user_id='.$uid.'
order by csata_id desc limit '.$_REQUEST['offset'].',10) csataim
inner join '.$database_mmog_nemlog.'.hist_csatak cs
inner join '.$database_mmog_nemlog.'.hist_csata_flotta csf
left join diplomacia_statuszok ds on ds.ki='.$adataim['tulaj_szov'].' and ds.kivel=csf.tulaj_szov
where csataim.csata_id=cs.id and csataim.csata_id=csf.csata_id
order by cs.id desc,if(csf.tulaj='.$uid.',1,2),csf.flotta_id'),1,-1);
mysql_query('update csata_user csu, (select csata_id from csata_user where user_id='.$uid.' order by csata_id desc limit '.$_REQUEST['offset'].',10) csataim
set csu.olvasott=1
where csu.csata_id=csataim.csata_id and csu.user_id='.$uid);
}

} elseif ($_REQUEST['felado_szuro']==2) {//rendszeruzik
$er=mysql_query('select * from levelek where tulaj='.$uid.$szuro.$spec_szuro.' order by ido desc,id desc limit '.$_REQUEST['offset'].',10') or hiba(__FILE__,__LINE__,mysql_error());
$i=0;while($level=mysql_fetch_array($er)) {
mysql_query('update levelek set olvasott=1 where id='.$level['id']) or hiba(__FILE__,__LINE__,mysql_error());
if ($i) echo ',';$i++;
?>[<?=$level['id'];?>,"<?=str_replace(' ','&nbsp;',substr($level['ido'],0,-3));?>",<?=json_encode($level['targy']);?>,<?=json_encode(nl2br($level['uzenet']));?>,<?=$level['olvasott'];?>]<?
}
} else {//szemelyes uzik
$er=mysql_query('select * from levelek where tulaj='.$uid.$szuro.$spec_szuro.' order by ido desc,id desc limit '.$_REQUEST['offset'].',10') or hiba(__FILE__,__LINE__,mysql_error());
$i=0;while($level=mysql_fetch_array($er)) {
if ($i) echo ',';$i++;
?>[<?=$level['id'];?>,"<?=str_replace(' ','&nbsp;',substr($level['ido'],0,-3));?>",<?
if ($level['felado']==$uid) {
	$er2=mysql_query('select u.id,u.nev from cimzettek c, userek u where c.level_id='.$level['id'].' and c.cimzett_tipus='.CIMZETT_TIPUS_USER.' and c.cimzett_id=u.id order by u.nev') or hiba(__FILE__,__LINE__,mysql_error());
	$aux2=mysql_fetch_array($er2);
	if ($aux2) echo $aux2[0];
	else {
		$er2=mysql_query('select sz.id,sz.nev from cimzettek c, szovetsegek sz where c.level_id='.$level['id'].' and c.cimzett_tipus='.CIMZETT_TIPUS_SZOVETSEG.' and c.cimzett_id=sz.id order by sz.nev') or hiba(__FILE__,__LINE__,mysql_error());
		$aux2=mysql_fetch_array($er2);
		echo $aux2[0];
	}
} else echo $level['felado'];
?>,<?
if ($level['felado']==$uid) {
	echo json_encode($aux2[1].((mysql_num_rows($er2)>1)?'...':''));
} else {
	if ($level['felado']) {
		$er2=mysql_query('select nev from userek where id='.$level['felado']) or hiba(__FILE__,__LINE__,mysql_error());
		$aux2=mysql_fetch_array($er2);
		echo json_encode($aux2['nev']);
	} else echo ($lang_lang=='hu')?'"rendszer"':'"system"';
}
?>,<?=json_encode($level['targy']);?>,<?=$level['olvasott'];?>,<? if ($level['felado']==$uid) echo '1';else echo '0';?>]<?
}
}

$csatajelentesek_szama=mysql2num('select count(1) from csata_user where user_id='.$uid);
$olvasatlan_csatajelentesek_szama=mysql2num('select count(1) from csata_user where user_id='.$uid.' and olvasott=0');

?>],"olvasatlan_levelek_szama":<?
$er=mysql_query('select count(1) from levelek where tulaj='.$uid.$spec_szuro.' and olvasott=0') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);echo $aux[0]+$olvasatlan_csatajelentesek_szama;
?>,"olvasatlan_levelek_szama_bontas":[<?
$er=mysql_query('select coalesce(sum(if(felado>0,1,0)),0),coalesce(sum(if(felado=0,1,0)),0),count(1) from levelek where tulaj='.$uid.$spec_szuro.' and olvasott=0') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
echo $aux[0].','.$aux[1].','.$olvasatlan_csatajelentesek_szama;
?>],"levelek_szama":<?
if ($_REQUEST['felado_szuro']==3) {//csatajelentesek
	echo $csatajelentesek_szama;
} else {
	echo mysql2num('select count(1) from levelek where tulaj='.$uid.$szuro.$spec_szuro);
}
?>,"levelek_szama_bontas":[<?
$er=mysql_query('select coalesce(sum(if(felado>0,1,0)),0),coalesce(sum(if(felado=0,1,0)),0),count(1) from levelek where tulaj='.$uid.$spec_szuro) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
echo $aux[0].','.$aux[1].','.$csatajelentesek_szama;
?>]}*/
<? insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);?>