<?
include('csatlak.php');
$nem_szamit_aktivitasnak=1;include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');

$_REQUEST['felado_szuro']=(int)$_REQUEST['felado_szuro'];if (!in_array($_REQUEST['felado_szuro'],array(1,2,3))) $_REQUEST['felado_szuro']=1;
$szuro='';
if ($_REQUEST['felado_szuro']==1) $szuro.=' and felado>0';
if ($_REQUEST['felado_szuro']==2) $szuro.=' and felado=0';

$csatajelentesek_szama=mysql2num('select count(1) from csata_user where user_id='.$uid);
$olvasatlan_csatajelentesek_szama=mysql2num('select count(1) from csata_user where user_id='.$uid.' and olvasott=0');

?>
/*{"bolygok":<?
echo mysql2jsonmatrix('
select id,nev,osztaly,1 as tied,if(timestampdiff(minute,now(),moratorium_mikor_jar_le)>-5,if(timestampdiff(minute,uccso_foglalas_mikor,now())<70,0,1),0) as utik,moral from bolygok where tulaj='.$uid.' and letezik=1
union all
select id,nev,osztaly,0 as tied,if(timestampdiff(minute,now(),moratorium_mikor_jar_le)>-5,if(timestampdiff(minute,uccso_foglalas_mikor,now())<70,0,1),0) as utik,moral from bolygok where kezelo='.$uid.' and letezik=1
order by tied desc,nev,id');

?>,"flottak":<?
if ($user_beallitasok['kozos_flottak_listaban']>0) {
echo mysql2jsonmatrix('
select id,nev,1 as diplo,1 as tied,0,kozos from flottak where tulaj='.$uid.'
union all
(select f.id,f.nev,if(f.tulaj_szov='.$adataim['tulaj_szov'].',2,3+coalesce(dsz.mi,0)) as diplo,0 as tied,0,f.kozos
from flottak f
left join diplomacia_statuszok dsz on dsz.ki=f.tulaj_szov and dsz.kivel='.$adataim['tulaj_szov'].'
where f.tulaj!='.$uid.' and f.kozos=1 and f.tulaj_szov='.$adataim['tulaj_szov'].' and '.$jogaim[5].'=1)
order by tied desc,nev,id');
} else {
echo mysql2jsonmatrix('
select id,nev,1 as diplo,1 as tied,0,kozos from flottak where tulaj='.$uid.'
order by tied desc,nev,id');
}

?>,"db":<?
$er=mysql_query('select count(1) from levelek where tulaj='.$uid.' and olvasott=0') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);echo $aux[0]+$olvasatlan_csatajelentesek_szama;
?>,"db_bontas":[<?
$er=mysql_query('select sum(if(felado>0,1,0)),sum(if(felado=0,1,0)),count(1) from levelek where tulaj='.$uid.' and olvasott=0') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
echo $aux[0].','.$aux[1].','.$olvasatlan_csatajelentesek_szama;
?>],"db_ossz":<?
$er=mysql_query('select count(1) from levelek where tulaj='.$uid.$szuro) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);echo $aux[0];
?>,"db_ossz_bontas":[<?
$er=mysql_query('select sum(if(felado>0,1,0)),sum(if(felado=0,1,0)),count(1) from levelek where tulaj='.$uid) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
echo $aux[0].','.$aux[1].','.$csatajelentesek_szama;
?>],"forum_db":<?
echo (int)mysql2num('select count(1) from
szov_forum_temak t
left join szov_forum_tema_olv o on o.user_id='.$uid.' and o.tema_id=t.id
where t.szov_id='.$adataim['szovetseg'].' and (o.uccso_komment is null or o.uccso_komment<>t.uccso_komment) and (t.belso=0 or '.$jogaim[1].'=1)');
?>,"cset":<?
if ($adataim['szovetseg']>0) {
	$er=mysql_query('select id from cset_hozzaszolasok where szov_id='.$adataim['szovetseg'].' order by id desc limit 1') or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);
	if (((int)$aux[0])>$adataim['uccso_cset_id']) echo 1;else echo 0;
} else echo 0;
?>,"nagycset":<?

$csat_mapping=array(0,-1,$adataim['szovetseg'],-500,-500,-500);
if ($adataim['szovetseg']<=0) $csat_mapping[2]=-500;//nemletezo csetszoba
$r=mysql_query('select cssz.id
from cset_szobak cssz
left join cset_szoba_user csszu on csszu.cset_szoba_id=cssz.id
where cssz.tulaj='.$uid.' or csszu.user_id='.$uid.'
group by cssz.id limit 3');
$cs=0;while($aux=mysql_fetch_array($r)) {
	$cs++;
	$csat_mapping[2+$cs]=-$aux[0];
}

/*if ($_REQUEST['elso']==1) {
	$adataim['uccso_nagy_cset_id']=0;
	$adataim['uccso_angol_cset_id']=0;
	$adataim['uccso_cset_id']=0;
	$adataim['uccso_priv_cset_id_1']=0;
	$adataim['uccso_priv_cset_id_2']=0;
	$adataim['uccso_priv_cset_id_3']=0;
}*/

$van_e_olvasatlan=0;
for($csat=0;$csat<6;$csat++) if ($csat_mapping[$csat]!=-500) {
	$er=mysql_query('select id from cset_hozzaszolasok where szov_id='.$csat_mapping[$csat].' order by id desc limit 1') or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);
	$u=(int)$aux[0];
	switch($csat) {
		case 0:if ($user_beallitasok['chat_hu']) if ($u>$adataim['uccso_nagy_cset_id']) $van_e_olvasatlan=1;break;
		case 1:if ($user_beallitasok['chat_en']) if ($u>$adataim['uccso_angol_cset_id']) $van_e_olvasatlan=1;break;
		case 2:if ($u>$adataim['uccso_cset_id']) $van_e_olvasatlan=1;break;
		case 3:if ($u>$adataim['uccso_priv_cset_id_1']) $van_e_olvasatlan=1;break;
		case 4:if ($u>$adataim['uccso_priv_cset_id_2']) $van_e_olvasatlan=1;break;
		case 5:if ($u>$adataim['uccso_priv_cset_id_3']) $van_e_olvasatlan=1;break;
	}
}

echo $van_e_olvasatlan;
?>}*/
<? insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);?>